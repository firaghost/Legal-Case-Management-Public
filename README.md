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

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 8.0+
- Web server (Apache/Nginx)

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Legal-Case-Mngmnt
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your database**
   - Update `.env` with your database credentials
   - Create a new MySQL database

6. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Set up file storage**
   ```bash
   php artisan storage:link
   ```

### 🔑 Default Login Credentials

After running the seeders, you can log in with these default accounts:

**Administrator Account:**
- Email: `admin@lcms.test`
- Password: `admin123`

**Supervisor Account:**
- Email: `supervisor@lcms.test`
- Password: `supervisor123`

**Lawyer Account:**
- Email: `lawyer@lcms.test`
- Password: `lawyer123`

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

## 🔒 Security

Security is paramount in legal software. If you discover any security vulnerabilities, please:

1. **DO NOT** create a public issue
2. Email the security team directly
3. Provide detailed information about the vulnerability
4. Allow time for the issue to be addressed before public disclosure

We take all security reports seriously and will respond promptly.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Built with ❤️ for legal professionals
- Powered by the amazing Laravel community
- Special thanks to all contributors and testers
- Designed with modern professional standards in mind

---

<p align="center">
  <strong>Ready to streamline your legal operations?</strong><br>
  Get started with LCMS today and experience the difference modern technology can make in legal case management.
</p>






