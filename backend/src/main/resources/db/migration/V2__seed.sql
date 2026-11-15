-- ====================================================================
-- Seed Data for Multi-Tenant Parent Portal
-- Tenants:
-- 1. Demo Institute of Engineering (slug: die, Contineo style)
-- 2. Apex Institute of Technology (slug: ait, Emerald modern style)
-- ====================================================================

-- 1. Tenants
INSERT INTO tenants (id, slug, name, code, domain, is_active) VALUES
(1, 'die', 'Demo Institute of Engineering', 'DIE', 'parents.demo-institute.edu.in', TRUE),
(2, 'ait', 'Apex Institute of Technology', 'AIT', 'parents.apex-tech.edu.in', TRUE);

-- 2. Tenant Settings
INSERT INTO tenant_settings (id, tenant_id, theme_json, welcome_text, notice_text, footer_text, contact_email, contact_phone, default_language) VALUES
(1, 1, '{"primary":"#1e88e5","primaryHover":"#1565c0","bgLeft":"#b3dff8","bgRight":"#242466","navy":"#1b2a47","crestIcon":"🏛️","fontFamily":"-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif","borderRadius":"8px","badgeColor":"#ef4444"}', 'Welcome to the official Contineo Institutional Parent Portal of Demo Institute of Engineering. An Autonomous Institution approved by AICTE, New Delhi and accredited by NAAC with ''A+'' Grade.', 'Welcome to the preview of the new mobile friendly parent portal. Even term 2026 academic sessions are now active.', 'Copyright © 2026 Demo Institute of Engineering. Powered by Contineo Platform (Demo).', 'parents@demo-institute.edu.in', '+91 821 2480475', 'en'),
(2, 2, '{"primary":"#059669","primaryHover":"#047857","bgLeft":"#d1fae5","bgRight":"#064e3b","navy":"#065f46","crestIcon":"🎓","fontFamily":"Inter, -apple-system, BlinkMacSystemFont, sans-serif","borderRadius":"12px","badgeColor":"#f59e0b"}', 'Welcome to Apex Institute of Technology Parent & Student Digital Experience Gateway. Premier Autonomous University for Artificial Intelligence and Next-Gen Engineering.', 'Spring 2026 Hackathon and Term 2 classes commence from March 1st. Check your student fee and timetable schedule.', 'Copyright © 2026 Apex Institute of Technology. All Rights Reserved.', 'support@apex-tech.edu.in', '+91 80 41238900', 'en');

-- 3. Login Schemas
INSERT INTO login_schemas (id, tenant_id, identifier_field, credential_type, allowed_terms_json, rate_limit_max, lockout_minutes) VALUES
(1, 1, 'USN', 'DOB_DROPDOWN', '["even_2026", "supply_2025_2026", "supply_part2_2025_2026"]', 5, 15),
(2, 2, 'USN', 'PASSWORD', '["spring_2026", "summer_2026"]', 5, 15);

-- 4. Global Modules Registry
INSERT INTO modules (id, module_key, default_label, default_icon, default_order, default_route) VALUES
(1, 'dashboard', 'Dashboard', 'LayoutDashboard', 1, '/dashboard'),
(2, 'profile', 'Student Profile', 'User', 2, '/profile'),
(3, 'attendance', 'Attendance', 'CalendarCheck', 3, '/attendance'),
(4, 'cie', 'Internal Marks (CIE)', 'FileSpreadsheet', 4, '/cie'),
(5, 'results', 'Semester Results', 'Award', 5, '/results'),
(6, 'timetable', 'Timetable', 'Clock', 6, '/timetable'),
(7, 'calendar', 'Academic Calendar', 'Calendar', 7, '/calendar'),
(8, 'announcements', 'Announcements', 'Bell', 8, '/announcements'),
(9, 'fees', 'Fee Records', 'CreditCard', 9, '/fees'),
(10, 'messaging', 'Faculty Mentors', 'MessageSquare', 10, '/messaging'),
(11, 'leave', 'Leave Intimation', 'FileText', 11, '/leave'),
(12, 'feedback', 'Grievance & Feedback', 'LifeBuoy', 12, '/feedback');

