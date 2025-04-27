# Dropseller Project

## Overview
The Dropseller project is a web application designed to facilitate the management of dropshipping services. It provides users with tools to manage orders, track transactions, and optimize their dropshipping operations.

## Features
- User authentication and role management
- Order management system
- Transaction history tracking
- Tools for calculating campaign percentages and adding SKU names
- Responsive design for mobile and desktop users

## Directory Structure
```
dropseller
├── resources
│   ├── views
│   │   ├── tools
│   │   │   └── add-sku-name.blade.php
│   └── navbar.blade.php
└── README.md
```

## Setup Instructions
1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd dropseller
   ```

2. **Install Dependencies**
   Make sure you have Composer installed, then run:
   ```bash
   composer install
   ```

3. **Environment Configuration**
   Copy the `.env.example` file to `.env` and configure your database and application settings:
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations**
   Set up your database by running the migrations:
   ```bash
   php artisan migrate
   ```

6. **Start the Development Server**
   ```bash
   php artisan serve
   ```

## Usage
- Access the application through your web browser at `http://localhost:8000`.
- Use the navigation bar to access different sections of the application, including the tools section for adding SKU names.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for details.