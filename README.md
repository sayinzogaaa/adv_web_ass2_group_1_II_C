# Umuganda Smart Service Request Platform

**INES-Ruhengeri | Advanced Web Design & Development | Assignment #2**
Group 1 — Year II C | Submission: Friday 15 May 2026

---

## 🔗 Important Links

| Item | URL |
|------|-----|
| **GitHub Repository** | `https://github.com/sayinzogaaa/adv_web_ass2_group_1_II_C` |
| **Live Deployed App** | `https://sayinzoga.infinityfreeapp.com` |

> **Action required:** Replace the placeholders above after deployment, then commit this README.

---

## ✅ Features Implemented

| # | Feature | File(s) |
|---|---------|---------|
| 1 | Public homepage | `public/index.php` |
| 2 | Request submission form | `app/view/request_form.php` |
| 3 | **JavaScript live preview** (real-time) | `assets/js/script.js` |
| 4 | MySQL storage — MySQLi prepared statements | `app/controller/RequestController.php` |
| 5 | Admin login + session protection | `app/controller/LoginController.php` |
| 6 | **Admin dashboard + summary stats** | `app/view/dashboard.php` |
| 7 | **Search & filter** by name, category, status | `app/view/dashboard.php` |
| 8 | **Status update** Pending→In Progress→Resolved | `app/controller/RequestController.php` |
| 9 | MVC folder structure | entire project |
| 10 | Deployment to InfinityFree | see below |

---

## 🗂️ MVC Folder Structure

```
umuganda_project/
├── public/
│   └── index.php
├── app/
│   ├── controller/
│   │   ├── LoginController.php
│   │   └── RequestController.php
│   └── view/
│       ├── login.php
│       ├── logout.php
│       ├── dashboard.php
│       └── request_form.php
├── config/
│   └── db.php
├── database/
│   └── schema.sql
├── assets/
│   ├── css/style.css
│   └── js/script.js
├── setup.php
└── README.md
```

---

## ⚙️ Local Setup

```bash
# 1. Clone
git clone https://github.com/YOUR_GITHUB_USERNAME/adv_web_ass2_group_1_II_C.git

# 2. Import DB (phpMyAdmin or terminal)
mysql -u root -p < database/schema.sql

# 3. Edit config/db.php with your credentials

# 4. Start server
php -S localhost:8000 -t public/
# Visit: http://localhost:8000
```

---

## 🔐 Test Credentials

| Role  | Username | Password   |
|-------|----------|------------|
| Admin | `admin`  | `admin123` |

---

## 🚀 InfinityFree Deployment

1. Sign up at infinityfree.net → create hosting account
2. Upload all files to **htdocs/**
3. Create MySQL DB → import `database/schema.sql`
4. Update `config/db.php` with InfinityFree DB credentials
5. Visit your live URL and paste it into this README

---

## 👥 GitHub Collaborators

Invite all 6 group members + `ambitieux.clement@gmail.com` under **Settings → Collaborators**.