-- 5. Tenant Modules
INSERT INTO tenant_modules (id, tenant_id, module_id, is_enabled, custom_label, display_order, allowed_roles) VALUES
-- Tenant 1 (DIE) Modules
(1, 1, 1, TRUE, 'Dashboard', 1, 'PARENT,ADMIN'),
(2, 1, 2, TRUE, 'Student Profile', 2, 'PARENT,ADMIN'),
(3, 1, 3, TRUE, 'Attendance', 3, 'PARENT,ADMIN'),
(4, 1, 4, TRUE, 'Internal Marks (CIE)', 4, 'PARENT,ADMIN'),
(5, 1, 5, TRUE, 'Semester Results', 5, 'PARENT,ADMIN'),
(6, 1, 6, TRUE, 'Timetable', 6, 'PARENT,ADMIN'),
(7, 1, 7, TRUE, 'Academic Calendar', 7, 'PARENT,ADMIN'),
(8, 1, 8, TRUE, 'Circulars', 8, 'PARENT,ADMIN'),
(9, 1, 9, TRUE, 'Fee Ledger', 9, 'PARENT,ADMIN'),
(10, 1, 10, TRUE, 'Faculty Mentors', 10, 'PARENT,ADMIN'),
(11, 1, 11, TRUE, 'Leave Request', 11, 'PARENT,ADMIN'),
(12, 1, 12, TRUE, 'Parent Feedback', 12, 'PARENT,ADMIN'),
-- Tenant 2 (AIT) Modules
(13, 2, 1, TRUE, 'Student Overview', 1, 'PARENT,ADMIN'),
(14, 2, 2, TRUE, 'Ward Bio', 2, 'PARENT,ADMIN'),
(15, 2, 3, TRUE, 'Attendance Tracker', 3, 'PARENT,ADMIN'),
(16, 2, 4, TRUE, 'Continuous Evaluation', 4, 'PARENT,ADMIN'),
(17, 2, 5, TRUE, 'Grade Sheet', 5, 'PARENT,ADMIN'),
(18, 2, 6, TRUE, 'Class Schedule', 6, 'PARENT,ADMIN'),
(19, 2, 7, TRUE, 'Campus Calendar', 7, 'PARENT,ADMIN'),
(20, 2, 8, TRUE, 'Notices', 8, 'PARENT,ADMIN'),
(21, 2, 9, TRUE, 'Online Fees', 9, 'PARENT,ADMIN'),
(22, 2, 10, TRUE, 'Mentor Desk', 10, 'PARENT,ADMIN'),
(23, 2, 11, TRUE, 'Absence Request', 11, 'PARENT,ADMIN'),
(24, 2, 12, TRUE, 'Help Desk', 12, 'PARENT,ADMIN');

-- 6. Rules Config
INSERT INTO rules_config (id, tenant_id, shortage_threshold, cie_max_marks, cie_min_eligibility, grading_scale_json, fee_due_grace_days) VALUES
(1, 1, 75.00, 50.00, 20.00, '{"O":[90,100,10],"A+":[80,89,9],"A":[70,79,8],"B+":[60,69,7],"B":[50,59,6],"C":[40,49,5],"F":[0,39,0]}', 15),
(2, 2, 80.00, 50.00, 20.00, '{"O":[90,100,10],"A+":[80,89,9],"A":[70,79,8],"B+":[60,69,7],"B":[50,59,6],"C":[40,49,5],"F":[0,39,0]}', 10);

-- 7. Roles
INSERT INTO roles (id, tenant_id, name, description) VALUES
(1, NULL, 'ROLE_SUPER_ADMIN', 'Institutional Super Administrator'),
(2, NULL, 'ROLE_DEPT_ADMIN', 'Department Head / Academic Coordinator'),
(3, NULL, 'ROLE_FACULTY', 'Teaching Faculty / Mentor'),
(4, NULL, 'ROLE_PARENT', 'Student Parent / Guardian account');

-- 8. Departments
INSERT INTO departments (id, tenant_id, code, name) VALUES
(1, 1, 'CSE', 'Department of Computer Science & Engineering'),
(2, 1, 'ECE', 'Department of Electronics & Communication Engineering'),
(3, 1, 'MECH', 'Department of Mechanical Engineering'),
(4, 2, 'AI', 'Department of Artificial Intelligence & Data Science'),
(5, 2, 'ROB', 'Department of Robotics & Automation');

