# get_current_version.py
import re

CHANGELOG_FILE = "../../changelog.md"

def get_latest_version():
    """Reads the latest version from changelog.md frontmatter."""
    try:
        with open(CHANGELOG_FILE, "r", encoding="utf-8") as file:
            content = file.read()
        match = re.search(r"version:\s*(\d+\.\d+\.\d+)", content)
        return match.group(1) if match else "0.1.0"
    except FileNotFoundError:
        return "0.1.0"

if __name__ == "__main__":
    print(get_latest_version())