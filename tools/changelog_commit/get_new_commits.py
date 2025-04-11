import re
import datetime
import subprocess
import sys

CHANGELOG_FILE = "../../changelog.md"



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
#     print(f"Defaulting to empty string - no new commits found.")
#     return ""
# get_new_commits()

# import subprocess
# import sys

# CHANGELOG_FILE = "changelog.md"  # Ensure this is correct

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
            # ["git", "log", "--pretty=format:- %s", f"d4219212011ee75a1945c825d3d9127d71b860d7..HEAD", "--invert-grep"],
            capture_output=True, text=True, check=True
        )

        commits = result.stdout.strip()
        
        if commits:
            return commits
    except subprocess.CalledProcessError as e:
        print(f"Git command failed: {e}", file=sys.stderr)
    
    print("Defaulting to empty string - no new commits found.")
    return ""

# Test
print(get_new_commits())