-- 9. Courses
INSERT INTO courses (id, tenant_id, department_id, code, name, degree_level, duration_years) VALUES
(1, 1, 1, 'BE-CS', 'Bachelor of Engineering in Computer Science and Engineering', 'Undergraduate', 4),
(2, 1, 2, 'BE-EC', 'Bachelor of Engineering in Electronics and Communication Engineering', 'Undergraduate', 4),
(3, 1, 3, 'BE-ME', 'Bachelor of Engineering in Mechanical Engineering', 'Undergraduate', 4),
(4, 2, 4, 'BTECH-AI', 'B.Tech in Artificial Intelligence & Machine Learning', 'Undergraduate', 4),
(5, 2, 5, 'BTECH-ROB', 'B.Tech in Robotics & Autonomous Systems', 'Undergraduate', 4);

-- 10. Terms
INSERT INTO terms (id, tenant_id, code, name, academic_year, start_date, end_date, is_active) VALUES
(1, 1, 'even_2026', 'Even term 2026', '2025-2026', '2026-01-15', '2026-06-30', TRUE),
(2, 1, 'supply_2025_2026', 'Supply/Summer term 2025-2026', '2025-2026', '2026-07-01', '2026-08-15', TRUE),
(3, 1, 'supply_part2_2025_2026', 'Supply/Summer term 2025-2026 Part 2', '2025-2026', '2026-08-16', '2026-09-15', TRUE),
(4, 2, 'spring_2026', 'Spring Term 2026', '2025-2026', '2026-01-10', '2026-05-30', TRUE),
(5, 2, 'summer_2026', 'Summer Fast-Track 2026', '2025-2026', '2026-06-01', '2026-07-31', TRUE);

-- 11. Term Access Links
INSERT INTO term_links (id, tenant_id, title, route_slug, term_code, margin_top, display_order, is_active) VALUES
(1, 1, 'Click here to access Even term 2026', '/parentseven/', 'even_2026', 20, 1, TRUE),
(2, 1, 'Click here to access Supply/Summer term 2025-2026', '/parentssupply/', 'supply_2025_2026', 20, 2, TRUE),
(3, 1, 'Click here to access Supply/Summer term 2025-2026 Part 2', '/parentssupplypart2/', 'supply_part2_2025_2026', 20, 3, TRUE),
(4, 2, 'Access Spring Term 2026 Portal', '/spring2026/', 'spring_2026', 20, 1, TRUE),
(5, 2, 'Access Summer Fast-Track Portal', '/summer2026/', 'summer_2026', 20, 2, TRUE);

-- 12. Users
INSERT INTO users (id, tenant_id, role_id, username, password_hash, email, phone, status) VALUES
-- Admin for DIE (password: Admin@123)
(1, 1, 1, 'admin', '$2a$10$rffM6lfbIygFBLEyertoP.1sOi94aXh0qgn68/oCaM4N1mE0vwVYK', 'admin@demo-institute.edu.in', '9845099991', 'ACTIVE'),
(2, 1, 2, 'hod_cse', '$2a$10$rffM6lfbIygFBLEyertoP.1sOi94aXh0qgn68/oCaM4N1mE0vwVYK', 'hod.cse@demo-institute.edu.in', '9845099992', 'ACTIVE'),
-- Parent 1 (Rajesh Sharma) - Aarav Sharma (DOB: 2005-04-15)
(3, 1, 4, '1DM24CS001', '$2a$10$phwiqTrEEWvQ9UM1igkgQeiOhTrLWm9JLEwWNBTScH7snPdmTW78S', 'rajesh.sharma@example.com', '9845012345', 'ACTIVE'),
-- Parent 2 (Venkatesh Iyer) - Student 1DM24CS002 (DOB: 2005-08-20)
(4, 1, 4, '1DM24CS002', '$2a$10$1nlgWeXN/kQt/KxMPRfqweGfBgqycKyFjqXFJnP0hvJ507vFD9BDW', 'venkatesh.iyer@example.com', '9845012346', 'ACTIVE'),
-- Parent 3 (Shortage Student 1DM24EC001) - Student 1DM24EC001 (DOB: 2005-11-10)
(5, 1, 4, '1DM24EC001', '$2a$10$ZusRB5w4ObXlD8I8p/NxPuUacLvq6/6FXoK7Dfgavzx0en2eQO19m', 'manjunath.hegde@example.com', '9845022301', 'ACTIVE'),
-- Admin for AIT (password: Admin@123)
(6, 2, 1, 'apex_admin', '$2a$10$rffM6lfbIygFBLEyertoP.1sOi94aXh0qgn68/oCaM4N1mE0vwVYK', 'admin@apex-tech.edu.in', '9845088881', 'ACTIVE'),
-- Student for AIT (password: Student@123)
(7, 2, 4, '2AT24AI001', '$2a$10$g8wCigxsRnpxC1tEVueE5.fGX5GhEKccZ7CT128V6g8Qiu.GhmeBK', 'kavita.shukla@example.com', '9845077771', 'ACTIVE');

