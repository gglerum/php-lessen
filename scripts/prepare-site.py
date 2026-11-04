#!/usr/bin/env python3
"""Bouwt _site_src/: een kopie van de repo met Jekyll front matter op elke .md,
zodat de bron-Markdown schoon blijft. Navigatie volgt de mapstructuur:
Part > week-map > losse pagina's (just-the-docs ondersteunt 3 niveaus)."""
import json
import re
import shutil
import subprocess
import posixpath
import sys
from pathlib import Path
from urllib.parse import quote, unquote

ROOT = Path(__file__).resolve().parent.parent
OUT = ROOT / "_site_src"
SKIP_DIRS = {".git", ".github", ".vscode", ".devcontainer", ".claude", "_archive",
             "_site_src", "_site", "scripts", "vendor", "node_modules", ".idea"}
SKIP_FILES = {"CLAUDE.md", ".env", "composer.lock", ".gitignore", "Gemfile.lock"}


def tracked_files() -> list:
    """Alleen getrackte bestanden, zodat lokale work-in-progress niet meegaat."""
    try:
        out = subprocess.run(["git", "ls-files", "-z"], cwd=ROOT, check=True,
                             capture_output=True, text=True, encoding="utf-8").stdout
        paths = [ROOT / n for n in out.split(chr(0)) if n]
    except Exception:
        paths = list(ROOT.rglob("*"))
    return [p for p in paths if p.is_file()
            and not (set(p.relative_to(ROOT).parts[:-1]) & SKIP_DIRS)
            and p.name not in SKIP_FILES]


def heading(path: Path, fallback: str) -> str:
    """Eerste '# titel' buiten codeblokken."""
    in_code = False
    for line in path.read_text(encoding="utf-8", errors="replace").splitlines():
        if line.lstrip().startswith(("```", "~~~")):
            in_code = not in_code
            continue
        m = None if in_code else re.match(r"#\s+(.+?)\s*#*$", line)
        if m:
            return re.sub(r"[`*]", "", m.group(1)).strip()
    return fallback


def pretty(stem: str) -> str:
    return stem.replace("_", " ").replace("-", " ").strip().capitalize()


def order(name: str, default: int = 99) -> int:
    m = re.match(r"(?:Part\s+)?(\d+)", name)
    return int(m.group(1)) if m else default


def fm(d: dict) -> str:
    lines = ["---"] + [f"{k}: {json.dumps(v, ensure_ascii=False)}" for k, v in d.items()]
    return "\n".join(lines) + "\n---\n\n"


CALLOUT_START = re.compile(r"(let op|\*\*hint:?\*\*)", re.IGNORECASE)


def add_callouts(body: str) -> str:
    """Zet alinea's die met 'Let op' / 'LETOP' / '**Hint:**' beginnen om in
    callout-blokken (kramdown: {: .class } onder de alinea). Codeblokken blijven
    ongemoeid; de bron-Markdown verandert niet."""
    out, para, in_code = [], [], False

    def flush() -> None:
        if para:
            text = "\n".join(para)
            first = para[0]
            # geen lijsten, tabellen of citaten in dezelfde alinea
            plain = not any(l.startswith((" ", "\t", ">", "|", "- ", "* ", "#"))
                            or re.match(r"\d+\. ", l) for l in para)
            # een alinea die op ':' eindigt leidt een codeblok in: geen callout
            if plain and not para[-1].rstrip().endswith(":") and re.match(r"(let\s?op|\*\*hint:?\*\*)", first, re.I):
                cls = "callout-hint" if first.lower().startswith("**hint") else "callout-note"
                text += "\n{: ." + cls + " }"
            out.append(text)
            para.clear()

    for line in body.split("\n"):
        if line.lstrip().startswith(("```", "~~~")):
            flush()
            if not in_code:  # taalnaam in kleine letters: Rouge herkent 'PHP' niet altijd
                line = re.sub(r"^(\s*(?:```|~~~))(\w+)", lambda m: m.group(1) + m.group(2).lower(), line)
            in_code = not in_code
            out.append(line)
        elif in_code:
            out.append(line)
        elif line.strip() == "":
            flush()
            out.append(line)
        else:
            para.append(line)
    flush()
    return "\n".join(out)


REPO_URL = "https://github.com/gglerum/php-lessen"
LINK = re.compile(r"\]\(((?:[^()\s]|\([^()]*\))+)\)")


