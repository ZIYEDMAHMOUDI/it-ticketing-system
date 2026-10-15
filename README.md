# IT Ticketing System

A web application for handling IT support requests. Employees open tickets, technicians work on them, and admins manage user accounts. Built with PHP and MySQL as a learning project, focused on access control and secure database access.

## Features

**Employees**
- Create a ticket with a title, description, category, and priority
- View their own tickets and their current status
- Cancel a ticket, as long as no technician has taken it yet

**Technicians**
- View the queue of open tickets
- Take an unassigned ticket
- Update the status to in progress or solved, and write a resolution

**Admins**
- Create user accounts with a role (employee, technician, or admin)
- Activate and deactivate accounts (deactivated users cannot log in)
- View all tickets

## Security

- Passwords are stored as hashes using `password_hash()`, never in plain text
- All database queries use prepared statements (PDO) to prevent SQL injection
- Output is escaped with `htmlspecialchars()` to prevent XSS
- Access rules are enforced on the server for every page and action, not only by hiding buttons
- Login errors show the same message for a wrong email and a wrong password

## Stack

- PHP 8 with PDO
- MySQL
- HTML5 and CSS3
- Vanilla JavaScript
- Git and GitHub

## Project structure


## Database

Three tables: `users`, `categories`, and `tickets`.

- `tickets.created_by` links each ticket to the employee who opened it
- `tickets.assigned_to` links each ticket to the technician who took it (empty until taken)
- `tickets.category_id` links each ticket to a category

The full structure is in `database/schema.sql`.

## Running it locally

1. Install [XAMPP](https://www.apachefriends.org) and start **Apache** and **MySQL**.
2. Clone this repository into `C:\xampp\htdocs\it-ticketing-system`.
3. Open phpMyAdmin (`http://localhost/phpmyadmin`) and import `database/schema.sql`.
4. Create `config/database.php` with your connection settings:
```php
   <?php
   $host     = 'localhost';
   $dbname   = 'ticketing_system';
   $username = 'root';
   $password = '';

   $pdo = new PDO(
       "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
       $username,
       $password,
       [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
       ]
   );
```
5. Create the first admin account by inserting a row into `users` with a hashed password (generate the hash with `password_hash()`), role `admin`, and `active` set to 1.
6. Open `http://localhost/it-ticketing-system/public/login.php`.

## What I learned

- **Git and GitHub:** I learned to save my work in small, clear commits, so every step of the project is recorded and I can go back if something breaks.
- **Planning before coding:** I learned to define the problem first, then sketch the database and the logic on paper before writing any code. This saved time and reduced rework.
- **Applying school knowledge:** I used what I studied at ISET, especially databases, OOP, and web development, in a real project.
- **Self-taught skills:** I learned PHP sessions, prepared statements, password hashing, and role-based access control on my own, and applied them to this project.

## Status

This is a working MVP. The interface uses basic styling, and the focus is on the backend logic and access control.

## Planned improvements

- Comments between employees and technicians, including internal notes
- A ticket history log of every status change
- Search and filters on the ticket lists
- Online deployment