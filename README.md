# Educaster

Educaster is a web-based teacher training platform built as a group project for the IWT module in Y1S2. The system supports different user roles, course browsing and enrolment, quizzes, provider tools, admin management, and customer inquiries.

## Project Snapshot

![Educaster home page](Home.png)

## Features

- User registration, login, and account management
- Course browsing, enrolment, and content viewing
- Quiz creation, quiz taking, and quiz result handling
- Student dashboard for enrolled course activity
- Provider dashboard for creating and managing courses and quizzes
- Admin dashboard for managing users, courses, providers, and platform activity
- Customer support and inquiry management

## Project Structure

- `home.php` - landing page for the platform
- `programs.php` - programme listing page
- `aboutus.php` - project and platform information
- `courses/` - course overview, content, enrolment, and unenrolment pages
- `dashboard/` - student dashboard pages
- `provider/` - provider tools for course and quiz management
- `admin/` - admin management pages
- `quiz/` - quiz flow pages
- `customerSupport/` - inquiry and contact pages
- `user/` - authentication and account pages
- `common/` - shared configuration, headers, helpers, and login logic
- `css/` - page and component styling
- `js/` - front-end scripts
- `uploads/` - uploaded course and content assets

## Setup

1. Install XAMPP or another PHP + MySQL stack.
2. Place the project folder inside your web root, for example `htdocs/educaster`.
3. Create a `.env` file based on `.env.example` and configure your database credentials.
4. Make sure MySQL is running and the Educaster database is available.
5. Open the project in your browser through the local server path, for example `http://localhost/educaster/home.php`.

## Contributors

- Chamod - admin module and quiz module
- Sandun - course module and student dashboard
- Manul - inquiry module

## Notes

- The application uses shared bootstrap logic from `common/config.php`.
- The home page is available in `home.php` and uses `Home.png` as the screenshot in this README.