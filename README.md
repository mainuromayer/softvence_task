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

![Course Index Page Screenshot](/public/screenshots/course_index.png)
![Course Create 1 Page Screenshot](/public/screenshots/course_create-1.png)
![Course Create 2 Page Screenshot](/public/screenshots/course_create-2.png)
![Course Create 3 Page Screenshot](/public/screenshots/course_create-3.png)
![Course View 1 Page Screenshot](/public/screenshots/course_view-1.png)
![Course View 2 Page Screenshot](/public/screenshots/course_view-2.png)
![Course View 3 Page Screenshot](/public/screenshots/course_view-3.png)
![Course Edit 1 Page Screenshot](/public/screenshots/course_edit-1.png)
![Course Edit 2 Page Screenshot](/public/screenshots/course_edit-2.png)
![Course Edit 3 Page Screenshot](/public/screenshots/course_edit-3.png)
