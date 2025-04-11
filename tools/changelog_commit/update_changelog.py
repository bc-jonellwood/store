import re
import subprocess
import datetime
import sys

CHANGELOG_FILE = "../../changelog.md"

def get_latest_version():
    """Reads the latest version from changelog.md frontmatter."""
    try:
        with open(CHANGELOG_FILE, "r", encoding="utf-8") as file:
            content = file.read()
        match = re.search(r"version:\s*(\d+\.\d+\.\d+)", content)
        return match.group(1) if match else "0.1.0"  # Default if no version found
    except FileNotFoundError:
        return "0.1.0"

def increment_patch_version(version):
    """Increments the patch version (e.g., 1.0.5 -> 1.0.6)."""
    major, minor, patch = map(int, version.split("."))
    return f"{major}.{minor}.{patch + 1}"

def get_commit_messages():
    """Gets commit messages from the latest push."""
    result = subprocess.run(["git", "log", "-1", "--pretty=format:- %s"], 
                            capture_output=True, text=True)
    return result.stdout.strip() if result.stdout else "- No commit messages found."

def get_new_commits():
    """Gets new commit messages that aren't already in the changelog."""
    try:
        # Get the last commit that modified the changelog file
        last_changelog_commit = subprocess.run(
            ["git", "log", "-1", "--pretty=format:%H", "--", CHANGELOG_FILE],
            capture_output=True, text=True, check=True
        ).stdout.strip()
        
        if not last_changelog_commit:
            print("No previous commit found for the changelog file.", file=sys.stderr)
            return ""

        # Get commits since the last changelog update
        result = subprocess.run(
            ["git", "log", "--pretty=format:- %s", f"{last_changelog_commit}..HEAD", "--invert-grep"],
            capture_output=True, text=True, check=True
        )

        commits = result.stdout.strip()
        
        if commits:
            return commits
    except subprocess.CalledProcessError as e:
        print(f"Git command failed: {e}", file=sys.stderr)
    
    # print("Defaulting to empty string - no new commits found.")
    return ""


def update_changelog(new_version, commit_messages):
    """Updates changelog.md with the new version and commits."""
    today = datetime.date.today().strftime("%Y-%m-%d")

    try:
        with open(CHANGELOG_FILE, "r+", encoding="utf-8") as file:
            content = file.read()
            # Update frontmatter version
            new_content = re.sub(r"(version:\s*)(\d+\.\d+\.\d+)", rf"\g<1>{new_version}", content, count=1)
            # Append new version log
            new_entry = f"\n## [{new_version}] - {today}\n\n{commit_messages}\n"
            new_content += new_entry
            # Write back
            file.seek(0)
            file.write(new_content)
            file.truncate()
    except FileNotFoundError:
        # Create a new changelog file if it doesn't exist
        new_content = f"---\nversion: {new_version}\n---\n## [{new_version}] - {today}\n\n{commit_messages}\n"
        with open(CHANGELOG_FILE, "w", encoding="utf-8") as file:
            file.write(new_content)

def commit_and_push():
    """Commits and pushes the changelog update."""
    subprocess.run(["git", "add", CHANGELOG_FILE])
    subprocess.run(["git", "commit", "-m", "Updated changelog"])
    subprocess.run(["git", "push"])

def get_new_version():
    """Reads the latest version from changelog.md frontmatter."""
    try:
        with open(CHANGELOG_FILE, "r", encoding="utf-8") as file:
            content = file.read()
        match = re.search(r"version:\s*(\d+\.\d+\.\d+)", content)
        if not match:
            print("Error: Version not found in changelog.md")
            return "0.1.0"  # Fallback version
        return match.group(1)
    except FileNotFoundError:
        print("Error: changelog.md not found")
        return "0.1.0"


if __name__ == "__main__":
    current_version = get_latest_version()
    new_version = increment_patch_version(current_version)
    # commit_messages = get_commit_messages()
    commit_messages = get_new_commits()
    
    update_changelog(new_version, commit_messages)
    new_version_for_database = get_new_version()
    # print(new_version)
    print(new_version_for_database)
    
    # Uncomment the next line if you want automatic commits
    commit_and_push()

