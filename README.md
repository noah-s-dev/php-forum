# PHP Forum System

A modern, secure, and responsive forum system built with PHP and MySQL. This application provides a complete discussion platform with user authentication, topic management, threaded replies, and comprehensive security features.

## 🛠️ Technologies Used

- **Backend**: PHP 7.4+ (8.0+ recommended)
- **Database**: MySQL 5.7+ (8.0+ recommended)
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework**: Bootstrap 5.3.0
- **Icons**: Bootstrap Icons 1.10.0
- **Fonts**: Google Fonts (Inter)
- **Web Server**: Apache/Nginx
- **Security**: Bcrypt, CSRF Protection, XSS Prevention

## 📋 Project Overview

The PHP Forum System is a comprehensive discussion platform designed for communities to engage in meaningful conversations. It features a modern, responsive design with robust security measures and an intuitive user interface. The system supports user registration, topic creation, threaded discussions, search functionality, and role-based access control.

## ✨ Key Features

### Core Functionality
- **User Authentication**: Secure registration and login system with password hashing
- **Topic Management**: Create, view, and manage discussion topics
- **Threaded Replies**: Reply to topics and other replies for organized discussions
- **Forum Browsing**: Browse topics with pagination, search, and filtering options
- **Responsive Design**: Mobile-friendly interface using Bootstrap 5

### Security Features
- **Password Security**: Bcrypt password hashing with strength validation
- **CSRF Protection**: Cross-Site Request Forgery protection on all forms
- **XSS Prevention**: Input sanitization and output encoding
- **SQL Injection Prevention**: Prepared statements and parameter binding
- **Session Security**: Secure session management with regeneration
- **Rate Limiting**: Protection against brute force attacks
- **Security Headers**: Comprehensive HTTP security headers

### User Experience
- **Clean Interface**: Modern, minimal design with smooth animations
- **Search Functionality**: Full-text search across topics and content
- **Pagination**: Efficient browsing of large topic lists
- **User Profiles**: Editable user profiles with statistics
- **Real-time Validation**: Client-side form validation with feedback
- **Responsive Layout**: Optimized for desktop, tablet, and mobile devices

## 👥 User Roles

### Regular User
- Create and reply to topics
- Edit own posts
- Update profile information
- Search and browse topics
- View forum statistics

### Moderator
- All user permissions
- Edit any post
- Lock/unlock topics
- Pin/unpin topics
- Moderate discussions

### Administrator
- All moderator permissions
- User management
- System configuration
- Access to admin panel
- Full system control

## 📁 Project Structure

```
php-forum/
├── config/
│   ├── config.php          # Main configuration
│   ├── database.php        # Database connection
│   └── security.php        # Security functions
├── includes/
│   ├── functions.php       # Common functions
│   ├── header.php          # HTML header template
│   └── footer.php          # HTML footer template
├── public/
│   ├── auth/
│   │   ├── login.php       # Login page
│   │   ├── register.php    # Registration page
│   │   ├── logout.php      # Logout handler
│   │   └── profile.php     # User profile
│   ├── forum/
│   │   ├── topics.php      # Topic listing
│   │   ├── view_topic.php  # Topic view
│   │   └── create_topic.php # Topic creation
│   ├── css/
│   │   └── style.css       # Custom styles
│   ├── js/
│   │   └── script.js       # JavaScript functionality
│   └── index.php           # Homepage
├── sql/
│   └── forum_setup.sql     # Database schema
└── README.md               # This file
```

## 🚀 Setup Instructions

### Prerequisites
- PHP 7.4 or higher (8.0+ recommended)
- MySQL 5.7 or higher (8.0+ recommended)
- Apache or Nginx web server
- PHP extensions: PDO, PDO_MySQL, mbstring, openssl

### Installation Steps

1. **Download and Setup**
   ```bash
   # Clone or download the project
   git clone https://github.com/noah-s-dev/php-forum.git
   cd php-forum

   # Set proper permissions
   chmod 755 public/
   chmod 644 public/*.php
   chmod 644 config/*.php
   chmod 644 includes/*.php
   ```

