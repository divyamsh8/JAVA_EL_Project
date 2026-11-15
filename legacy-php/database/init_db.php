<?php
/**
 * Database Initializer & Seeder
 * Generates seed.sql and populates SQLite or MySQL automatically
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';

function get_fictional_seed_dataset(): array {
    // 30 Students: 10 CSE, 10 ECE, 10 MECH
    $students = [
        // CSE (10)
        ['usn' => '1DM24CS001', 'first_name' => 'Aarav', 'last_name' => 'Sharma', 'gender' => 'MALE', 'dob' => '2005-04-15', 'blood_group' => 'O+', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Rajesh Sharma', 'mother' => 'Sunita Sharma', 'phone' => '9845012345', 'email' => 'rajesh.sharma@example.com', 'address' => '#142, 4th Main, Kuvempunagar, Mysuru - 570023'],
        ['usn' => '1DM24CS002', 'first_name' => 'Ananya', 'last_name' => 'Iyer', 'gender' => 'FEMALE', 'dob' => '2005-08-22', 'blood_group' => 'B+', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Venkatesh Iyer', 'mother' => 'Radha Iyer', 'phone' => '9845012346', 'email' => 'venkatesh.iyer@example.com', 'address' => '#56, Gokulam 3rd Stage, Mysuru - 570002'],
        ['usn' => '1DM24CS003', 'first_name' => 'Rohan', 'last_name' => 'Verma', 'gender' => 'MALE', 'dob' => '2004-11-05', 'blood_group' => 'A+', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Sanjay Verma', 'mother' => 'Poonam Verma', 'phone' => '9845012347', 'email' => 'sanjay.verma@example.com', 'address' => '#88, Vijayanagar 2nd Stage, Mysuru - 570017'],
        ['usn' => '1DM24CS004', 'first_name' => 'Priya', 'last_name' => 'Nair', 'gender' => 'FEMALE', 'dob' => '2005-02-18', 'blood_group' => 'AB+', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Madhavan Nair', 'mother' => 'Shobha Nair', 'phone' => '9845012348', 'email' => 'madhavan.nair@example.com', 'address' => '#210, Saraswathipuram, Mysuru - 570009'],
        ['usn' => '1DM24CS005', 'first_name' => 'Karthik', 'last_name' => 'Rao', 'gender' => 'MALE', 'dob' => '2005-06-30', 'blood_group' => 'O-', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Prabhakar Rao', 'mother' => 'Geetha Rao', 'phone' => '9845012349', 'email' => 'prabhakar.rao@example.com', 'address' => '#12, Jayalakshmipuram, Mysuru - 570012'],
        ['usn' => '1DM24CS006', 'first_name' => 'Sneha', 'last_name' => 'Patil', 'gender' => 'FEMALE', 'dob' => '2005-09-12', 'blood_group' => 'A-', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Basavaraj Patil', 'mother' => 'Suvarna Patil', 'phone' => '9845012350', 'email' => 'basavaraj.patil@example.com', 'address' => '#75, Ramakrishnanagar, Mysuru - 570022'],
        ['usn' => '1DM24CS007', 'first_name' => 'Aditya', 'last_name' => 'Kulkarni', 'gender' => 'MALE', 'dob' => '2004-12-25', 'blood_group' => 'B-', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Anand Kulkarni', 'mother' => 'Meera Kulkarni', 'phone' => '9845012351', 'email' => 'anand.kulkarni@example.com', 'address' => '#401, Dattagalli, Mysuru - 570023'],
        ['usn' => '1DM24CS008', 'first_name' => 'Diya', 'last_name' => 'Deshmukh', 'gender' => 'FEMALE', 'dob' => '2005-03-14', 'blood_group' => 'O+', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Nitin Deshmukh', 'mother' => 'Kavita Deshmukh', 'phone' => '9845012352', 'email' => 'nitin.deshmukh@example.com', 'address' => '#19, Hebbal 1st Stage, Mysuru - 570016'],
        ['usn' => '1DM24CS009', 'first_name' => 'Varun', 'last_name' => 'Bhat', 'gender' => 'MALE', 'dob' => '2005-07-08', 'blood_group' => 'AB-', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Subramanya Bhat', 'mother' => 'Sharada Bhat', 'phone' => '9845012353', 'email' => 'subramanya.bhat@example.com', 'address' => '#64, Srirampura, Mysuru - 570023'],
        ['usn' => '1DM24CS010', 'first_name' => 'Meera', 'last_name' => 'Nambiar', 'gender' => 'FEMALE', 'dob' => '2005-10-19', 'blood_group' => 'B+', 'dept_id' => 1, 'course_id' => 1, 'father' => 'Gopalan Nambiar', 'mother' => 'Lalitha Nambiar', 'phone' => '9845012354', 'email' => 'gopalan.nambiar@example.com', 'address' => '#93, Siddarthanagar, Mysuru - 570011'],

        // ECE (10)
        ['usn' => '1DM24EC001', 'first_name' => 'Nikhil', 'last_name' => 'Hegde', 'gender' => 'MALE', 'dob' => '2005-01-10', 'blood_group' => 'O+', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Manjunath Hegde', 'mother' => 'Shailaja Hegde', 'phone' => '9845022301', 'email' => 'manjunath.hegde@example.com', 'address' => '#102, Alanahalli, Mysuru - 570028'],
        ['usn' => '1DM24EC002', 'first_name' => 'Tanvi', 'last_name' => 'Reddy', 'gender' => 'FEMALE', 'dob' => '2005-05-16', 'blood_group' => 'A+', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Srinivas Reddy', 'mother' => 'Padmaja Reddy', 'phone' => '9845022302', 'email' => 'srinivas.reddy@example.com', 'address' => '#15, Bogadi 2nd Stage, Mysuru - 570026'],
        ['usn' => '1DM24EC003', 'first_name' => 'Arjun', 'last_name' => 'Pai', 'gender' => 'MALE', 'dob' => '2004-10-28', 'blood_group' => 'B+', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Dinesh Pai', 'mother' => 'Sandhya Pai', 'phone' => '9845022303', 'email' => 'dinesh.pai@example.com', 'address' => '#72, Vontikoppal, Mysuru - 570002'],
        ['usn' => '1DM24EC004', 'first_name' => 'Pooja', 'last_name' => 'Menon', 'gender' => 'FEMALE', 'dob' => '2005-04-03', 'blood_group' => 'O-', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Ravi Menon', 'mother' => 'Deepa Menon', 'phone' => '9845022304', 'email' => 'ravi.menon@example.com', 'address' => '#33, Yadavagiri, Mysuru - 570020'],
        ['usn' => '1DM24EC005', 'first_name' => 'Chetan', 'last_name' => 'Gowda', 'gender' => 'MALE', 'dob' => '2005-09-24', 'blood_group' => 'A+', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Shivalinga Gowda', 'mother' => 'Manjula Gowda', 'phone' => '9845022305', 'email' => 'shivalinga.gowda@example.com', 'address' => '#118, Roopa Nagar, Mysuru - 570026'],
        ['usn' => '1DM24EC006', 'first_name' => 'Keerthi', 'last_name' => 'Shetty', 'gender' => 'FEMALE', 'dob' => '2005-11-15', 'blood_group' => 'B-', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Suresh Shetty', 'mother' => 'Kusuma Shetty', 'phone' => '9845022306', 'email' => 'suresh.shetty@example.com', 'address' => '#51, Metagalli, Mysuru - 570016'],
        ['usn' => '1DM24EC007', 'first_name' => 'Manoj', 'last_name' => 'Kumar', 'gender' => 'MALE', 'dob' => '2004-07-19', 'blood_group' => 'AB+', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Ramesh Kumar', 'mother' => 'Usha Kumar', 'phone' => '9845022307', 'email' => 'ramesh.kumar@example.com', 'address' => '#204, Krishnamurthypuram, Mysuru - 570004'],
        ['usn' => '1DM24EC008', 'first_name' => 'Ishita', 'last_name' => 'Sengupta', 'gender' => 'FEMALE', 'dob' => '2005-03-29', 'blood_group' => 'O+', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Debashis Sengupta', 'mother' => 'Mitali Sengupta', 'phone' => '9845022308', 'email' => 'debashis.sengupta@example.com', 'address' => '#84, Chamundipuram, Mysuru - 570004'],
        ['usn' => '1DM24EC009', 'first_name' => 'Siddharth', 'last_name' => 'Joshi', 'gender' => 'MALE', 'dob' => '2005-12-04', 'blood_group' => 'A-', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Girish Joshi', 'mother' => 'Vidya Joshi', 'phone' => '9845022309', 'email' => 'girish.joshi@example.com', 'address' => '#14, Sharadadevinagar, Mysuru - 570022'],
        ['usn' => '1DM24EC010', 'first_name' => 'Rhea', 'last_name' => 'Fernandes', 'gender' => 'FEMALE', 'dob' => '2005-06-11', 'blood_group' => 'B+', 'dept_id' => 2, 'course_id' => 2, 'father' => 'Francis Fernandes', 'mother' => 'Maria Fernandes', 'phone' => '9845022310', 'email' => 'francis.fernandes@example.com', 'address' => '#67, Bannimantap, Mysuru - 570015'],

        // MECH (10)
        ['usn' => '1DM24ME001', 'first_name' => 'Vikram', 'last_name' => 'Chauhan', 'gender' => 'MALE', 'dob' => '2005-02-27', 'blood_group' => 'O+', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Kuldeep Chauhan', 'mother' => 'Sarita Chauhan', 'phone' => '9845033401', 'email' => 'kuldeep.chauhan@example.com', 'address' => '#39, Nazarbad, Mysuru - 570010'],
        ['usn' => '1DM24ME002', 'first_name' => 'Harshitha', 'last_name' => 'Prasad', 'gender' => 'FEMALE', 'dob' => '2005-07-21', 'blood_group' => 'A+', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Narayana Prasad', 'mother' => 'Vasantha Prasad', 'phone' => '9845033402', 'email' => 'narayana.prasad@example.com', 'address' => '#150, Ashokapuram, Mysuru - 570008'],
        ['usn' => '1DM24ME003', 'first_name' => 'Gautam', 'last_name' => 'Singhania', 'gender' => 'MALE', 'dob' => '2004-09-17', 'blood_group' => 'B+', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Vijay Singhania', 'mother' => 'Rekha Singhania', 'phone' => '9845033403', 'email' => 'vijay.singhania@example.com', 'address' => '#28, Vidyaranyapuram, Mysuru - 570008'],
        ['usn' => '1DM24ME004', 'first_name' => 'Sahana', 'last_name' => 'Bhat', 'gender' => 'FEMALE', 'dob' => '2005-05-09', 'blood_group' => 'AB+', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Keshava Bhat', 'mother' => 'Pushpa Bhat', 'phone' => '9845033404', 'email' => 'keshava.bhat@example.com', 'address' => '#9, Lakshmipuram, Mysuru - 570004'],
        ['usn' => '1DM24ME005', 'first_name' => 'Abhishek', 'last_name' => 'Mishra', 'gender' => 'MALE', 'dob' => '2005-10-31', 'blood_group' => 'O-', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Tribhuvan Mishra', 'mother' => 'Kamala Mishra', 'phone' => '9845033405', 'email' => 'tribhuvan.mishra@example.com', 'address' => '#77, Tilak Nagar, Mysuru - 570021'],
        ['usn' => '1DM24ME006', 'first_name' => 'Divya', 'last_name' => 'Ramesh', 'gender' => 'FEMALE', 'dob' => '2005-01-23', 'blood_group' => 'A-', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Ramesh Babu', 'mother' => 'Gayathri Ramesh', 'phone' => '9845033406', 'email' => 'ramesh.babu@example.com', 'address' => '#62, Mandi Mohalla, Mysuru - 570021'],
        ['usn' => '1DM24ME007', 'first_name' => 'Pranav', 'last_name' => 'Pandey', 'gender' => 'MALE', 'dob' => '2004-11-18', 'blood_group' => 'B-', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Brijesh Pandey', 'mother' => 'Sudha Pandey', 'phone' => '9845033407', 'email' => 'brijesh.pandey@example.com', 'address' => '#112, Vijayanagar 3rd Stage, Mysuru - 570017'],
        ['usn' => '1DM24ME008', 'first_name' => 'Ankita', 'last_name' => 'Dutta', 'gender' => 'FEMALE', 'dob' => '2005-08-04', 'blood_group' => 'O+', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Somnath Dutta', 'mother' => 'Ananya Dutta', 'phone' => '9845033408', 'email' => 'somnath.dutta@example.com', 'address' => '#45, TK Layout, Mysuru - 570009'],
        ['usn' => '1DM24ME009', 'first_name' => 'Rahul', 'last_name' => 'Shekhawat', 'gender' => 'MALE', 'dob' => '2005-04-26', 'blood_group' => 'AB-', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Mahipal Shekhawat', 'mother' => 'Kanchan Shekhawat', 'phone' => '9845033409', 'email' => 'mahipal.shekhawat@example.com', 'address' => '#83, JP Nagar, Mysuru - 570031'],
        ['usn' => '1DM24ME010', 'first_name' => 'Kavya', 'last_name' => 'Reddy', 'gender' => 'FEMALE', 'dob' => '2005-12-14', 'blood_group' => 'B+', 'dept_id' => 3, 'course_id' => 3, 'father' => 'Pratap Reddy', 'mother' => 'Sharmila Reddy', 'phone' => '9845033410', 'email' => 'pratap.reddy@example.com', 'address' => '#106, Bogadi Ring Road, Mysuru - 570026'],
    ];

    return $students;
}

function initialize_sqlite_db(PDO $pdo): void {
    // Enable WAL mode & foreign keys
    $pdo->exec("PRAGMA journal_mode = WAL;");
    $pdo->exec("PRAGMA foreign_keys = ON;");

    // Table creation queries in SQLite syntax
    $queries = [
        "CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            description TEXT
        );",
        "CREATE TABLE IF NOT EXISTS departments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS courses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            department_id INTEGER NOT NULL,
            code TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            degree_level TEXT DEFAULT 'Undergraduate',
            duration_years INTEGER DEFAULT 4,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
        );",
        "CREATE TABLE IF NOT EXISTS terms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            academic_year TEXT NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS term_links (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            route_slug TEXT NOT NULL,
            term_code TEXT NOT NULL,
            margin_top INTEGER DEFAULT 20,
            display_order INTEGER DEFAULT 1,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role_id INTEGER NOT NULL,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            email TEXT,
            phone TEXT,
            status TEXT DEFAULT 'ACTIVE',
            last_login_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
        );",
        "CREATE TABLE IF NOT EXISTS parents (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            father_name TEXT NOT NULL,
            mother_name TEXT NOT NULL,
            primary_mobile TEXT NOT NULL,
            alternate_mobile TEXT,
            email TEXT,
            address TEXT NOT NULL,
            occupation TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );",
        "CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            parent_id INTEGER NOT NULL,
            department_id INTEGER NOT NULL,
            course_id INTEGER NOT NULL,
            usn TEXT NOT NULL UNIQUE,
            roll_no TEXT,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            gender TEXT DEFAULT 'MALE',
            dob DATE NOT NULL,
            blood_group TEXT DEFAULT 'O+',
            admission_year INTEGER NOT NULL,
            current_sem INTEGER DEFAULT 4,
            photo_url TEXT DEFAULT 'assets/images/student_placeholder.svg',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE RESTRICT,
            FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT
        );",
        "CREATE TABLE IF NOT EXISTS subjects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            department_id INTEGER NOT NULL,
            course_id INTEGER NOT NULL,
            term_id INTEGER NOT NULL,
            semester INTEGER NOT NULL,
            code TEXT NOT NULL,
            title TEXT NOT NULL,
            credits REAL DEFAULT 4.0,
            type TEXT DEFAULT 'Theory',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            FOREIGN KEY (term_id) REFERENCES terms(id) ON DELETE CASCADE
        );",
        "CREATE TABLE IF NOT EXISTS attendance (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            subject_id INTEGER NOT NULL,
            term_id INTEGER NOT NULL,
            month TEXT NOT NULL,
            total_classes INTEGER NOT NULL DEFAULT 0,
            attended_classes INTEGER NOT NULL DEFAULT 0,
            percentage REAL NOT NULL DEFAULT 0.00,
            remarks TEXT,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
            FOREIGN KEY (term_id) REFERENCES terms(id) ON DELETE CASCADE
        );",
        "CREATE TABLE IF NOT EXISTS internal_marks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            subject_id INTEGER NOT NULL,
            term_id INTEGER NOT NULL,
            test_name TEXT NOT NULL,
            max_marks REAL NOT NULL DEFAULT 50.00,
            marks_obtained REAL NOT NULL DEFAULT 0.00,
            remarks TEXT,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
            FOREIGN KEY (term_id) REFERENCES terms(id) ON DELETE CASCADE
        );",
        "CREATE TABLE IF NOT EXISTS examination_results (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            term_id INTEGER NOT NULL,
            semester INTEGER NOT NULL,
            subject_id INTEGER NOT NULL,
            credits REAL NOT NULL DEFAULT 4.0,
            grade_letter TEXT NOT NULL,
            grade_points REAL NOT NULL DEFAULT 0.00,
            result_status TEXT DEFAULT 'P',
            sgpa REAL NOT NULL DEFAULT 0.00,
            cgpa REAL NOT NULL DEFAULT 0.00,
            exam_session TEXT NOT NULL,
            declared_date DATE NOT NULL,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
            FOREIGN KEY (term_id) REFERENCES terms(id) ON DELETE CASCADE
        );",
        "CREATE TABLE IF NOT EXISTS timetable (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            term_id INTEGER NOT NULL,
            department_id INTEGER NOT NULL,
            semester INTEGER NOT NULL,
            day_of_week TEXT NOT NULL,
            period_number INTEGER NOT NULL,
            start_time TEXT NOT NULL,
            end_time TEXT NOT NULL,
            subject_id INTEGER NOT NULL,
            room_no TEXT NOT NULL,
            faculty_name TEXT NOT NULL,
            FOREIGN KEY (term_id) REFERENCES terms(id) ON DELETE CASCADE,
            FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
            FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
        );",
        "CREATE TABLE IF NOT EXISTS notices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            is_login_notice INTEGER DEFAULT 1,
            priority INTEGER DEFAULT 1,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS announcements (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            term_id INTEGER,
            title TEXT NOT NULL,
            category TEXT DEFAULT 'Academic',
            description TEXT NOT NULL,
            attachment_file TEXT,
            is_published INTEGER DEFAULT 1,
            published_date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (term_id) REFERENCES terms(id) ON DELETE SET NULL
        );",
        "CREATE TABLE IF NOT EXISTS fee_records (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            term_id INTEGER NOT NULL,
            academic_year TEXT NOT NULL,
            fee_category TEXT NOT NULL,
            total_amount REAL NOT NULL,
            paid_amount REAL NOT NULL DEFAULT 0.00,
            balance_amount REAL NOT NULL DEFAULT 0.00,
            payment_status TEXT DEFAULT 'Pending',
            receipt_no TEXT,
            payment_date DATE,
            payment_mode TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
            FOREIGN KEY (term_id) REFERENCES terms(id) ON DELETE CASCADE
        );",
        "CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            username TEXT NOT NULL,
            attempt_time INTEGER NOT NULL
        );",
        "CREATE TABLE IF NOT EXISTS login_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            ip_address TEXT NOT NULL,
            user_agent TEXT,
            status TEXT NOT NULL,
            failure_reason TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );"
    ];

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }

    // Populate data
    seed_database($pdo);
}

function seed_database(PDO $pdo): void {
    // Check if already seeded
    $stmt = $pdo->query("SELECT COUNT(*) FROM roles");
    if ((int)$stmt->fetchColumn() > 0) {
        return; // Already seeded
    }

    // 1. Roles
    $roles = [
        [1, 'super_admin', 'Institutional Super Administrator with full control'],
        [2, 'department_admin', 'Department Head / Academic Coordinator'],
        [3, 'faculty', 'Teaching Faculty / Subject In-charge'],
        [4, 'parent', 'Student Parent / Guardian account with view privileges'],
        [5, 'student', 'Enrolled Student Account']
    ];
    $stmt = $pdo->prepare("INSERT INTO roles (id, name, description) VALUES (?, ?, ?)");
    foreach ($roles as $r) {
        $stmt->execute($r);
    }

    // 2. Departments
    $depts = [
        [1, 'CSE', 'Department of Computer Science & Engineering'],
        [2, 'ECE', 'Department of Electronics & Communication Engineering'],
        [3, 'MECH', 'Department of Mechanical Engineering']
    ];
    $stmt = $pdo->prepare("INSERT INTO departments (id, code, name) VALUES (?, ?, ?)");
    foreach ($depts as $d) {
        $stmt->execute($d);
    }

    // 3. Courses
    $courses = [
        [1, 1, 'BE-CSE', 'Bachelor of Engineering in Computer Science and Engineering', 'Undergraduate', 4],
        [2, 2, 'BE-ECE', 'Bachelor of Engineering in Electronics and Communication Engineering', 'Undergraduate', 4],
        [3, 3, 'BE-MECH', 'Bachelor of Engineering in Mechanical Engineering', 'Undergraduate', 4]
    ];
    $stmt = $pdo->prepare("INSERT INTO courses (id, department_id, code, name, degree_level, duration_years) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($courses as $c) {
        $stmt->execute($c);
    }

    // 4. Terms
    $terms = [
        [1, 'even_2026', 'Even term 2026', '2025-2026', '2026-01-15', '2026-06-30', 1],
        [2, 'supply_2025_2026', 'Supply/Summer term 2025-2026', '2025-2026', '2026-07-01', '2026-08-15', 1],
        [3, 'supply_part2_2025_2026', 'Supply/Summer term 2025-2026 Part 2', '2025-2026', '2026-08-16', '2026-09-15', 1]
    ];
    $stmt = $pdo->prepare("INSERT INTO terms (id, code, name, academic_year, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($terms as $t) {
        $stmt->execute($t);
    }

    // 5. Term Access Links
    $termLinks = [
        [1, 'Click here to access Even term 2026', '/parentseven/', 'even_2026', 20, 1, 1],
        [2, 'Click here to access Supply/Summer term 2025-2026', '/parentssupply/', 'supply_2025_2026', 40, 2, 1],
        [3, 'Click here to access Supply/Summer term 2025-2026 Part 2', '/parentssupplypart2/', 'supply_part2_2025_2026', 80, 3, 1]
    ];
    $stmt = $pdo->prepare("INSERT INTO term_links (id, title, route_slug, term_code, margin_top, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($termLinks as $tl) {
        $stmt->execute($tl);
    }

    // 6. Notices
    $notices = [
        [1, 'Welcome Notice', 'Welcome to the preview of the new mobile friendly parent portal', 1, 1, 1],
        [2, 'CIE Schedule', 'Second Continuous Internal Evaluation (CIE-2) will commence from March 24, 2026. Hall tickets available in portal.', 0, 2, 1],
        [3, 'Fee Payment Deadline', 'Even Term tuition and laboratory fee installments are payable without late fee up to February 28, 2026.', 0, 3, 1]
    ];
    $stmt = $pdo->prepare("INSERT INTO notices (id, title, content, is_login_notice, priority, is_active) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($notices as $n) {
        $stmt->execute($n);
    }

    // 7. Admin Accounts
    $adminPasswordHash = password_hash('admin@123', PASSWORD_DEFAULT);
    $admins = [
        [1, 'admin', $adminPasswordHash, 'admin@demo-institute.edu.in', '9845099991', 'ACTIVE'],
        [2, 'hod_cse', $adminPasswordHash, 'hod.cse@demo-institute.edu.in', '9845099992', 'ACTIVE']
    ];
    $stmt = $pdo->prepare("INSERT INTO users (role_id, username, password_hash, email, phone, status) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($admins as $a) {
        $stmt->execute($a);
    }

    // 8. Subjects (For 4th semester Even Term 2026, across departments)
    $subjects = [
        // CSE 4th Sem (Term 1)
        [1, 1, 1, 1, 4, '21CS41', 'Design and Analysis of Algorithms', 4.0, 'Theory'],
        [2, 1, 1, 1, 4, '21CS42', 'Operating Systems', 4.0, 'Theory'],
        [3, 1, 1, 1, 4, '21CS43', 'Database Management Systems', 4.0, 'Theory'],
        [4, 1, 1, 1, 4, '21CS44', 'Microcontroller and Embedded Systems', 3.0, 'Theory'],
        [5, 1, 1, 1, 4, '21CSL46', 'Design and Analysis of Algorithms Lab', 1.5, 'Lab'],
        [6, 1, 1, 1, 4, '21CSL47', 'Database Management Systems Lab', 1.5, 'Lab'],
        
        // ECE 4th Sem (Term 1)
        [7, 2, 2, 1, 4, '21EC41', 'Analog Circuits and Systems', 4.0, 'Theory'],
        [8, 2, 2, 1, 4, '21EC42', 'Signals and Systems', 4.0, 'Theory'],
        [9, 2, 2, 1, 4, '21EC43', 'Communication Theory', 4.0, 'Theory'],
        [10, 2, 2, 1, 4, '21EC44', 'VLSI Design and Technology', 3.0, 'Theory'],
        [11, 2, 2, 1, 4, '21ECL46', 'Analog Circuits Laboratory', 1.5, 'Lab'],

        // MECH 4th Sem (Term 1)
        [12, 3, 3, 1, 4, '21ME41', 'Applied Thermodynamics', 4.0, 'Theory'],
        [13, 3, 3, 1, 4, '21ME42', 'Kinematics of Machinery', 4.0, 'Theory'],
        [14, 3, 3, 1, 4, '21ME43', 'Fluid Mechanics and Machinery', 4.0, 'Theory'],
        [15, 3, 3, 1, 4, '21ME44', 'Manufacturing Process II', 3.0, 'Theory'],
        [16, 3, 3, 1, 4, '21MEL46', 'Fluid Mechanics & Machinery Lab', 1.5, 'Lab']
    ];
    $stmt = $pdo->prepare("INSERT INTO subjects (id, department_id, course_id, term_id, semester, code, title, credits, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($subjects as $s) {
        $stmt->execute($s);
    }

    // 9. Students, Parents, Users, Attendance, CIE Marks, Results, Fees, Timetable
    $studentsDataset = get_fictional_seed_dataset();

    $stmtUser = $pdo->prepare("INSERT INTO users (role_id, username, password_hash, email, phone, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtParent = $pdo->prepare("INSERT INTO parents (user_id, father_name, mother_name, primary_mobile, alternate_mobile, email, address, occupation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtStudent = $pdo->prepare("INSERT INTO students (user_id, parent_id, department_id, course_id, usn, roll_no, first_name, last_name, gender, dob, blood_group, admission_year, current_sem, photo_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtAtt = $pdo->prepare("INSERT INTO attendance (student_id, subject_id, term_id, month, total_classes, attended_classes, percentage, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtCie = $pdo->prepare("INSERT INTO internal_marks (student_id, subject_id, term_id, test_name, max_marks, marks_obtained, remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmtRes = $pdo->prepare("INSERT INTO examination_results (student_id, term_id, semester, subject_id, credits, grade_letter, grade_points, result_status, sgpa, cgpa, exam_session, declared_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtFee = $pdo->prepare("INSERT INTO fee_records (student_id, term_id, academic_year, fee_category, total_amount, paid_amount, balance_amount, payment_status, receipt_no, payment_date, payment_mode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $deptSubjects = [
        1 => [1, 2, 3, 4, 5, 6],    // CSE
        2 => [7, 8, 9, 10, 11],     // ECE
        3 => [12, 13, 14, 15, 16]   // MECH
    ];

    $studentIdCounter = 1;

    foreach ($studentsDataset as $idx => $st) {
        // Password hash of YYYY-MM-DD
        $dobHash = password_hash($st['dob'], PASSWORD_DEFAULT);
        
        // 1. Insert user record for Student/Parent
        // Role 4 = parent
        $stmtUser->execute([4, $st['usn'], $dobHash, $st['email'], $st['phone'], 'ACTIVE']);
        $userId = (int)$pdo->lastInsertId();

        // 2. Insert Parent record
        $stmtParent->execute([
            $userId,
            $st['father'],
            $st['mother'],
            $st['phone'],
            '98450' . rand(10000, 99999),
            $st['email'],
            $st['address'],
            'Private Service / Business'
        ]);
        $parentId = (int)$pdo->lastInsertId();

        // 3. Insert Student record
        $rollNo = sprintf('%02d', $idx + 1);
        $stmtStudent->execute([
            $userId,
            $parentId,
            $st['dept_id'],
            $st['course_id'],
            $st['usn'],
            $rollNo,
            $st['first_name'],
            $st['last_name'],
            $st['gender'],
            $st['dob'],
            $st['blood_group'],
            2024,
            4,
            'assets/images/student_placeholder.svg'
        ]);
        $currStudentId = (int)$pdo->lastInsertId();

        // 4. Attendance & CIE for student's subjects
        $assignedSubs = $deptSubjects[$st['dept_id']];
        $isShortageStudent = ($st['usn'] === '1DM24CS002'); // Student 2 has deliberate shortage < 75% for testing

        foreach ($assignedSubs as $subId) {
            // Attendance
            $totalClasses = 40;
            if ($isShortageStudent && ($subId == 2 || $subId == 3)) {
                $attended = 26; // 65% - triggers warning!
                $pct = 65.00;
                $rem = 'Shortage Warning';
            } else {
                $attended = rand(34, 39);
                $pct = round(($attended / $totalClasses) * 100, 2);
                $rem = 'Good';
            }
            $stmtAtt->execute([$currStudentId, $subId, 1, 'Overall', $totalClasses, $attended, $pct, $rem]);

            // CIE-1
            $cie1Marks = rand(36, 48);
            $stmtCie->execute([$currStudentId, $subId, 1, 'CIE-1', 50.00, $cie1Marks, 'Conducted Jan 2026']);

            // CIE-2
            $cie2Marks = rand(38, 49);
            $stmtCie->execute([$currStudentId, $subId, 1, 'CIE-2', 50.00, $cie2Marks, 'Conducted Feb 2026']);

            // Assignment
            $stmtCie->execute([$currStudentId, $subId, 1, 'Assignment', 10.00, rand(8, 10), 'Submitted']);

            // Examination Results (for previous 3rd Semester)
            $grades = ['S', 'A', 'B', 'C'];
            $gPoints = ['S' => 10.0, 'A' => 9.0, 'B' => 8.0, 'C' => 7.0];
            $selectedG = $grades[array_rand($grades)];
            $stmtRes->execute([
                $currStudentId,
                1,
                3,
                $subId,
                4.0,
                $selectedG,
                $gPoints[$selectedG],
                'P',
                8.75,
                8.82,
                'Nov/Dec 2025 Regular',
                '2026-01-10'
            ]);
        }

        // 5. Fee Records
        $totalFee = 95000.00;
        if ($idx % 3 === 0) {
            // Fully paid
            $stmtFee->execute([$currStudentId, 1, '2025-2026', 'Even Term Tuition & Facilities Fee', $totalFee, $totalFee, 0.00, 'Paid', 'REC-2026-' . sprintf('%04d', $currStudentId), '2026-01-20', 'Online NetBanking']);
        } elseif ($idx % 3 === 1) {
            // Partial
            $stmtFee->execute([$currStudentId, 1, '2025-2026', 'Even Term Tuition & Facilities Fee', $totalFee, 50000.00, 45000.00, 'Partial', 'REC-2026-' . sprintf('%04d', $currStudentId), '2026-01-22', 'UPI / QR']);
        } else {
            // Pending
            $stmtFee->execute([$currStudentId, 1, '2025-2026', 'Even Term Tuition & Facilities Fee', $totalFee, 0.00, $totalFee, 'Pending', null, null, null]);
        }

        $studentIdCounter++;
    }

    // 10. Timetable Entries (Weekly Schedule for CSE Sem 4)
    $ttStmt = $pdo->prepare("INSERT INTO timetable (term_id, department_id, semester, day_of_week, period_number, start_time, end_time, subject_id, room_no, faculty_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $scheduleData = [
        ['Monday', 1, '09:00:00', '10:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'],
        ['Monday', 2, '10:00:00', '11:00:00', 2, 'LH-201', 'Prof. Priya Rao'],
        ['Monday', 3, '11:15:00', '12:15:00', 3, 'LH-201', 'Dr. Anand Joshi'],
        ['Monday', 4, '12:15:00', '01:15:00', 4, 'LH-201', 'Prof. Sunita Patil'],
        ['Monday', 5, '02:00:00', '04:00:00', 5, 'CS-Lab 2', 'Dr. Ramesh / Prof. Priya'],

        ['Tuesday', 1, '09:00:00', '10:00:00', 3, 'LH-201', 'Dr. Anand Joshi'],
        ['Tuesday', 2, '10:00:00', '11:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'],
        ['Tuesday', 3, '11:15:00', '12:15:00', 2, 'LH-201', 'Prof. Priya Rao'],
        ['Tuesday', 4, '12:15:00', '01:15:00', 4, 'LH-201', 'Prof. Sunita Patil'],
        ['Tuesday', 5, '02:00:00', '04:00:00', 6, 'CS-Lab 1', 'Dr. Anand / Team'],

        ['Wednesday', 1, '09:00:00', '10:00:00', 2, 'LH-201', 'Prof. Priya Rao'],
        ['Wednesday', 2, '10:00:00', '11:00:00', 4, 'LH-201', 'Prof. Sunita Patil'],
        ['Wednesday', 3, '11:15:00', '12:15:00', 1, 'LH-201', 'Dr. Ramesh Kumar'],
        ['Wednesday', 4, '12:15:00', '01:15:00', 3, 'LH-201', 'Dr. Anand Joshi'],
        ['Wednesday', 5, '02:00:00', '04:00:00', 5, 'CS-Lab 2', 'Lab Faculty'],

        ['Thursday', 1, '09:00:00', '10:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'],
        ['Thursday', 2, '10:00:00', '11:00:00', 3, 'LH-201', 'Dr. Anand Joshi'],
        ['Thursday', 3, '11:15:00', '12:15:00', 2, 'LH-201', 'Prof. Priya Rao'],
        ['Thursday', 4, '12:15:00', '01:15:00', 4, 'LH-201', 'Prof. Sunita Patil'],
        ['Thursday', 5, '02:00:00', '03:30:00', 1, 'LH-201', 'Tutorial Group A'],

        ['Friday', 1, '09:00:00', '10:00:00', 4, 'LH-201', 'Prof. Sunita Patil'],
        ['Friday', 2, '10:00:00', '11:00:00', 2, 'LH-201', 'Prof. Priya Rao'],
        ['Friday', 3, '11:15:00', '12:15:00', 1, 'LH-201', 'Dr. Ramesh Kumar'],
        ['Friday', 4, '12:15:00', '01:15:00', 3, 'LH-201', 'Dr. Anand Joshi'],
        ['Friday', 5, '02:00:00', '04:00:00', 6, 'CS-Lab 1', 'Dr. Anand / Team'],

        ['Saturday', 1, '09:00:00', '10:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'],
        ['Saturday', 2, '10:00:00', '11:00:00', 3, 'LH-201', 'Dr. Anand Joshi'],
        ['Saturday', 3, '11:15:00', '12:45:00', 2, 'LH-201', 'Seminar / Activity']
    ];

    foreach ($scheduleData as $slot) {
        $ttStmt->execute([1, 1, 4, $slot[0], $slot[1], $slot[2], $slot[3], $slot[4], $slot[5], $slot[6]]);
    }

    // 11. Announcements (Circulars with attachments)
    $annStmt = $pdo->prepare("INSERT INTO announcements (term_id, title, category, description, attachment_file, is_published, published_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $annList = [
        [1, 'Schedule for Continuous Internal Evaluation (CIE-2)', 'Examinations', 'The second Continuous Internal Evaluation (CIE-2) for B.E. 4th semester students is scheduled from March 24th to March 27th, 2026. All students must report to their assigned examination halls 15 minutes prior to commencement.', 'CIE_2_Timetable_Schedule.pdf', 1, '2026-02-10'],
        [1, 'Submission of Scholarship Applications for Academic Year 2025-26', 'Scholarship', 'Eligible students under state and central post-matric scholarship schemes must complete their e-attestation and submit the verified application copies to the academic section by March 15th, 2026.', 'Scholarship_Guidelines_2025_26.pdf', 1, '2026-02-05'],
        [1, 'Annual Technical Symposium - INNOVATE 2026 Registration Open', 'Events', 'Demo Institute of Engineering invites students to participate in our National-level Technical Fest INNOVATE 2026 featuring Hackathons, Robotics, Paper presentations, and Project Expos. Exciting prizes to be won.', 'Innovate_2026_Brochure.pdf', 1, '2026-01-28'],
        [1, 'Industrial Visit to National Aerospace & Tech Laboratories', 'Department', 'Department of Computer Science & Engineering is organizing a 2-day industrial visit for 4th-semester students to National Aerospace Laboratories, Bangalore on March 30th and 31st, 2026.', 'Industrial_Visit_Consent_Form.pdf', 1, '2026-01-25']
    ];

    foreach ($annList as $ann) {
        $annStmt->execute($ann);
    }
}

// Generate seed.sql for MySQL/XAMPP import
function generate_mysql_seed_sql_file(): void {
    $seedFilePath = __DIR__ . '/seed.sql';
    $studentsDataset = get_fictional_seed_dataset();
    $adminPasswordHash = password_hash('admin@123', PASSWORD_DEFAULT);

    $sql = "-- ====================================================================\n";
    $sql .= "-- Seed Data for Demo Institute of Engineering - Parent Portal\n";
    $sql .= "-- MySQL / XAMPP Compatible (portal_demo)\n";
    $sql .= "-- ====================================================================\n\n";
    $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    // Roles
    $sql .= "-- Roles\n";
    $sql .= "INSERT INTO `roles` (`id`, `name`, `description`) VALUES\n";
    $sql .= "(1, 'super_admin', 'Institutional Super Administrator with full control'),\n";
    $sql .= "(2, 'department_admin', 'Department Head / Academic Coordinator'),\n";
    $sql .= "(3, 'faculty', 'Teaching Faculty / Subject In-charge'),\n";
    $sql .= "(4, 'parent', 'Student Parent / Guardian account with view privileges'),\n";
    $sql .= "(5, 'student', 'Enrolled Student Account')\n";
    $sql .= "ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);\n\n";

    // Departments
    $sql .= "-- Departments\n";
    $sql .= "INSERT INTO `departments` (`id`, `code`, `name`) VALUES\n";
    $sql .= "(1, 'CSE', 'Department of Computer Science & Engineering'),\n";
    $sql .= "(2, 'ECE', 'Department of Electronics & Communication Engineering'),\n";
    $sql .= "(3, 'MECH', 'Department of Mechanical Engineering')\n";
    $sql .= "ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);\n\n";

    // Courses
    $sql .= "-- Courses\n";
    $sql .= "INSERT INTO `courses` (`id`, `department_id`, `code`, `name`, `degree_level`, `duration_years`) VALUES\n";
    $sql .= "(1, 1, 'BE-CSE', 'Bachelor of Engineering in Computer Science and Engineering', 'Undergraduate', 4),\n";
    $sql .= "(2, 2, 'BE-ECE', 'Bachelor of Engineering in Electronics and Communication Engineering', 'Undergraduate', 4),\n";
    $sql .= "(3, 3, 'BE-MECH', 'Bachelor of Engineering in Mechanical Engineering', 'Undergraduate', 4)\n";
    $sql .= "ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);\n\n";

    // Terms
    $sql .= "-- Terms\n";
    $sql .= "INSERT INTO `terms` (`id`, `code`, `name`, `academic_year`, `start_date`, `end_date`, `is_active`) VALUES\n";
    $sql .= "(1, 'even_2026', 'Even term 2026', '2025-2026', '2026-01-15', '2026-06-30', 1),\n";
    $sql .= "(2, 'supply_2025_2026', 'Supply/Summer term 2025-2026', '2025-2026', '2026-07-01', '2026-08-15', 1),\n";
    $sql .= "(3, 'supply_part2_2025_2026', 'Supply/Summer term 2025-2026 Part 2', '2025-2026', '2026-08-16', '2026-09-15', 1)\n";
    $sql .= "ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);\n\n";

    // Term Links
    $sql .= "-- Term Links (Login Page Sub-Portals)\n";
    $sql .= "INSERT INTO `term_links` (`id`, `title`, `route_slug`, `term_code`, `margin_top`, `display_order`, `is_active`) VALUES\n";
    $sql .= "(1, 'Click here to access Even term 2026', '/parentseven/', 'even_2026', 20, 1, 1),\n";
    $sql .= "(2, 'Click here to access Supply/Summer term 2025-2026', '/parentssupply/', 'supply_2025_2026', 40, 2, 1),\n";
    $sql .= "(3, 'Click here to access Supply/Summer term 2025-2026 Part 2', '/parentssupplypart2/', 'supply_part2_2025_2026', 80, 3, 1)\n";
    $sql .= "ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);\n\n";

    // Notices
    $sql .= "-- Notices\n";
    $sql .= "INSERT INTO `notices` (`id`, `title`, `content`, `is_login_notice`, `priority`, `is_active`) VALUES\n";
    $sql .= "(1, 'Welcome Notice', 'Welcome to the preview of the new mobile friendly parent portal', 1, 1, 1),\n";
    $sql .= "(2, 'CIE Schedule', 'Second Continuous Internal Evaluation (CIE-2) will commence from March 24, 2026. Hall tickets available in portal.', 0, 2, 1),\n";
    $sql .= "(3, 'Fee Payment Deadline', 'Even Term tuition and laboratory fee installments are payable without late fee up to February 28, 2026.', 0, 3, 1)\n";
    $sql .= "ON DUPLICATE KEY UPDATE `content`=VALUES(`content`);\n\n";

    // Admin Users
    $sql .= "-- Admin Users (Password: admin@123)\n";
    $sql .= "INSERT INTO `users` (`id`, `role_id`, `username`, `password_hash`, `email`, `phone`, `status`) VALUES\n";
    $sql .= "(1, 1, 'admin', '{$adminPasswordHash}', 'admin@demo-institute.edu.in', '9845099991', 'ACTIVE'),\n";
    $sql .= "(2, 2, 'hod_cse', '{$adminPasswordHash}', 'hod.cse@demo-institute.edu.in', '9845099992', 'ACTIVE')\n";
    $sql .= "ON DUPLICATE KEY UPDATE `password_hash`=VALUES(`password_hash`);\n\n";

    // Subjects
    $sql .= "-- Subjects\n";
    $sql .= "INSERT INTO `subjects` (`id`, `department_id`, `course_id`, `term_id`, `semester`, `code`, `title`, `credits`, `type`) VALUES\n";
    $sql .= "(1, 1, 1, 1, 4, '21CS41', 'Design and Analysis of Algorithms', 4.0, 'Theory'),\n";
    $sql .= "(2, 1, 1, 1, 4, '21CS42', 'Operating Systems', 4.0, 'Theory'),\n";
    $sql .= "(3, 1, 1, 1, 4, '21CS43', 'Database Management Systems', 4.0, 'Theory'),\n";
    $sql .= "(4, 1, 1, 1, 4, '21CS44', 'Microcontroller and Embedded Systems', 3.0, 'Theory'),\n";
    $sql .= "(5, 1, 1, 1, 4, '21CSL46', 'Design and Analysis of Algorithms Lab', 1.5, 'Lab'),\n";
    $sql .= "(6, 1, 1, 1, 4, '21CSL47', 'Database Management Systems Lab', 1.5, 'Lab'),\n";
    $sql .= "(7, 2, 2, 1, 4, '21EC41', 'Analog Circuits and Systems', 4.0, 'Theory'),\n";
    $sql .= "(8, 2, 2, 1, 4, '21EC42', 'Signals and Systems', 4.0, 'Theory'),\n";
    $sql .= "(9, 2, 2, 1, 4, '21EC43', 'Communication Theory', 4.0, 'Theory'),\n";
    $sql .= "(10, 2, 2, 1, 4, '21EC44', 'VLSI Design and Technology', 3.0, 'Theory'),\n";
    $sql .= "(11, 2, 2, 1, 4, '21ECL46', 'Analog Circuits Laboratory', 1.5, 'Lab'),\n";
    $sql .= "(12, 3, 3, 1, 4, '21ME41', 'Applied Thermodynamics', 4.0, 'Theory'),\n";
    $sql .= "(13, 3, 3, 1, 4, '21ME42', 'Kinematics of Machinery', 4.0, 'Theory'),\n";
    $sql .= "(14, 3, 3, 1, 4, '21ME43', 'Fluid Mechanics and Machinery', 4.0, 'Theory'),\n";
    $sql .= "(15, 3, 3, 1, 4, '21ME44', 'Manufacturing Process II', 3.0, 'Theory'),\n";
    $sql .= "(16, 3, 3, 1, 4, '21MEL46', 'Fluid Mechanics & Machinery Lab', 1.5, 'Lab')\n";
    $sql .= "ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);\n\n";

    // 30 Students, Parents, Users
    $sql .= "-- Users, Parents, Students Data\n";
    $deptSubjects = [
        1 => [1, 2, 3, 4, 5, 6],
        2 => [7, 8, 9, 10, 11],
        3 => [12, 13, 14, 15, 16]
    ];

    $userRows = [];
    $parentRows = [];
    $studentRows = [];
    $attRows = [];
    $cieRows = [];
    $resRows = [];
    $feeRows = [];

    $userIdBase = 3;
    $parentIdBase = 1;
    $studentIdBase = 1;

    foreach ($studentsDataset as $idx => $st) {
        $uId = $userIdBase + $idx;
        $pId = $parentIdBase + $idx;
        $sId = $studentIdBase + $idx;
        $dobHash = password_hash($st['dob'], PASSWORD_DEFAULT);

        // User
        $userRows[] = "({$uId}, 4, '{$st['usn']}', '{$dobHash}', '{$st['email']}', '{$st['phone']}', 'ACTIVE')";

        // Parent
        $parentRows[] = "({$pId}, {$uId}, '{$st['father']}', '{$st['mother']}', '{$st['phone']}', '9845011223', '{$st['email']}', '{$st['address']}', 'Business / Private Service')";

        // Student
        $roll = sprintf('%02d', $idx + 1);
        $studentRows[] = "({$sId}, {$uId}, {$pId}, {$st['dept_id']}, {$st['course_id']}, '{$st['usn']}', '{$roll}', '{$st['first_name']}', '{$st['last_name']}', '{$st['gender']}', '{$st['dob']}', '{$st['blood_group']}', 2024, 4, 'assets/images/student_placeholder.svg')";

        // Attendance & CIE & Results
        $assigned = $deptSubjects[$st['dept_id']];
        $isShortage = ($st['usn'] === '1DM24CS002');

        foreach ($assigned as $subId) {
            $totalCls = 40;
            if ($isShortage && ($subId == 2 || $subId == 3)) {
                $attCls = 26;
                $pct = 65.00;
                $rem = 'Shortage Warning';
            } else {
                $attCls = 36;
                $pct = 90.00;
                $rem = 'Good';
            }
            $attRows[] = "({$sId}, {$subId}, 1, 'Overall', {$totalCls}, {$attCls}, {$pct}, '{$rem}')";
            $cieRows[] = "({$sId}, {$subId}, 1, 'CIE-1', 50.00, 44.00, 'Conducted Jan 2026')";
            $cieRows[] = "({$sId}, {$subId}, 1, 'CIE-2', 50.00, 46.00, 'Conducted Feb 2026')";
            $cieRows[] = "({$sId}, {$subId}, 1, 'Assignment', 10.00, 9.00, 'Submitted')";

            $resRows[] = "({$sId}, 1, 3, {$subId}, 4.0, 'A', 9.00, 'P', 8.75, 8.82, 'Nov/Dec 2025 Regular', '2026-01-10')";
        }

        // Fees
        if ($idx % 3 === 0) {
            $feeRows[] = "({$sId}, 1, '2025-2026', 'Even Term Tuition & Facilities Fee', 95000.00, 95000.00, 0.00, 'Paid', 'REC-2026-" . sprintf('%04d', $sId) . "', '2026-01-20', 'Online NetBanking')";
        } elseif ($idx % 3 === 1) {
            $feeRows[] = "({$sId}, 1, '2025-2026', 'Even Term Tuition & Facilities Fee', 95000.00, 50000.00, 45000.00, 'Partial', 'REC-2026-" . sprintf('%04d', $sId) . "', '2026-01-22', 'UPI / QR')";
        } else {
            $feeRows[] = "({$sId}, 1, '2025-2026', 'Even Term Tuition & Facilities Fee', 95000.00, 0.00, 95000.00, 'Pending', NULL, NULL, NULL)";
        }
    }

    $sql .= "INSERT INTO `users` (`id`, `role_id`, `username`, `password_hash`, `email`, `phone`, `status`) VALUES\n" . implode(",\n", $userRows) . ";\n\n";
    $sql .= "INSERT INTO `parents` (`id`, `user_id`, `father_name`, `mother_name`, `primary_mobile`, `alternate_mobile`, `email`, `address`, `occupation`) VALUES\n" . implode(",\n", $parentRows) . ";\n\n";
    $sql .= "INSERT INTO `students` (`id`, `user_id`, `parent_id`, `department_id`, `course_id`, `usn`, `roll_no`, `first_name`, `last_name`, `gender`, `dob`, `blood_group`, `admission_year`, `current_sem`, `photo_url`) VALUES\n" . implode(",\n", $studentRows) . ";\n\n";
    $sql .= "INSERT INTO `attendance` (`student_id`, `subject_id`, `term_id`, `month`, `total_classes`, `attended_classes`, `percentage`, `remarks`) VALUES\n" . implode(",\n", $attRows) . ";\n\n";
    $sql .= "INSERT INTO `internal_marks` (`student_id`, `subject_id`, `term_id`, `test_name`, `max_marks`, `marks_obtained`, `remarks`) VALUES\n" . implode(",\n", $cieRows) . ";\n\n";
    $sql .= "INSERT INTO `examination_results` (`student_id`, `term_id`, `semester`, `subject_id`, `credits`, `grade_letter`, `grade_points`, `result_status`, `sgpa`, `cgpa`, `exam_session`, `declared_date`) VALUES\n" . implode(",\n", $resRows) . ";\n\n";
    $sql .= "INSERT INTO `fee_records` (`student_id`, `term_id`, `academic_year`, `fee_category`, `total_amount`, `paid_amount`, `balance_amount`, `payment_status`, `receipt_no`, `payment_date`, `payment_mode`) VALUES\n" . implode(",\n", $feeRows) . ";\n\n";

    // Timetable
    $sql .= "-- Timetable Schedule\n";
    $sql .= "INSERT INTO `timetable` (`term_id`, `department_id`, `semester`, `day_of_week`, `period_number`, `start_time`, `end_time`, `subject_id`, `room_no`, `faculty_name`) VALUES\n";
    $sql .= "(1, 1, 4, 'Monday', 1, '09:00:00', '10:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'),\n";
    $sql .= "(1, 1, 4, 'Monday', 2, '10:00:00', '11:00:00', 2, 'LH-201', 'Prof. Priya Rao'),\n";
    $sql .= "(1, 1, 4, 'Monday', 3, '11:15:00', '12:15:00', 3, 'LH-201', 'Dr. Anand Joshi'),\n";
    $sql .= "(1, 1, 4, 'Monday', 4, '12:15:00', '01:15:00', 4, 'LH-201', 'Prof. Sunita Patil'),\n";
    $sql .= "(1, 1, 4, 'Monday', 5, '02:00:00', '04:00:00', 5, 'CS-Lab 2', 'Dr. Ramesh / Prof. Priya'),\n";
    $sql .= "(1, 1, 4, 'Tuesday', 1, '09:00:00', '10:00:00', 3, 'LH-201', 'Dr. Anand Joshi'),\n";
    $sql .= "(1, 1, 4, 'Tuesday', 2, '10:00:00', '11:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'),\n";
    $sql .= "(1, 1, 4, 'Tuesday', 3, '11:15:00', '12:15:00', 2, 'LH-201', 'Prof. Priya Rao'),\n";
    $sql .= "(1, 1, 4, 'Tuesday', 4, '12:15:00', '01:15:00', 4, 'LH-201', 'Prof. Sunita Patil'),\n";
    $sql .= "(1, 1, 4, 'Tuesday', 5, '02:00:00', '04:00:00', 6, 'CS-Lab 1', 'Dr. Anand / Team'),\n";
    $sql .= "(1, 1, 4, 'Wednesday', 1, '09:00:00', '10:00:00', 2, 'LH-201', 'Prof. Priya Rao'),\n";
    $sql .= "(1, 1, 4, 'Wednesday', 2, '10:00:00', '11:00:00', 4, 'LH-201', 'Prof. Sunita Patil'),\n";
    $sql .= "(1, 1, 4, 'Wednesday', 3, '11:15:00', '12:15:00', 1, 'LH-201', 'Dr. Ramesh Kumar'),\n";
    $sql .= "(1, 1, 4, 'Wednesday', 4, '12:15:00', '01:15:00', 3, 'LH-201', 'Dr. Anand Joshi'),\n";
    $sql .= "(1, 1, 4, 'Wednesday', 5, '02:00:00', '04:00:00', 5, 'CS-Lab 2', 'Lab Faculty'),\n";
    $sql .= "(1, 1, 4, 'Thursday', 1, '09:00:00', '10:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'),\n";
    $sql .= "(1, 1, 4, 'Thursday', 2, '10:00:00', '11:00:00', 3, 'LH-201', 'Dr. Anand Joshi'),\n";
    $sql .= "(1, 1, 4, 'Thursday', 3, '11:15:00', '12:15:00', 2, 'LH-201', 'Prof. Priya Rao'),\n";
    $sql .= "(1, 1, 4, 'Thursday', 4, '12:15:00', '01:15:00', 4, 'LH-201', 'Prof. Sunita Patil'),\n";
    $sql .= "(1, 1, 4, 'Thursday', 5, '02:00:00', '03:30:00', 1, 'LH-201', 'Tutorial Group A'),\n";
    $sql .= "(1, 1, 4, 'Friday', 1, '09:00:00', '10:00:00', 4, 'LH-201', 'Prof. Sunita Patil'),\n";
    $sql .= "(1, 1, 4, 'Friday', 2, '10:00:00', '11:00:00', 2, 'LH-201', 'Prof. Priya Rao'),\n";
    $sql .= "(1, 1, 4, 'Friday', 3, '11:15:00', '12:15:00', 1, 'LH-201', 'Dr. Ramesh Kumar'),\n";
    $sql .= "(1, 1, 4, 'Friday', 4, '12:15:00', '01:15:00', 3, 'LH-201', 'Dr. Anand Joshi'),\n";
    $sql .= "(1, 1, 4, 'Friday', 5, '02:00:00', '04:00:00', 6, 'CS-Lab 1', 'Dr. Anand / Team'),\n";
    $sql .= "(1, 1, 4, 'Saturday', 1, '09:00:00', '10:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'),\n";
    $sql .= "(1, 1, 4, 'Saturday', 2, '10:00:00', '11:00:00', 3, 'LH-201', 'Dr. Anand Joshi'),\n";
    $sql .= "(1, 1, 4, 'Saturday', 3, '11:15:00', '12:45:00', 2, 'LH-201', 'Seminar / Activity');\n\n";

    // Announcements
    $sql .= "-- Announcements\n";
    $sql .= "INSERT INTO `announcements` (`term_id`, `title`, `category`, `description`, `attachment_file`, `is_published`, `published_date`) VALUES\n";
    $sql .= "(1, 'Schedule for Continuous Internal Evaluation (CIE-2)', 'Examinations', 'The second Continuous Internal Evaluation (CIE-2) for B.E. 4th semester students is scheduled from March 24th to March 27th, 2026. All students must report to their assigned examination halls 15 minutes prior to commencement.', 'CIE_2_Timetable_Schedule.pdf', 1, '2026-02-10'),\n";
    $sql .= "(1, 'Submission of Scholarship Applications for Academic Year 2025-26', 'Scholarship', 'Eligible students under state and central post-matric scholarship schemes must complete their e-attestation and submit the verified application copies to the academic section by March 15th, 2026.', 'Scholarship_Guidelines_2025_26.pdf', 1, '2026-02-05'),\n";
    $sql .= "(1, 'Annual Technical Symposium - INNOVATE 2026 Registration Open', 'Events', 'Demo Institute of Engineering invites students to participate in our National-level Technical Fest INNOVATE 2026 featuring Hackathons, Robotics, Paper presentations, and Project Expos. Exciting prizes to be won.', 'Innovate_2026_Brochure.pdf', 1, '2026-01-28'),\n";
    $sql .= "(1, 'Industrial Visit to National Aerospace & Tech Laboratories', 'Department', 'Department of Computer Science & Engineering is organizing a 2-day industrial visit for 4th-semester students to National Aerospace Laboratories, Bangalore on March 30th and 31st, 2026.', 'Industrial_Visit_Consent_Form.pdf', 1, '2026-01-25');\n\n";

    $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    file_put_contents($seedFilePath, $sql);
}

// If invoked from CLI directly, generate seed.sql and test initialization
if (php_sapi_name() === 'cli' && isset($argv[0]) && realpath($argv[0]) === realpath(__FILE__)) {
    echo "Generating database/seed.sql...\n";
    generate_mysql_seed_sql_file();
    echo "seed.sql generated successfully!\n";

    echo "Testing SQLite initialization...\n";
    $sqliteDb = __DIR__ . '/portal_demo.sqlite';
    if (file_exists($sqliteDb)) {
        unlink($sqliteDb);
    }
    $pdo = new PDO('sqlite:' . $sqliteDb);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    initialize_sqlite_db($pdo);
    echo "SQLite database initialized and seeded successfully!\n";
}
