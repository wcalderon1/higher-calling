# Higher Calling — Full-Stack Social Platform

**Stack:** Laravel, MySQL, PHP, Tailwind CSS, JavaScript
**Deployed:** cPanel (live web deployment)
**Course:** Capstone Project — Georgia Gwinnett College, Fall 2025

---

## Project Overview

Higher Calling is a faith-based social platform built with Laravel as a full-stack capstone project. The platform enables users to connect around spiritual content through devotionals, community interaction, and personalized reading plans.

This project demonstrates end-to-end full-stack development: from database design and backend logic to frontend UI and live deployment.

---

## Features

| Feature | Description |
|---|---|
| User Profiles | Account registration, login, and personalized profile pages |
| Devotionals | Create, publish, and browse daily devotional posts |
| Social Interaction | Comments, likes, and follower/following system |
| Reading Plans | Structured reading plans with progress tracking |
| Admin Panel | Role-based moderation tools for content management |
| Responsive Design | Mobile-first layout built with Tailwind CSS |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend Framework | Laravel (PHP) |
| Database | MySQL with Eloquent ORM |
| Frontend Styling | Tailwind CSS + Blade templates |
| Interactivity | JavaScript |
| Build Tool | Vite |
| Deployment | cPanel shared hosting |

---

## Database Schema

| Table | Purpose |
|---|---|
| Users | Authentication, profile data, roles |
| Devotionals | Posts with categories and publish status |
| Comments | Nested comments on devotionals |
| Follows | Many-to-many follower relationships |
| Reading Plans | Plan definitions and user progress tracking |
| Likes | Polymorphic like system |

---

## Highlights

Built and deployed a production-ready web application as a solo capstone project. Implemented role-based permissions using Laravel's authorization system (Gates & Policies). Managed the full deployment lifecycle including environment configuration, asset compilation with Vite, and cPanel setup. Focused on accessibility, mobile responsiveness, and intuitive navigation throughout.

---

## Local Setup

```bash
git clone https://github.com/wcalderon1/higher-calling.git
cd higher-calling
composer install
npm install
cp .env.example .env
php artisan key:generate
# Configure your .env with database credentials
php artisan migrate --seed
npm run dev
php artisan serve
```

---

*Individual project — Wendy Calderon · Georgia Gwinnett College · Fall 2025*
