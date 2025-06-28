# Course Management System (CMS)

A comprehensive Laravel-based Course Management System with role-based authentication, assignment submission, and grading capabilities.

## 🚀 Features

### Core Functionality
- **Role-Based Authentication**: Admin, Lecturer, and Student roles with specific permissions
- **User Management**: Complete CRUD operations with department assignments and archiving
- **Course Management**: Create, edit, and manage courses with enrollment tracking
- **Assignment System**: Full assignment lifecycle from creation to grading
- **Submission Management**: Student file uploads and text submissions
- **Grading Interface**: Comprehensive lecturer grading tools with feedback system

### Key Highlights
- **Responsive Design**: Mobile-optimized interface for all user roles
- **File Upload System**: Secure file handling for assignment submissions
- **Real-time Statistics**: Live dashboards with progress tracking
- **Export Capabilities**: CSV export for gradebook integration
- **Advanced Filtering**: Search and sort functionality across all modules
- **Professional UI**: Bootstrap-based interface with intuitive navigation

## 🛠️ Technology Stack

- **Backend**: Laravel 11.x
- **Frontend**: Bootstrap 5.1.3, Font Awesome 6.0
- **Database**: MySQL/SQLite
- **Authentication**: Laravel's built-in authentication
- **File Storage**: Laravel's file storage system
- **Version Control**: Git

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/SQLite
- Git

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd course-mgt-sys
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

5. **Database configuration**
   - Update `.env` file with your database credentials
   - Run migrations and seeders:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Storage setup**
   ```bash
   php artisan storage:link
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## 👥 User Roles & Permissions

### Admin
- Complete system oversight
- User management (create, edit, archive users)
- Course management (create, edit, delete courses)
- Department management
- Assignment oversight
- System reports and analytics

### Lecturer
- Course assignment management
- Create and manage assignments
- View and grade student submissions
- Export grades
- Student progress tracking

### Student
- Course enrollment
- View assigned courses and materials
- Submit assignments with file uploads
- View grades and feedback
- Track assignment deadlines

## 📊 Database Schema

### Core Tables
- `users` - User accounts with role-based access
- `departments` - Academic departments
- `courses` - Course information and management
- `enrollments` - Student-course relationships
- `assignments` - Assignment details and requirements
- `submissions` - Student assignment submissions
- `course_materials` - Course resources (future feature)
- `messages` - Communication system (future feature)

## 🎯 Key Features Detail

### Assignment Submission System
- **Student Interface**: Clean submission form with file upload
- **File Support**: PDF, DOC, DOCX, TXT, ZIP, RAR (10MB limit)
- **Deadline Management**: Automatic late submission detection
- **Update Capability**: Students can modify submissions before grading

### Lecturer Grading Interface
- **Submissions Dashboard**: Comprehensive view of all submissions
- **Advanced Filtering**: Search by student, filter by status, sort options
- **Grading Modal**: Intuitive scoring with feedback system
- **Export Tools**: CSV download for gradebook integration
- **Statistics**: Real-time grading progress and analytics

### Responsive Design
- **Mobile-First**: Optimized for all screen sizes
- **Card Layouts**: Mobile-friendly submission viewing
- **Touch-Friendly**: Large buttons and easy navigation
- **Progressive Enhancement**: Works on all devices

## 🔐 Security Features

- **Role-Based Access Control**: Middleware protection for all routes
- **File Upload Security**: Validated file types and size limits
- **Data Isolation**: Users only see their authorized data
- **CSRF Protection**: Laravel's built-in CSRF protection
- **Input Validation**: Comprehensive form validation

## 📁 Project Structure

```
course-mgt-sys/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/              # Eloquent models
│   └── Middleware/          # Custom middleware
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   └── views/              # Blade templates
│       ├── admin/          # Admin interface views
│       ├── dashboard/      # Role-based dashboards
│       └── student/        # Student interface views
├── routes/
│   └── web.php            # Application routes
└── storage/
    └── app/public/        # File uploads storage
```

## 🚦 Getting Started

### Default Login Credentials
After running the seeders, you can use these default accounts:

**Admin Account:**
- Email: admin@cms.com
- Password: password

**Lecturer Account:**
- Email: lecturer@cms.com
- Password: password

**Student Account:**
- Email: student@cms.com
- Password: password

### First Steps
1. Login as Admin to set up departments and courses
2. Create lecturer and student accounts
3. Assign lecturers to courses
4. Students can enroll in available courses
5. Lecturers can create assignments
6. Students submit work, lecturers grade submissions

## 🔄 Git Workflow

The project is now under Git version control with:
- Initial commit containing complete CMS functionality
- Proper `.gitignore` for Laravel projects
- Structured commit messages for feature tracking

### Recommended Git Commands
```bash
# Check status
git status

# Add changes
git add .

# Commit changes
git commit -m "Feature: Description of changes"

# View history
git log --oneline

# Create feature branch
git checkout -b feature/new-feature

# Merge feature
git checkout main
git merge feature/new-feature
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is open-source and available under the [MIT License](LICENSE).

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review the code comments for implementation details

## 🎉 Acknowledgments

- Laravel Framework for the robust backend foundation
- Bootstrap for the responsive UI components
- Font Awesome for the comprehensive icon library
- The open-source community for inspiration and best practices

---

**Course Management System** - Empowering education through technology 🎓
