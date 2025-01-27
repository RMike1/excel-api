## Employee Data Export API

Laravel based API system for exporting employee data to Excel files. It uses Laravel queues for efficient processing of large datasets and generates Excel files with PhpOffice's PhpSpreadsheet.

[![Tests](https://github.com/RMike1/excel-api/actions/workflows/test.yml/badge.svg)](https://github.com/RMike1/excel-api/actions/workflows/test.yml)

### Features

- Export Employee Data: Enables exporting large employee datasets to Excel format.

### Steps to Clone and Set Up this Project
Follow these steps to clone the repo and set up the project:

1. Clone the Repository
Run the following command to clone the project from GitHub:
```shell
git clone https://github.com/RMike1/excel-api.git
```

2. Navigate to the Project Directory
```shell
cd excel-api
```

3. Install PHP dependencies:
```shell
composer install
```

4. Create .env file
Copy the .env.example file to create a .env file and .env.testing (For testing):
```shell
cp .env.example .env
cp .env.example .env.testing
```

5. Generate Application Key
```shell
php artisan key:generate
```

6. Set up the database
Run migrations:
```shell
php artisan migrate
```

7. Start the server
```shell
php artisan serve
```

### Technologies Used

- Laravel: Backend framework for routing, controllers, and services.
- PhpSpreadsheet: Used for generating Excel files.

### Usage
- Start the queue worker
```shell
 php artisan queue:work --queue=export_excel --no-ansi
```

### Endpoint
- Export Employee Data

```shell
 POST  /api/employees/export
```

### Testing
Run test
```shell
 php artisan test
```