2. **Database Setup**
   ```sql
   -- Create database
   CREATE DATABASE php_forum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

   -- Import the schema
   mysql -u username -p php_forum < sql/forum_setup.sql
   ```

3. **Configuration**
   
   a. **Database Configuration**
      Edit `config/database.php` with your database credentials:
      ```php
      define('DB_HOST', 'localhost');
      define('DB_NAME', 'php_forum');
      define('DB_USER', 'your_username');
      define('DB_PASS', 'your_password');
      ```
   
   b. **URL Configuration**
      The system automatically detects the base URL and project path. If your project is located at `http://localhost/php-forum`, the system will automatically configure the URLs correctly. No manual configuration is needed for the base URL.
      
      The `SITE_URL` in `config/config.php` is automatically set based on your server configuration and project location.

## 📖 Usage

### Getting Started

1. **Access the Forum**: 
   - If installed in root: Navigate to `http://localhost` in a web browser
   - If installed in subdirectory (e.g., `php-forum`): Navigate to `http://localhost/php-forum` in a web browser
   - The system automatically detects the correct base path and configures URLs accordingly
2. **Register an Account**: Click "Register" and create your account
3. **Login**: Use your credentials to log in
4. **Create Topics**: Click "New Topic" to start a discussion
5. **Reply to Topics**: Click on any topic to view and reply

### Default Admin Account
Default admin credentials (change immediately):
- **Username**: admin
- **Password**: admin123

## 🎯 Intended Use

This PHP Forum System is designed for:

- **Educational Purposes**: Learning PHP, MySQL, and web development
- **Small Communities**: Local clubs, study groups, or hobby communities
- **Prototyping**: Testing forum concepts before scaling up
- **Personal Projects**: Individual developers building discussion platforms
- **Demo Applications**: Showcasing forum functionality and features

The system provides a solid foundation for forum development with modern security practices and responsive design. It's suitable for small to medium-sized communities and can be extended with additional features as needed.

## 🔧 Customization

### Styling
- Edit `public/css/style.css` for custom styles
- Modify Bootstrap variables for theme changes
- Update color scheme in CSS variables

### Functionality
- Add new features in respective directories
- Follow existing code patterns and security practices
- Update database schema as needed

### Configuration
- Modify `config/config.php` for site settings
- Adjust pagination and limits as needed
- Configure security settings in `config/security.php`

## 🛡️ Security Considerations

### Production Deployment

1. **Environment Configuration**
   - Disable error reporting in production
   - Use HTTPS for all connections
   - Set secure session cookies
   - Configure proper file permissions

2. **Database Security**
   - Use dedicated database user with minimal privileges
   - Enable MySQL SSL connections
   - Regular database backups
   - Monitor for suspicious queries

3. **Server Security**
   - Keep PHP and MySQL updated
   - Configure firewall rules
   - Regular security updates
   - Monitor access logs

### Security Features Implemented

- **Input Validation**: All user inputs are validated and sanitized
- **Output Encoding**: All outputs are properly encoded to prevent XSS
- **CSRF Protection**: All forms include CSRF tokens
- **SQL Injection Prevention**: All database queries use prepared statements
- **Password Security**: Passwords are hashed using bcrypt
- **Session Security**: Secure session configuration with regeneration
- **Rate Limiting**: Protection against brute force attacks
- **Security Headers**: Comprehensive HTTP security headers


## 📄 License

**License for RiverTheme**
RiverTheme makes this project available for demo, instructional, and personal use. You can ask for or buy a license from [RiverTheme.com](https://RiverTheme.com) if you want a pro website, sophisticated features, or expert setup and assistance. A Pro license is needed for production deployments, customizations, and commercial use.

**Disclaimer**
The free version is offered "as is" with no warranty and might not function on all devices or browsers. It might also have some coding or security flaws. For additional information or to get a Pro license, please get in touch with [RiverTheme.com](https://RiverTheme.com).

---

**Note**: This is a basic forum system intended for educational and small-scale use. For large-scale production deployments, consider additional optimizations and security measures.