def rewrite_links(body: str, rel: Path, known: set, dirs: set) -> str:
    """Herschrijft relatieve links (buiten codeblokken):
    - .md  -> .html (README.md -> map), relatief gehouden zodat baseurl niet uitmaakt
    - .html / afbeeldingen die op de site staan -> ongewijzigd
    - alles anders (php, drawio, mappen zonder pagina) -> de bron op GitHub,
      zodat code getoond wordt in plaats van gedownload."""
    base = rel.parent.as_posix()

    def fix(m: re.Match) -> str:
        url = m.group(1)
        if re.match(r"([a-z][a-z0-9+.-]*:|#|/)", url, re.I):
            return m.group(0)
        path, sep, frag = url.partition("#")
        target = posixpath.normpath(posixpath.join(base, unquote(path))) if path else ""
        if not target or target.startswith(".."):
            return m.group(0)
        if target in dirs or target == ".":
            return m.group(0)  # map met (eventueel gegenereerde) indexpagina
        if target.endswith(".md") and target in known:
            name = posixpath.basename(target)
            if name == "README.md":  # wordt index.html van de map
                out = posixpath.relpath(posixpath.dirname(target) or ".", base) + "/"
            else:
                out = posixpath.relpath(target, base)[:-3] + ".html"
            return "](" + quote(out, safe="/.&()") + (sep + frag) + ")"
        if target.endswith((".html", ".png", ".jpg", ".svg", ".gif")) and target in known:
            return m.group(0)
        kind = "tree" if (path.endswith("/") or not posixpath.splitext(target)[1]) and target not in known else "blob"
        return "](" + REPO_URL + "/" + kind + "/main/" + quote(target, safe="/&()") + (sep + frag) + ")"

    parts = re.split(r"(^[ \t]*(?:```|~~~)[^\n]*\n.*?^[ \t]*(?:```|~~~)[ \t]*$)", body,
                     flags=re.M | re.S)
    return "".join(x if i % 2 else LINK.sub(fix, x) for i, x in enumerate(parts))


def main() -> None:
    if OUT.exists():
        shutil.rmtree(OUT)
    files = tracked_files()
    for p in files:
        dest = OUT / p.relative_to(ROOT)
        dest.parent.mkdir(parents=True, exist_ok=True)
        shutil.copy2(p, dest)

    mds = [p for p in files if p.suffix == ".md"]
    dirs = set()  # alle mappen met markdown erin (ook indirect)
    for p in mds:
        d = p.parent
        while d != ROOT:
            dirs.add(d)
            d = d.parent

    def chain(d: Path) -> list:  # [Part, week, ...] van boven naar beneden
        parts = []
        while d != ROOT:
            parts.append(d.name)
            d = d.parent
        return list(reversed(parts))

    def dir_meta(d: Path) -> dict:
        c = chain(d)
        meta = {"title": d.name, "nav_order": order(d.name), "has_children": True}
        if len(c) == 2:
            meta["parent"] = c[0]
        elif len(c) >= 3:
            meta["parent"], meta["grand_parent"] = c[-2], c[-3]
        return meta

    known = {p.relative_to(ROOT).as_posix() for p in files}
    dirs_rel = {d.relative_to(ROOT).as_posix() for d in dirs}
    for d in dirs:  # mappen zonder README krijgen een eigen indexpagina
        if not (d / "README.md").exists():
            (OUT / d.relative_to(ROOT) / "index.md").write_text(fm(dir_meta(d)), encoding="utf-8")

    for p in mds:
        rel = p.relative_to(ROOT)
        dest = OUT / rel
        body = dest.read_text(encoding="utf-8", errors="replace")
        if p.parent == ROOT:
            meta = {"title": heading(p, p.stem), "nav_order": 0 if p.name == "README.md" else 90}
        elif p.name == "README.md":
            meta = dir_meta(p.parent)
            if len(chain(p.parent)) >= 3:
                meta.pop("has_children")  # niveau 3 is het diepste
        else:
            c = chain(p.parent)
            meta = {"title": heading(p, pretty(p.stem)), "nav_order": 10}
            if len(c) == 1:
                meta["parent"] = c[0]
            else:
                meta["parent"], meta["grand_parent"] = c[-1], c[-2]
        if "example" in rel.parts:  # voorbeeld-readme's niet in het menu
            for k in ("parent", "grand_parent", "has_children"):
                meta.pop(k, None)
            meta["nav_exclude"] = True
        body = add_callouts(body)
        body = rewrite_links(body, rel, known, dirs_rel)
        if p.name == "README.md":
            dest.unlink()
            dest = dest.with_name("index.md")
        dest.write_text(fm(meta) + body, encoding="utf-8")

    print(f"{len(files)} bestanden, {len(mds)} markdown-pagina's -> {OUT}")


if __name__ == "__main__":
    sys.exit(main())