-- 13. Parents
INSERT INTO parents (id, tenant_id, user_id, father_name, mother_name, primary_mobile, alternate_mobile, email, address, occupation) VALUES
(1, 1, 3, 'Rajesh Sharma', 'Sunita Sharma', '9845012345', '9845011223', 'rajesh.sharma@example.com', '#142, 4th Main, Kuvempunagar, Mysuru - 570023', 'Business / Private Service'),
(2, 1, 4, 'Venkatesh Iyer', 'Radha Iyer', '9845012346', '9845011224', 'venkatesh.iyer@example.com', '#56, Gokulam 3rd Stage, Mysuru - 570002', 'Chartered Accountant'),
(3, 1, 5, 'Manjunath Hegde', 'Shailaja Hegde', '9845022301', '9845022399', 'manjunath.hegde@example.com', '#102, Alanahalli, Mysuru - 570028', 'Professor'),
(4, 2, 7, 'Vikram Shukla', 'Kavita Shukla', '9845077771', '9845077799', 'kavita.shukla@example.com', '#88, Indiranagar, Bengaluru - 560038', 'Tech Executive');

-- 14. Students (Notice Sibling: Parent 1 has student 1 AND student 2!)
INSERT INTO students (id, tenant_id, user_id, parent_id, department_id, course_id, usn, roll_no, first_name, last_name, gender, dob, blood_group, admission_year, current_sem, photo_url) VALUES
(1, 1, 3, 1, 1, 1, '1DM24CS001', '01', 'Aarav', 'Sharma', 'MALE', '2005-04-15', 'O+', 2024, 4, 'assets/images/student_placeholder.svg'),
(2, 1, 3, 1, 2, 2, '1DM24EC002', '12', 'Ananya', 'Sharma', 'FEMALE', '2005-08-22', 'B+', 2024, 2, 'assets/images/student_placeholder.svg'),
(3, 1, 5, 3, 2, 2, '1DM24EC001', '01', 'Nikhil', 'Hegde', 'MALE', '2005-01-10', 'A+', 2024, 4, 'assets/images/student_placeholder.svg'),
(4, 2, 7, 4, 4, 4, '2AT24AI001', '01', 'Dev', 'Shukla', 'MALE', '2005-05-10', 'O+', 2024, 4, 'assets/images/student_placeholder.svg');

-- 15. Subjects
INSERT INTO subjects (id, tenant_id, department_id, course_id, term_id, semester, code, title, credits, type) VALUES
(1, 1, 1, 1, 1, 4, '21CS41', 'Design and Analysis of Algorithms', 4.0, 'Theory'),
(2, 1, 1, 1, 1, 4, '21CS42', 'Operating Systems', 4.0, 'Theory'),
(3, 1, 1, 1, 1, 4, '21CS43', 'Database Management Systems', 4.0, 'Theory'),
(4, 1, 1, 1, 1, 4, '21CS44', 'Microcontroller and Embedded Systems', 3.0, 'Theory'),
(5, 1, 1, 1, 1, 4, '21CSL46', 'Design and Analysis of Algorithms Lab', 1.5, 'Practical'),
(6, 1, 1, 1, 1, 4, '21CSL47', 'Database Management Systems Lab', 1.5, 'Practical'),
(7, 1, 2, 2, 1, 4, '21EC41', 'Analog and Digital Circuits', 4.0, 'Theory'),
(8, 1, 2, 2, 1, 4, '21EC42', 'Signals and Systems', 4.0, 'Theory'),
(9, 2, 4, 4, 4, 4, '24AI401', 'Deep Neural Architectures', 4.0, 'Theory'),
(10, 2, 4, 4, 4, 4, '24AI402', 'Natural Language Processing', 4.0, 'Theory');

