# Contributing to Poste Tunisienne

Welcome to the Poste Tunisienne administration application repository. This guide covers everything you need to know to contribute effectively to the project.

---

## Table of Contents

- [Contributing to Poste Tunisienne](#contributing-to-poste-tunisienne)
  - [Table of Contents](#table-of-contents)
  - [Getting Started](#getting-started)
    - [Prerequisites](#prerequisites)
    - [Setup](#setup)
  - [Application Features](#application-features)
  - [Project Structure](#project-structure)
  - [Project Board](#project-board)
  - [Branch Naming Convention](#branch-naming-convention)
    - [Branch Types](#branch-types)
    - [Examples](#examples)
  - [Working on an Issue](#working-on-an-issue)
  - [Commit Message Format](#commit-message-format)
    - [Types](#types)
    - [Examples](#examples-1)
  - [Opening a Pull Request](#opening-a-pull-request)
    - [Pull Request Rules](#pull-request-rules)
  - [Code Review Process](#code-review-process)
  - [PHP Code Standards](#php-code-standards)
  - [Rules \& Non-Negotiables](#rules--non-negotiables)

---

## Getting Started

### Prerequisites

- PHP 8.0 or newer
- MySQL
- A local PHP development server such as XAMPP, Laragon, or PHP CLI
- Git
- VS Code or another PHP-compatible editor

### Setup

```bash
# Clone the repository
git clone https://github.com/DevHoussem/poste_project.git
cd poste_project

# Configure the database
# Create a local .env.local file at the repository root:
HOST=localhost
DBNAME=poste_bd
DBUSERNAME=root
DBPASSWORD=

# Start the PHP development server
php -S localhost:8000
```

Open `http://localhost:8000/app/auth/sign_in.php` in your browser.

The application expects a MySQL database named `poste_bd` with the `admins` and `operations` tables. Never commit `.env.local` or real credentials.

If you encounter setup issues, contact the project maintainer.

---

## Application Features

Authenticated administrators can:

- Register and sign in securely with hashed passwords.
- View operation totals and recent operations on the dashboard.
- Filter operations by start and end dates.
- Navigate through paginated operation results.
- Add new operations with server-side validation.
- Edit existing operations and recalculate their totals automatically.
- Delete operations through a protected POST action with confirmation and CSRF validation.
- Export all or date-filtered operations as a CSV file.

### Main Application Pages

| Page | Purpose |
|---|---|
| `/app/auth/sign_in.php` | Administrator sign-in |
| `/app/auth/register.php` | Administrator registration |
| `/app/dashboard.php` | Statistics, filtering, pagination, and actions |
| `/app/add_operation.php` | Add an operation |
| `/app/edit_operation.php?id=<id>` | Edit an operation |
| `/app/export_operations.php` | Export operations as CSV |
| `/app/delete_operation.php` | POST-only operation deletion endpoint |

Database and configuration failures are handled through the `errors/503.html` service-unavailable page. Other standard error pages are available in the `errors/` directory.

---

## Project Structure

```text
app/
├── auth/
│   ├── auth.php              # Authenticated-session guard
│   ├── register.php          # Administrator registration
│   └── sign_in.php           # Administrator sign-in
├── add_operation.php         # Create an operation
├── dashboard.php             # Dashboard and operation listing
├── delete_operation.php      # Protected deletion endpoint
├── edit_operation.php        # Edit an operation
├── export_operations.php     # CSV export endpoint
└── operations.php            # Shared validation, filters, and CSRF helpers
config/
└── config.php                # .env.local loading and PDO connection
errors/                       # HTTP error pages
js/                           # JavaScript assets
style/                        # CSS assets
```

---

## Project Board

All issues must be tracked on the project board used by the team.

**When you open or are assigned an issue, you must:**

1. Open the issue on GitHub.
2. Add it to the project board.
3. Set its priority.
4. Set its status to `Backlog`, or `Ready` if it has been assigned to you.

> Issues that are not added to the project board may be flagged during review.

---

## Branch Naming Convention

Always create a new branch from the latest `main` branch:

```bash
git checkout main
git pull origin main
git checkout -b <type>/<issue-number>-<short-description>
```

### Branch Types

| Prefix | When to Use |
| `fix/` | Bug fixes |
| `feat/` | New features or enhancements |
| `ux/` | UI/UX improvements |
| `chore/` | Refactoring, configuration, or dependencies |
| `docs/` | Documentation only |

### Examples

```text
fix/42-login-redirect
feat/67-operation-export
ux/31-dashboard-spacing
chore/15-refactor-database-config
docs/08-update-contributing
```

The issue number in the branch name links your branch to the issue automatically in GitHub.

---

## Working on an Issue

1. Make sure the issue is assigned to you before starting.
2. Move the issue to `In Progress` on the project board.
3. Create your branch using the naming convention above.
4. Work in small, focused commits.
5. Test PHP syntax and the affected application flow locally.
6. Open a pull request when the work is ready.

---

## Commit Message Format

Use clear, lowercase commit messages that describe what changed and why.

```text
<type>: <short description>

Optional body explaining why this change was made.
```

### Types

- `fix:` — bug fix
- `feat:` — new feature
- `ux:` — UI/UX change
- `chore:` — refactor, cleanup, or configuration update
- `docs:` — documentation only

### Examples

```text
fix: correct dashboard asset paths
feat: add date-range operation export
ux: improve mobile operation form layout
chore: load database credentials from env
docs: update contributing guide
```

---

## Opening a Pull Request

When your work is ready for review:

1. Push your branch to GitHub.
2. Go to the repository and open a new pull request.
3. Set the base branch to `main`.
4. Fill out the pull request template completely.
5. Attach before-and-after screenshots for UI changes.
6. Link the related issue under **Development**.
7. Add the pull request to the project board.
8. Move the issue to `In Review`.

### Pull Request Rules

- One pull request per issue; never bundle unrelated changes.
- Keep pull requests focused and small where possible.
- Use `Closes #<issue-number>` in the pull request description when appropriate.
- Review every changed line before requesting review.
- Do not merge your own pull request.
- Never include `.env.local`, passwords, or other secrets.

---

## Code Review Process

After opening a pull request:

- Pull requests should be reviewed within 24 hours on business days.
- Review comments may be labeled:
  - `nit:` — minor suggestion, optional to address
  - `blocker:` — must be resolved before merge
  - `question:` — clarification requested
- Address all `blocker:` comments with either a code change or a discussion reply.
- Once the pull request is approved, it can be merged by an authorized maintainer.

---

## PHP Code Standards

- Use the existing project structure and shared configuration helpers.
- Keep database credentials in `.env.local`, never in committed PHP files.
- Use prepared statements for database queries with user input.
- Escape dynamic HTML output with `htmlspecialchars()`.
- Use `require_once` with `__DIR__` for PHP file includes.
- Keep operation validation and CSRF logic in the shared operations helper.
- Keep delete actions as authenticated POST requests; never delete records through GET links.
- Calculate `total_operations` on the server from the individual operation fields.
- Validate PHP files with:

```bash
php -l path/to/file.php
```

- Validate all application PHP files with:

```powershell
Get-ChildItem app -Filter *.php -Recurse | ForEach-Object { php -l $_.FullName }
```

- Keep CSS and JavaScript assets in their existing `style/` and `js/` directories.
- Preserve existing behavior unless the issue explicitly requires a behavior change.

---

## Rules & Non-Negotiables

These rules apply to every contribution:

1. **Never push directly to `main`** — all changes go through a pull request.
2. **Never merge your own pull request** — a review is required first.
3. **Every issue must be on the project board** before work begins.
4. **Every UI change requires screenshots** in the pull request.
5. **Every branch must reference an issue number.**
6. **Never commit secrets** or `.env.local`.
7. **No commented-out code** in pull requests — delete it or open a separate chore issue.
