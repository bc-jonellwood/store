# update_changelog.py
import re
import datetime
import sys

CHANGELOG_FILE = "../../changelog.md"

def update_changelog(new_version, commit_messages):
    """Updates changelog.md with the new version and commits."""
    today = datetime.date.today().strftime("%Y-%m-%d")

    try:
        with open(CHANGELOG_FILE, "r+", encoding="utf-8") as file:
            content = file.read()
            # Update frontmatter version
            new_content = re.sub(r"(version:\s*)(\d+\.\d+\.\d+)", rf"\g<1>{new_version}", content, count=1)
            
            # Find where to insert the new entry
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
    except FileNotFoundError:
        # Create a new changelog file if it doesn't exist
        new_content = f"---\nversion: {new_version}\n---\n\n## [{new_version}] - {today}\n\n{commit_messages}\n"
        with open(CHANGELOG_FILE, "w", encoding="utf-8") as file:
            file.write(new_content)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python update_changelog.py <new_version> <commit_messages>")
        sys.exit(1)
        
    new_version = sys.argv[1]
    commit_messages = sys.argv[2]
    
    update_changelog(new_version, commit_messages)
    print(new_version)  # Return the new version