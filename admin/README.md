
# 🛠️ PickMyAnime Admin System

This admin panel allows you to manage all anime recommendations, mood tags, and internal tools for [PickMyAnime.com](https://pickmyanime.com).

---

## 🔐 Authentication

| File | Purpose |
|------|---------|
| `login.php` | Admin login form |
| `logout.php` | Logout and clear session |
| `includes/auth.php` | Session protection for secure access |

---

## 🧭 Navigation

| File | Purpose |
|------|---------|
| `includes/nav.php` | Shared top navbar used across all admin pages |

---

## 🏠 Entry Point

| File | Purpose |
|------|---------|
| `index.php` | Main admin panel with quick access to tools |

---

## 📊 Dashboard

| File | Purpose |
|------|---------|
| `dashboard.php` | Stats like total anime, mood usage, and recent entries |

---

## 📚 Anime Management

| File | Purpose |
|------|---------|
| `list.php` | View and manage all anime entries |
| `add-anime.php` | Add new anime recommendation |
| `edit-anime.php` | Edit existing anime |
| `delete-anime.php` | Delete anime (with confirmation) |
| `insert-anime.php` | Logic to insert new entries into DB |
| `update-anime.php` | Logic to update existing entries |

---

## 🏷️ Mood Tag Management

| File | Purpose |
|------|---------|
| `edit-moods.php` | Rename or delete existing mood tags |

---

## 🧪 Other

| File | Purpose |
|------|---------|
| `init.php` | Optional setup/init logic (if used) |

---

## 🧭 Flow Overview

```text
index.php
 ├──> dashboard.php
 ├──> list.php
 │     ├──> edit-anime.php → update-anime.php
 │     └──> delete-anime.php
 ├──> add-anime.php → insert-anime.php
 ├──> edit-moods.php
 └──> logout.php
```

---

Built with ❤️ for managing mood-based anime recommendations.
