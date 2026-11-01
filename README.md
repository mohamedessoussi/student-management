# Student Management System

A web-based Student Management System built with **PHP, MySQL, HTML, CSS, and JavaScript**. This application helps manage student information through a simple and user-friendly dashboard.

## Features

- 🔐 User Authentication (Login & Registration)
- 👨‍🎓 Add New Students
- 📋 View Student Records
- ✏️ Update Student Information
- 🗑️ Delete Student Records
- ✅ Form Validation
- 📊 Dashboard Interface
- 🗄️ MySQL Database Integration

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Local Server:** XAMPP
- **Version Control:** Git & GitHub

## Project Structure

```text
student_management/
│
├── assets/
│   ├── css/
│   └── js/
│
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── register.php
│
├── classes/
│   ├── Student.php
│   ├── User.php
│   └── Validator.php
│
├── config/
│   └── Database.example.php
│
├── includes/
├── students/
│   ├── create.php
│   ├── read.php
│   ├── update.php
│   └── delete.php
│
├── dashboard.php
├── index.php
└── student_db.sql
```

## Installation & Setup

1. Clone the repository:

```bash
git clone https://github.com/mohamedessoussi/student-management.git
```

2. Move the project into the XAMPP `htdocs` directory.

3. Start Apache and MySQL from XAMPP Control Panel.

4. Open phpMyAdmin and create a MySQL database.

5. Import the `student_db.sql` file into your database.

6. Copy `config/Database.example.php` and rename the copy to `Database.php`.

7. Configure your database connection credentials inside `config/Database.php`.

8. Open the project in your browser:

```text
http://localhost/student_management/
```

## Author

**Mohamed Essoussi**

- GitHub: [@mohamedessoussi](https://github.com/mohamedessoussi)

## License

This project is available for educational and portfolio purposes.
