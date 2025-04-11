import re
import datetime
import subprocess
import sys

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

def increment_patch_version(version):
    """Increments the patch version (e.g., 1.0.5 -> 1.0.6)."""
    major, minor, patch = map(int, version.split("."))
    return f"{major}.{minor}.{patch + 1}"

# def get_new_commits():
#     """Gets new commit messages that aren't already in the changelog."""
#     try:
#         # Get the last commit that modified the changelog file
#         last_changelog_commit = subprocess.run(
#             ["git", "log", "-1", "--pretty=format:%H", "--", CHANGELOG_FILE],
#             capture_output=True, text=True
#         ).stdout.strip()
        
#         if last_changelog_commit:
#             # Get commits since the last changelog update, excluding the update commit itself
#             result = subprocess.run(
#                 ["git", "log", "--pretty=format:- %s", f"{last_changelog_commit}..HEAD", "--not", "--grep=Updated changelog"],
#                 capture_output=True, text=True
#             )
#             commits = result.stdout.strip()
            
#             # If we have commits and they're not just the changelog update
#             if commits and "Updated changelog" not in commits:
#                 return commits
#     except Exception as e:
#         print(f"Error getting commits: {e}", file=sys.stderr)
    
#     # Default to empty if no new commits found
#     return ""
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
            ["git", "log", "--pretty=format:- %s", f"{last_changelog_commit}..HEAD", "--invert-grep", "--grep=Updated changelog"],
            capture_output=True, text=True, check=True
        )

        commits = result.stdout.strip()
        
        if commits:
            return commits
    except subprocess.CalledProcessError as e:
        print(f"Git command failed: {e}", file=sys.stderr)
    
    # print("Defaulting to empty string - no new commits found.")
    return ""

# Test
print(get_new_commits())

def update_changelog(new_version, commit_messages):
    """Updates changelog.md with the new version and commits."""
    today = datetime.date.today().strftime("%Y-%m-%d")

    try:
        with open(CHANGELOG_FILE, "r+", encoding="utf-8") as file:
            content = file.read()
            
            # Update frontmatter version
            new_content = re.sub(r"(version:\s*)(\d+\.\d+\.\d+)", rf"\g<1>{new_version}", content, count=1)
            
            # Check if we have commit messages to add
            if commit_messages.strip():
                # Find where to insert the new entry (after the frontmatter)
                changelog_start = re.search(r"---\s*\n.*\n---\s*\n", new_content, re.DOTALL)
                insert_position = changelog_start.end() if changelog_start else 0
                
                # Prepare new version entry
                new_entry = f"## [{new_version}] - {today}\n\n{commit_messages}\n\n"
                
                # Insert the new entry after the frontmatter
                new_content = new_content[:insert_position] + new_entry + new_content[insert_position:]
            
            # Write back
            file.seek(0)
            file.write(new_content)
            file.truncate()
            
            return True
    except Exception as e:
        print(f"Error updating changelog: {e}", file=sys.stderr)
        return False
    
    return False

def commit_and_push():
    """Commits and pushes the changelog update."""
    try:
        subprocess.run(["git", "add", CHANGELOG_FILE])
        subprocess.run(["git", "commit", "-m", "Updated changelog"])
        subprocess.run(["git", "push"])
        return True
    except Exception as e:
        print(f"Error committing changes: {e}", file=sys.stderr)
        return False

if __name__ == "__main__":
    # Get current version and calculate new version
    current_version = get_latest_version()
    new_version = increment_patch_version(current_version)
    
    # Get new commit messages
    commit_messages = get_new_commits()
    
    # Update changelog if we have new commits
    if commit_messages.strip():
        success = update_changelog(new_version, commit_messages)
        if success:
            commit_and_push()
            print(f"{new_version}|SUCCESS|{commit_messages}")
        else:
            print(f"{current_version}|FAIL|Failed to update changelog")
    else:
        print(f"{current_version}|NOCHANGE|No new commits")