# VS Code Mastery for PHP Development

> **📍 Learning Path**: Step 3 of 6 → [Step 4: Environment Setup](README.md#🛠️-development-environment-setup) | [Back to Learning Path](README.md#🎯-your-learning-path)

VS Code's power lies not in its features, but in how efficiently you can use them. Mastering key workflows and shortcuts transforms you from a casual user into a productive professional developer.

## 🎯 What You'll Master

By mastering these VS Code fundamentals, you'll:

- **Code faster** with keyboard shortcuts and intelligent features
- **Maintain consistent style** with automated formatting
- **Navigate large projects** effortlessly
- **Recover lost work** using built-in history features
- **Debug efficiently** with integrated tools

These skills compound over time—small efficiency gains become massive productivity advantages.

## 🎨 Professional Code Formatting

### PSR-12 Standard Compliance
The PHP extension automatically enforces PSR-12 standards—the industry-standard code style for PHP professionals.

**Manual Formatting**:
- Right-click in any PHP file → "Format Document"
- Keyboard shortcut: `Shift + Alt + F`

### Automated Formatting Setup
Configure VS Code to format your code automatically, eliminating manual formatting tasks:

**Settings Configuration**:
1. Navigate to: `File → Preferences → Settings`
2. Go to: `Text Editor → Formatting`
3. Enable these options:
   - ✅ **Format On Save** - Formats code when you save files
   - ✅ **Format On Paste** - Formats pasted code automatically
   - ⚠️ **Format On Type** - Optional: formats as you type (can be distracting for beginners)

**Professional Impact**: Automated formatting ensures your code always meets professional standards without conscious effort, letting you focus on logic instead of style.

## 🧠 Intelligent Code Completion

### IntelliSense Power
VS Code's IntelliSense provides context-aware code suggestions that prevent errors and speed up development.

**Try This Right Now**:
1. Create a new PHP file in VS Code
2. Type `<?php` and press Enter
3. Type `arr` and watch IntelliSense suggest array functions
4. Select `array_map` and see how VS Code shows you the function signature
5. Type `str` and explore string function suggestions

**Manual Trigger**: Press `Ctrl + Space` anywhere to invoke IntelliSense suggestions.

**Pro Tips**:
- IntelliSense learns from your code and improves suggestions over time
- Use arrow keys to navigate suggestions, Enter to accept
- Esc to dismiss suggestions without selecting

### Advanced Code Navigation

**Go to Definition**: `F12` - Jump directly to function/class definitions
**Peek Definition**: `Alt + F12` - See definitions without leaving current file
**Go to Symbol**: `Ctrl + Shift + O` - Quick navigation within current file
**Quick Open**: `Ctrl + P` - Fast file switching by name

## 🕰️ Local History - Your Safety Net

Git is essential for version control, but Local History saves you from daily mishaps and provides granular recovery options.

### Timeline Feature
**Access**: Right-click any file → "Open Timeline"

**Capabilities**:
- **View**: See every save point with timestamps
- **Compare**: Diff between any two versions
- **Restore**: Recover specific versions of your code

**Professional Use Cases**:
- Recovering accidentally deleted code sections
- Comparing different implementation approaches
- Undoing changes beyond `Ctrl + Z` limits
- Reviewing your coding progress over time

### Deleted File Recovery
When `Ctrl + Z` can't help and Git doesn't have your latest changes:

1. Open Command Palette: `Ctrl + Shift + P`
2. Search: "Local History"
3. Select: "Local History: Find Entry to Restore"
4. Choose your deleted file from the timeline

**Critical Insight**: Local History works independently of Git, creating snapshots with every save. It's your last line of defense against data loss.

## 🔧 Advanced Productivity Features

### Multi-Cursor Editing
**Add cursor**: `Alt + Click` at multiple locations
**Select all occurrences**: `Ctrl + Shift + L`
**Select next occurrence**: `Ctrl + D`

**Try This Exercise**:
1. Create a new PHP file
2. Write a few lines with the word `user` in different places
3. Double-click on one instance of `user`
4. Press `Ctrl + D` to select the next occurrence
5. Keep pressing `Ctrl + D` to select more instances
6. Type `customer` to replace all selected instances simultaneously

**Real-World Use**: Perfect for renaming variables, changing class names, or updating multiple similar lines at once.

### Command Palette Mastery
**Access**: `Ctrl + Shift + P`

Essential commands for PHP developers:
- "PHP: Validate syntax" - Check for syntax errors
- "Format Document" - Manual formatting
- "Toggle Terminal" - Quick terminal access
- "Git: Commit" - Version control operations

### Integrated Terminal
**Toggle**: `Ctrl + `` ` (backtick)

**Essential Workflow**:
1. **Test your PHP**: Open terminal and run `php --version` to verify PHP is installed
2. **Run PHP files**: Use `php filename.php` to execute your scripts
3. **Stay in VS Code**: No need to switch to external command prompt

**Pro Tip**: The terminal opens in your current workspace folder, so file paths are automatically correct.

## 📁 Project Management

### Workspace Configuration
For consistent team settings, you can create project-specific configurations. This becomes important later when working on team projects, but for now, the default settings work perfectly for learning.

**When You Need This**: When you start working with teams or on larger projects, you'll want consistent formatting and validation rules across all developers.

### File Explorer Shortcuts
- `Ctrl + Shift + E` - Focus file explorer
- `Ctrl + N` - New file
- `F2` - Rename file/folder
- `Delete` - Move to trash

## 🐛 Debugging Integration

### Prerequisites for PHP Debugging
**⚠️ Important**: PHP debugging in VS Code requires **Xdebug** to be installed and configured on your system. Without Xdebug, the debugging features below won't work.

**Check if Xdebug is installed**:
1. Open VS Code terminal (`Ctrl + `` `)
2. Run: `php -m | grep -i xdebug`
3. If you see "xdebug" in the output, you're ready to debug
4. If not, you'll need to install and configure Xdebug first

**What if Xdebug isn't installed?**
- **XAMPP users**: Xdebug is usually included but may need activation
- **Other installations**: You'll need to install Xdebug separately
- **For now**: You can still learn PHP without debugging - it becomes important for complex applications

### Breakpoint Management
**Once Xdebug is configured**:
- **Set breakpoint**: Click left margin or press `F9`
- **Start debugging**: `F5`
- **Step over**: `F10`
- **Step into**: `F11`
- **Continue**: `F5`

### Debug Console
**Practical Debugging Workflow (Requires Xdebug)**:
1. Set a breakpoint by clicking in the left margin of your code
2. Press `F5` to start debugging
3. When code pauses at your breakpoint, hover over variables to see their values
4. Use the debug console to test expressions and check variable states
5. Press `F10` to step through your code line by line

**When This Helps**: Essential when your PHP code isn't working as expected and you need to understand what's happening step by step.

**Alternative for Beginners**: If debugging setup seems complex, you can use `echo` and `var_dump()` statements to inspect variables - this works without any additional setup!

## 🚀 Success Indicators

You've mastered VS Code for PHP development when you can:

- [ ] Format code automatically without thinking about it
- [ ] Navigate large projects quickly using keyboard shortcuts
- [ ] Use IntelliSense to write code faster and with fewer errors
- [ ] Recover accidentally deleted or modified code using Local History
- [ ] Debug PHP applications with breakpoints and variable inspection
- [ ] Use multi-cursor editing for efficient refactoring
- [ ] Access any VS Code feature through Command Palette

## ➡️ Next Steps

With VS Code mastered, continue with [Step 4: Environment Setup](README.md#🛠️-development-environment-setup) to complete your XAMPP, Git, and XDebug installation.

**Pro Tip**: Don't try to memorize all shortcuts at once. Focus on the most common ones first (Ctrl + S, Ctrl + P, Ctrl + Shift + P) and gradually add more to your repertoire. Muscle memory develops through consistent practice, not forced memorization.
