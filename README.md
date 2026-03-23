# Job Application Tracker

A web application built with PHP and MySQL to manage and track job applications.

## Features

### Authentication
- User registration with validation
- Password hashing for security
- Login system with session management
- Protected dashboard (accessible only when logged in)
- Logout functionality

## Technologies

- PHP (PDO)
- MySQL
- HTML / CSS
- Git & GitHub

## Project Structure

- register.php → user registration
- login.php → user authentication
- dashboard.php → protected user page
- logout.php → session destruction
- config/database.php → database connection
- assets/ → CSS and JS files

## Installation
1. Clone the repository: git clone https://github.com/azanguim123/Job-application-tracker.git
2. Move the project to XAMPP:  C:\xampp\htdocs\projects/job-tracker
3. Create the database:
- Name: `job_tracker`
4. Create the table `users` with fields:
- id
- full_name
- email
- password
- created_at
5. Configure the database connection in:  `config/database.php`
6. Run in browser: `http://localhost/projets/job-tracker/`

## Future Improvements

- Add job application CRUD (create, read, update, delete)
- Search and filter applications
- Dashboard statistics
- Improve UI/UX

## Author

Azanguim Ndongmo Larry Nelson