-- 16. Attendance (Student 1 has 90%, Student 3 has shortage 68.75%)
INSERT INTO attendance (id, tenant_id, student_id, subject_id, term_id, "month", total_classes, attended_classes, percentage, remarks) VALUES
-- Student 1 (Aarav Sharma - DIE)
(1, 1, 1, 1, 1, 'Feb 2026', 40, 36, 90.00, 'Regular'),
(2, 1, 1, 2, 1, 'Feb 2026', 40, 37, 92.50, 'Regular'),
(3, 1, 1, 3, 1, 'Feb 2026', 40, 35, 87.50, 'Regular'),
(4, 1, 1, 4, 1, 'Feb 2026', 40, 36, 90.00, 'Regular'),
-- Student 2 (Ananya Sharma - DIE, Sibling)
(5, 1, 2, 7, 1, 'Feb 2026', 36, 32, 88.89, 'Satisfactory'),
(6, 1, 2, 8, 1, 'Feb 2026', 36, 31, 86.11, 'Satisfactory'),
-- Student 3 (Nikhil Hegde - DIE, Shortage <75%)
(7, 1, 3, 7, 1, 'Feb 2026', 40, 26, 65.00, 'Critical Shortage'),
(8, 1, 3, 8, 1, 'Feb 2026', 40, 29, 72.50, 'Shortage Alert'),
-- Student 4 (Dev Shukla - AIT)
(9, 2, 4, 9, 4, 'Feb 2026', 42, 39, 92.86, 'Excellent'),
(10, 2, 4, 10, 4, 'Feb 2026', 42, 40, 95.24, 'Excellent');

-- 17. Internal Marks (CIE)
INSERT INTO internal_marks (id, tenant_id, student_id, subject_id, term_id, test_name, max_marks, marks_obtained, remarks) VALUES
-- Student 1
(1, 1, 1, 1, 1, 'CIE-1', 50.00, 44.00, 'Good analysis'),
(2, 1, 1, 1, 1, 'CIE-2', 50.00, 46.00, 'Excellent algorithm proof'),
(3, 1, 1, 1, 1, 'Assignment-1', 10.00, 9.00, 'Completed on time'),
(4, 1, 1, 1, 1, 'Assignment-2', 10.00, 9.50, 'Well documented'),
(5, 1, 1, 2, 1, 'CIE-1', 50.00, 42.00, 'Satisfactory'),
(6, 1, 1, 2, 1, 'CIE-2', 50.00, 45.00, 'Very good'),
-- Student 2
(7, 1, 2, 7, 1, 'CIE-1', 50.00, 41.00, 'Good'),
(8, 1, 2, 7, 1, 'CIE-2', 50.00, 43.00, 'Consistent'),
-- Student 3
(9, 1, 3, 7, 1, 'CIE-1', 50.00, 28.00, 'Needs improvement'),
(10, 1, 3, 7, 1, 'CIE-2', 50.00, 31.00, 'Marginal'),
-- Student 4
(11, 2, 4, 9, 4, 'Evaluation-1', 50.00, 48.00, 'Top score'),
(12, 2, 4, 10, 4, 'Evaluation-1', 50.00, 47.00, 'Top score');

