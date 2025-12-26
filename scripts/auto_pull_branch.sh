#!/bin/bash
# Auto-pull script
# Usage: auto_pull_branch.sh [repo_dir] [branch]
set -euo pipefail
REPO_DIR="${1:-/Users/buiquocvu/dropseller-clean}"
BRANCH="${2:-main}"

cd "$REPO_DIR" || { echo "Repo not found: $REPO_DIR" >&2; exit 1; }

# Ensure git is available
command -v git >/dev/null 2>&1 || { echo "git not installed" >&2; exit 1; }

# Fetch remote refs
git fetch origin --prune

# Checkout branch locally or track remote
if git show-ref --verify --quiet "refs/heads/$BRANCH"; then
  git checkout "$BRANCH"
else
  git checkout -b "$BRANCH" "origin/$BRANCH"
fi

# Stash local changes if present
STASHED=0
if [ -n "$(git status --porcelain)" ]; then
  git stash push -u -m "auto-pull-$(date +%s)" || true
  STASHED=1
fi

# Try a safe fast-forward pull, fall back to reset to remote if necessary
if ! git pull --ff-only origin "$BRANCH"; then
  git reset --hard "origin/$BRANCH"
fi

# Try to reapply stash
if [ "$STASHED" = 1 ]; then
  git stash pop || echo "No stash to pop or conflict occurred" >&2
fi

exit 0
