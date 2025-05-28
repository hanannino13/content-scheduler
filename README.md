## Installation

Follow these steps to get the project up and running on your local machine.

### Prerequisites

Before you begin, ensure you have the following installed:

* **PHP:** Version 8.2 or higher (or the specific version your project requires, e.g., `^8.2`).
* **Node.js & npm:** (Node.js version 18+ recommended)
* **Composer:** (PHP package manager)
* **MySQL:** Or any other database supported by Laravel (e.g., PostgreSQL, SQLite).

### Steps

1.  **Clone the repository:**
    ```bash
    git clone [your-repository-url]
    ```
    *Replace `[your-repository-url]` with the actual URL of your GitHub repository.*

2.  **Navigate into the project directory:**
    ```bash
    cd your-project-name
    ```
    *Replace `your-project-name` with the actual name of the cloned directory.*

3.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

4.  **Install Node.js dependencies:**
    ```bash
    npm install
    ```

5.  **Create a copy of your environment file:**
    ```bash
    cp .env.example .env
    ```

6.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```

7.  **Configure your database:**
    Open the newly created `.env` file and update the database connection details:
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name # Change this to your actual database name
    DB_USERNAME=your_database_user # Change this to your database username
    DB_PASSWORD=your_database_password # Change this to your database password
    ```
    *Make sure to create the `your_database_name` database in your MySQL server.*

8.  **Run database migrations and seeders:**
    This will create the necessary tables in your database and populate them with initial data.
    ```bash
    php artisan migrate --seed
    ```

9.  **Compile frontend assets:**
    * **For development (with hot reloading):**
        ```bash
        npm run dev
        ```
        *Keep this command running in a separate terminal window while developing.*
    * **For production (optimized build):**
        ```bash
        npm run build
        ```

10. **Start the Laravel development server:**
    ```bash
    php artisan serve
    ```

11. **Access the application:**
    Open your web browser and navigate to the URL provided by the `php artisan serve` command (typically `http://127.0.0.1:8000`).

## Project Overview

This project is a content scheduling application built with Laravel. It aims to provide users with a dashboard to manage and schedule their posts across various platforms, along with features for user authentication, activity logging, and profile management.

Throughout the development process, the primary focus has been on resolving core programming challenges related to Laravel's routing, controllers, middleware, CSS integration, Sanctum authentication for the web frontend, and the integration of the Spatie Activitylog package.

While significant progress has been made on the technical implementation, this README outlines the remaining requirements for a complete project submission as per the provided guidelines.

---

## Submission Guidelines Checklist & Progress

Below is a breakdown of the submission requirements, indicating the current status and what needs to be completed.

### 1. GitHub Repository with README.md

* **Current Status:** You possess the foundational codebase for the project.
* **Action Required:**
    * Create a new public GitHub repository.
    * Upload all your project code to this repository.
    * **Crucially, create a comprehensive `README.md` file.** This file should include:
        * A clear description of the project and its purpose.
        * An overview of the technologies used.
        * The detailed installation instructions (see point 3 below).
        * An explanation of the architectural approach and trade-offs (see point 4 below).

### 2. Database Migrations and Seeders

* **Current Status:**
    * The database **migrations** are correctly set up and functional, as confirmed by successful resolution of issues like the `activity_log` table not found. This indicates that your database schema (e.g., `users`, `posts`, `platforms`, `activity_log` tables) is defined.
    * **Database Seeders:** This aspect was not directly covered in our discussions.
* **Action Required:**
    * **Ensure the existence and functionality of Database Seeders.** If you haven't created them yet, you must develop seeders to populate your database with initial data (e.g., a default user, sample posts, platforms). This is vital for easy setup and review.
    * Verify that commands like `php artisan migrate --seed` or `php artisan db:seed` execute without errors and populate the database as expected.

### 3. Installation Instructions

* **Current Status:** The instructions were not directly written during our troubleshooting.
* **Action Required:**
    * These instructions **must be a core part of your `README.md` file** on GitHub.
    * They should provide a step-by-step guide for anyone to download and run your project on their machine.
    * **Refer to the "Installation" section** (as provided in the previous response) for the complete content, covering prerequisites, cloning, dependency installation (Composer, npm), environment setup, database migrations/seeding, asset compilation, and serving the application.

### 4. Explanation of Approach and Trade-offs

* **Current Status:** This detailed explanation was not directly written.
* **Action Required:**
    * This section should also be a significant part of your `README.md` file.
    * **Explain the key architectural and technical decisions** made during the project's development. For instance:
        * Why Laravel was chosen as the framework.
        * The decision to use Laravel Sanctum for web authentication (cookie-based SPA authentication) instead of traditional session-based authentication (like default Laravel Breeze). Discuss the advantages (e.g., API readiness, token-based flexibility) and disadvantages/complexity (e.g., manual frontend JS handling for login/logout, CSRF token management for AJAX requests from Blade).
        * The integration and benefits of `Spatie\laravel-activitylog` for tracking user actions.
        * The overall structure of your controllers (e.g., `Web/` namespace for Blade controllers).
        * Any other significant choices regarding database design, third-party packages, or frontend frameworks.
    * **Discuss the "trade-offs":** What alternative solutions did you consider for key features? What were the pros and cons of each option? Why did you ultimately choose your current approach?

---

This structured approach will help you present your project comprehensively according to the submission guidelines.