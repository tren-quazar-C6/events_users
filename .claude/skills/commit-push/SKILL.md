---
name: commit-push
description: Stage changed files, commit with a short feat: message, and push to a feat/* branch. Use after completing any set of changes.
disable-model-invocation: true
---

Follow these steps exactly:

1. Run `git status` to identify changed and untracked files relevant to the work just done.
2. Check the current branch name with `git branch --show-current`.
   - If already on a `feat/*` branch, stay there.
   - If on `main` or any other branch, create and switch to a new branch: `git checkout -b feat/<short-kebab-description>` where the description is derived from the changes made (2–4 words, lowercase, hyphens).
3. Stage the relevant files with `git add <file1> <file2> ...` (never `git add -A` or `git add .` unless every changed file is intentional).
4. Write a commit message following this format exactly:
   ```
   feat: <what changed in one short sentence>
   ```
   - The subject line must be under 72 characters.
   - No scope (no `feat(something):`). Just `feat:`.
   - No body. Never add Co-Authored-By, co-author lines, or any Claude signature. Never.
5. Commit: `git commit -m "feat: <message>"`
6. Push to remote: `git push -u origin <branch-name>`
7. Report the branch name and commit message to the user in one line.

Do not open a pull request unless the user explicitly asks.