-- 18. Examination Results
INSERT INTO examination_results (id, tenant_id, student_id, term_id, semester, subject_id, credits, grade_letter, grade_points, result_status, sgpa, cgpa, exam_session, declared_date) VALUES
(1, 1, 1, 1, 4, 1, 4.0, 'O', 10.00, 'PASS', 8.75, 8.82, 'Dec 2025 - Jan 2026', '2026-02-01'),
(2, 1, 1, 1, 4, 2, 4.0, 'A+', 9.00, 'PASS', 8.75, 8.82, 'Dec 2025 - Jan 2026', '2026-02-01'),
(3, 1, 1, 1, 4, 3, 4.0, 'A', 8.00, 'PASS', 8.75, 8.82, 'Dec 2025 - Jan 2026', '2026-02-01'),
(4, 1, 2, 1, 2, 7, 4.0, 'A+', 9.00, 'PASS', 8.90, 8.95, 'Dec 2025 - Jan 2026', '2026-02-01'),
(5, 1, 3, 1, 4, 7, 4.0, 'B', 6.00, 'PASS', 6.80, 7.10, 'Dec 2025 - Jan 2026', '2026-02-01'),
(6, 2, 4, 4, 4, 9, 4.0, 'O', 10.00, 'PASS', 9.40, 9.35, 'Dec 2025', '2026-01-20');

-- 19. Timetable
INSERT INTO timetable (id, tenant_id, term_id, department_id, semester, day_of_week, period_number, start_time, end_time, subject_id, room_no, faculty_name) VALUES
(1, 1, 1, 1, 4, 'Monday', 1, '09:00:00', '10:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'),
(2, 1, 1, 1, 4, 'Monday', 2, '10:00:00', '11:00:00', 2, 'LH-201', 'Prof. Priya Rao'),
(3, 1, 1, 1, 4, 'Monday', 3, '11:15:00', '12:15:00', 3, 'LH-201', 'Dr. Anand Joshi'),
(4, 1, 1, 1, 4, 'Monday', 4, '12:15:00', '01:15:00', 4, 'LH-201', 'Prof. Sunita Patil'),
(5, 1, 1, 1, 4, 'Tuesday', 1, '09:00:00', '10:00:00', 3, 'LH-201', 'Dr. Anand Joshi'),
(6, 1, 1, 1, 4, 'Wednesday', 1, '09:00:00', '10:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'),
(7, 1, 1, 1, 4, 'Thursday', 1, '09:00:00', '10:00:00', 2, 'LH-201', 'Prof. Priya Rao'),
(8, 1, 1, 1, 4, 'Friday', 1, '09:00:00', '10:00:00', 4, 'LH-201', 'Prof. Sunita Patil'),
(9, 1, 1, 1, 4, 'Saturday', 1, '09:00:00', '10:00:00', 1, 'LH-201', 'Dr. Ramesh Kumar'),
(10, 2, 4, 4, 4, 'Monday', 1, '09:30:00', '10:30:00', 9, 'AI-Lab 4', 'Dr. Varun Mehta');

-- 20. Notices (Login page)
INSERT INTO notices (id, tenant_id, title, content, is_login_notice, priority, is_active) VALUES
(1, 1, 'Even Term 2026 Academic Commencement', 'Welcome to the preview of the new mobile friendly parent portal. Even term 2026 academic sessions are now active.', TRUE, 1, TRUE),
(2, 2, 'Spring 2026 Campus Circular', 'Spring 2026 Hackathon and Term 2 classes commence from March 1st. Check your student fee and timetable schedule.', TRUE, 1, TRUE);

-- 21. Announcements
INSERT INTO announcements (id, tenant_id, term_id, title, category, description, attachment_file, is_published, published_date) VALUES
(1, 1, 1, 'Schedule for Continuous Internal Evaluation (CIE-2)', 'Examinations', 'The second Continuous Internal Evaluation (CIE-2) for B.E. 4th semester students is scheduled from March 24th to March 27th, 2026.', 'CIE_2_Schedule.pdf', TRUE, '2026-02-10'),
(2, 1, 1, 'Annual Technical Symposium - INNOVATE 2026', 'Events', 'Demo Institute invites all engineering students to participate in INNOVATE 2026 technical hackathons.', 'Innovate_2026.pdf', TRUE, '2026-01-28'),
(3, 2, 4, 'Global AI Summit 2026 Registrations', 'Conferences', 'Apex Institute of Technology is hosting the 5th Annual Global AI Summit on April 12-14, 2026.', 'AI_Summit_2026.pdf', TRUE, '2026-02-05');

