# 🏛️ Legal Case Management System (LCMS)

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue.js">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

<p align="center">
  A comprehensive, web-based legal case management system designed to streamline and centralize legal operations for organizations. Built with modern technologies and a focus on user experience.
</p>

---

## 🌟 What Makes LCMS Special?

Legal work shouldn't be bogged down by complicated software. LCMS was built with real legal professionals in mind, offering:

- **🎯 Intuitive Design** - Clean, professional interface that lawyers and staff actually enjoy using
- **⚡ Lightning Fast** - Built on Laravel's robust foundation for reliable performance
- **🔒 Enterprise-Level Security** - Role-based access control and comprehensive audit trails
- **📱 Mobile Friendly** - Responsive design that works seamlessly on all devices
- **💬 Real-Time Communication** - Integrated chat system powered by Vue.js for instant collaboration

## 🚀 Key Features

### 📋 **Case Management**
- Complete case lifecycle management from initiation to closure
- Support for multiple case types: Criminal, Civil, Labor, Loan Recovery, and more
- Automated workflow and approval processes
- Smart case assignment and tracking

### 👥 **Role-Based Access Control**
- **Administrators** - Full system oversight and user management
- **Supervisors** - Case oversight and approval authority
- **Lawyers** - Case management and documentation
- **Clerks** - Data entry and basic case updates

### 📊 **Comprehensive Reporting**
- Professional PDF reports with customizable branding
- Real-time analytics and case statistics
- Customizable report generation
- Export capabilities for external use

### 💬 **Integrated Communication**
- Real-time chat system for team collaboration
- Case-specific communication threads
- File sharing and document collaboration
- Notification system for important updates

### 📁 **Document Management**
- Secure file upload and storage
- Document versioning and revision history
- Role-based document access permissions
- Integration with case workflows

### 🔍 **Audit & Compliance**
- Complete audit trails for all system activities
- Compliance reporting and monitoring
- User activity tracking
- Security incident logging

## 🛠️ Technology Stack

**Backend Framework**
- **Laravel 12.x** - Robust PHP framework providing the core application logic
- **PHP 8.2+** - Modern PHP for optimal performance and security

**Frontend Technologies**
- **Blade Templates** - Laravel's templating engine for server-side rendering
- **Tailwind CSS** - Utility-first CSS framework for beautiful, responsive design
- **Vue.js 3.x** - Powers the real-time chat system for seamless communication
- **Inertia.js** - Bridges Laravel and Vue.js for the chat functionality

**Database & Storage**
- **MySQL 8.0+** - Reliable relational database for data persistence
- **File Storage** - Secure document and media file management

**Additional Technologies**
- **Laravel Fortify** - Authentication and user management
- **Spatie Permissions** - Role and permission management
- **DomPDF** - Professional PDF report generation
- **Pusher** - Real-time communication infrastructure
- **Laravel Sanctum** - API authentication

## 📦 Installation & Setup

### 🔧 Prerequisites

**System Requirements:**
- **PHP 8.2 or higher** with required extensions
- **Composer** (PHP dependency manager)
- **Node.js 16+ & NPM** (for frontend assets)
- **MySQL 8.0+** (database)
- **Web server** (Apache/Nginx) or XAMPP for local development

**Required PHP Extensions:**
Ensure the following PHP extensions are enabled (especially important for XAMPP users):
- `zip` - **Required for Composer and Laravel**
- `curl` - For HTTP requests
- `fileinfo` - For file type detection
- `mbstring` - For string manipulation
- `openssl` - For encryption
- `pdo_mysql` - For MySQL database connection
- `tokenizer` - For Laravel framework
- `xml` - For XML processing
- `gd` or `imagick` - For image processing
- `bcmath` - For mathematical operations

### 🚀 Step-by-Step Installation Guide

#### Step 1: Enable Required PHP Extensions (XAMPP Users)

If you're using XAMPP, you need to enable required PHP extensions:

1. **Open XAMPP Control Panel**
2. **Stop Apache** if it's running
3. **Navigate to your XAMPP installation directory** (usually `C:\xampp`)
4. **Edit `php.ini`** file located in `C:\xampp\php\php.ini`
5. **Find and uncomment** (remove the `;` at the beginning) these lines:
   ```ini
   extension=zip
   extension=curl
   extension=fileinfo
   extension=mbstring
   extension=openssl
   extension=pdo_mysql
   extension=gd
   extension=bcmath
   ```
