#!/usr/bin/env python3
"""Bouwt _site_src/: een kopie van de repo met Jekyll front matter op elke .md,
zodat de bron-Markdown schoon blijft. Navigatie volgt de mapstructuur:
Part > week-map > losse pagina's (just-the-docs ondersteunt 3 niveaus)."""
import json
import re
import shutil
import subprocess
import sys
from pathlib import Path

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
        # README.md -> index.md: jekyll-readme-index maakte geen startpagina aan
        body = re.sub(r"\]\(([^)]*?)README\.md([#)][^)]*|)\)",
                      lambda m: "](" + m.group(1) + "index.md" + m.group(2) + ")", body)
        if p.name == "README.md":
            dest.unlink()
            dest = dest.with_name("index.md")
        dest.write_text(fm(meta) + body, encoding="utf-8")

    print(f"{len(files)} bestanden, {len(mds)} markdown-pagina's -> {OUT}")


if __name__ == "__main__":
    sys.exit(main())