-- 22. Fee Records
INSERT INTO fee_records (id, tenant_id, student_id, term_id, academic_year, fee_category, total_amount, paid_amount, balance_amount, payment_status, receipt_no, payment_date, payment_mode) VALUES
(1, 1, 1, 1, '2025-2026', 'Even Term Tuition & Facilities Fee', 95000.00, 95000.00, 0.00, 'Paid', 'REC/2026/0491', '2026-01-18', 'Online NetBanking'),
(2, 1, 2, 1, '2025-2026', 'Tuition & Laboratory Fee', 90000.00, 90000.00, 0.00, 'Paid', 'REC/2026/0512', '2026-01-20', 'Credit Card'),
(3, 1, 3, 1, '2025-2026', 'Even Term Tuition Fee', 95000.00, 50000.00, 45000.00, 'Partial', 'REC/2026/0890', '2026-01-22', 'UPI'),
(4, 2, 4, 4, '2025-2026', 'Spring Term Academic Fee', 120000.00, 60000.00, 60000.00, 'Partial', 'AIT/2026/101', '2026-01-25', 'NetBanking');

-- 23. Calendar Events
INSERT INTO calendar_events (id, tenant_id, title, event_type, start_date, end_date, description) VALUES
(1, 1, 'Continuous Internal Evaluation (CIE-1)', 'EXAM', '2026-02-20', '2026-02-23', 'First cycle of internal assessment tests'),
(2, 1, 'Maha Shivaratri Holiday', 'HOLIDAY', '2026-02-26', '2026-02-26', 'Institutional Holiday'),
(3, 1, 'CIE-2 Examinations', 'EXAM', '2026-03-24', '2026-03-27', 'Second cycle of internal assessment tests'),
(4, 1, 'Fee Installment Deadline', 'FEE_DUE', '2026-03-31', '2026-03-31', 'Final date for even term balance dues'),
(5, 1, 'Semester End Practical Exams', 'EXAM', '2026-06-01', '2026-06-10', 'University lab practical examinations'),
(6, 2, 'Spring Innovation Sprint', 'ACADEMIC', '2026-03-05', '2026-03-08', '48-hour interdisciplinary AI hackathon');

-- 24. Leave Requests
INSERT INTO leave_requests (id, tenant_id, student_id, parent_id, start_date, end_date, reason, status, submitted_at, approved_by, remarks) VALUES
(1, 1, 1, 1, '2026-02-12', '2026-02-13', 'Family wedding ceremony in hometown', 'APPROVED', '2026-02-08 10:30:00', 'Dr. Ramesh Kumar (HOD)', 'Approved. Student advised to collect lecture notes.');

-- 25. Feedback / Grievance Tickets
INSERT INTO feedback_tickets (id, tenant_id, student_id, parent_id, category, subject, message, status, response, created_at) VALUES
(1, 1, 1, 1, 'Academics', 'Algorithm Tutorial Class Timing', 'Can tutorial classes for 4th sem CSE be scheduled in the morning slots instead of late afternoons?', 'RESOLVED', 'The department academic coordinator has shifted Section A tutorial to 10:00 AM on Thursdays.', '2026-02-02 14:15:00');

-- 26. Mentor Consultations
INSERT INTO mentor_appointments (id, tenant_id, student_id, faculty_name, appointment_time, mode, status, topic, notes) VALUES
(1, 1, 1, 'Dr. Ramesh Kumar', '2026-03-02 15:30:00', 'IN_PERSON', 'CONFIRMED', 'Internship & Research Mentorship', 'Discussion on summer industry internship credits in Bangalore.');

-- 27. Payments
INSERT INTO payments (id, tenant_id, fee_id, student_id, transaction_ref, amount, payment_method, status, receipt_no, created_at) VALUES
(1, 1, 1, 1, 'TXN_2026_9812490', 95000.00, 'NetBanking HDFC Sandbox', 'SUCCESS', 'REC/2026/0491', '2026-01-18 11:24:00');

-- 28. Notifications (For SSE)
INSERT INTO notifications (id, tenant_id, user_id, title, message, type, is_read, created_at) VALUES
(1, 1, 3, 'CIE-2 Schedule Published', 'Examination branch has published the timetable for CIE-2 starting March 24th.', 'EXAM', FALSE, '2026-02-10 09:00:00'),
(2, 1, 3, 'Fee Payment Acknowledgment', 'Fee receipt REC/2026/0491 has been generated and credited successfully.', 'FEE', TRUE, '2026-01-18 11:25:00');
