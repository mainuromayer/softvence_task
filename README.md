# Project Name
# softvence_task

## Requirements
- PHP >= 8.2
- Composer
- Laravel >= 12.18.0
- MySQL (Laragon)

## Installation
1. Create a new Laravel project:
    ```bash
    composer create-project laravel/laravel softvence_task
    cd softvence_task
    ```

2. Configure the `.env` file:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=softvence_task
    DB_USERNAME=root
    DB_PASSWORD=
    ```

3. Install dependencies:
    ```bash
    composer install
    ```

4. Generate the application key:
    ```bash
    php artisan key:generate
    ```

5. Run database migrations:
    ```bash
    php artisan migrate
    ```

6. Start the development server:
    ```bash
    php artisan serve
    ```

7. Access the application in your browser at:
    ```
    http://127.0.0.1:8000
    ```

## Screenshots

Here is a preview of the application:

![Homepage Screenshot](screenshots/homepage.png)

![Dashboard Screenshot](screenshots/dashboard.png)