6. **Save the file** and **restart Apache** in XAMPP Control Panel
7. **Verify extensions** are loaded by creating a PHP file with `<?php phpinfo(); ?>` and checking the loaded extensions

#### Step 2: Clone the Repository

```bash
git clone https://github.com/firaghost/Legal-Case-Management-Public.git
cd Legal-Case-Management-Public
```

#### Step 3: Install PHP Dependencies

```bash
composer install
```

> ⚠️ **If you get a "zip extension not found" error**, make sure you've enabled the zip extension in Step 1.

#### Step 4: Install Node.js Dependencies

```bash
npm install
```

#### Step 5: Environment Configuration

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

3. **Configure your `.env` file** with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=legal_case_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

#### Step 6: Database Setup

1. **Create a new MySQL database** named `legal_case_management` (or your preferred name)
2. **Update database credentials** in your `.env` file
3. **Test database connection:**
   ```bash
   php artisan migrate:status
   ```

#### Step 7: Create Required Directories

Ensure all necessary directories exist:

```bash
# Create storage directories if they don't exist
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
```

**For Windows users:**
```cmd
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"
if not exist "bootstrap\cache" mkdir "bootstrap\cache"
```

#### Step 8: Set Directory Permissions

**For Linux/Mac users:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

**For Windows/XAMPP users:**
- Ensure your web server has read/write permissions to `storage` and `bootstrap/cache` directories

#### Step 9: Run Database Migrations and Seeders

```bash
php artisan migrate --seed
```

> 📝 **Note:** This will create all necessary database tables and populate them with sample data including default user accounts.

#### Step 10: Set Up File Storage

```bash
php artisan storage:link
```

#### Step 11: Build Frontend Assets

```bash
npm run build
```

#### Step 12: Start the Development Server

```bash
php artisan serve
```

Your application will be available at: `http://localhost:8000`

### 🔍 Troubleshooting Common Issues

**Issue: "Class 'ZipArchive' not found"**
- **Solution:** Enable the `zip` extension in your `php.ini` file (see Step 1)

**Issue: "Permission denied" errors**
- **Solution:** Ensure proper directory permissions for `storage` and `bootstrap/cache`

**Issue: "Directory not found: storage/framework/views"**
- **Solution:** Create the directory manually using the commands in Step 7

**Issue: "SQLSTATE[HY000] [2002] Connection refused"**
- **Solution:** Ensure MySQL is running and database credentials in `.env` are correct

**Issue: Composer install fails**
- **Solution:** Ensure all required PHP extensions are enabled and PHP version is 8.2+

### 🔄 Alternative Installation (Using Laravel Sail)

For a Docker-based development environment:

```bash
# Install dependencies
composer install

# Start Sail
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate --seed

# Build assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 🔑 Default Login Credentials

After running the seeders, you can log in with these default accounts:

**Administrator Account:**
- Email: `admin@lcms.test`
- Password: `password`

**Supervisor Account:**
- Email: `supervisor@lcms.test`
- Password: `password`

**Lawyer Account:**
- Email: `lawyer@lcms.test`
- Password: `password`

> ⚠️ **Important:** Change these default passwords immediately after your first login, especially in production environments!

### 🔧 Configuration

For detailed configuration instructions, including:
- Database setup
- File storage configuration
- Email settings
- Real-time chat setup
- PDF generation settings

Please refer to our [Deployment Checklist](DEPLOYMENT_CHECKLIST.md) and [User Guide](LCMS_User_Guide.md).

## 📚 Documentation

- **[User Guide](LCMS_User_Guide.md)** - Comprehensive guide for end users
- **[Deployment Checklist](DEPLOYMENT_CHECKLIST.md)** - Step-by-step deployment guide
- **[Production Deployment](PRODUCTION_DEPLOYMENT_CHECKLIST.md)** - Production-ready deployment guide

## 🤝 Contributing

We welcome contributions from the community! Whether you're fixing bugs, adding features, or improving documentation, your help makes LCMS better for everyone.

### How to Contribute
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Built for legal professionals
- Powered by the amazing Laravel community
- Special thanks to all contributors and testers
- Designed with modern professional standards in mind

---

<p align="center">
  <strong>Ready to streamline your legal operations?</strong><br>
  Get started with LCMS today and experience the difference modern technology can make in legal case management.
</p>






