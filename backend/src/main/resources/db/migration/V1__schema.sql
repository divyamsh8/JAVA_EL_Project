-- ====================================================================
-- Database Schema for Config-Driven Multi-Tenant Parent Portal
-- Compatible with H2 (MySQL Mode) and MySQL 8+
-- ====================================================================

-- 1. Tenants Table
CREATE TABLE IF NOT EXISTS tenants (
  id BIGINT NOT NULL AUTO_INCREMENT,
  slug VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  code VARCHAR(50) NOT NULL UNIQUE,
  domain VARCHAR(150) NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

-- 2. Tenant Settings Table (Design tokens, text, branding)
CREATE TABLE IF NOT EXISTS tenant_settings (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  theme_json TEXT NOT NULL,
  welcome_text TEXT NOT NULL,
  notice_text TEXT NOT NULL,
  footer_text TEXT NOT NULL,
  contact_email VARCHAR(150) NULL,
  contact_phone VARCHAR(50) NULL,
  default_language VARCHAR(10) DEFAULT 'en',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_tenant_settings_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 3. Login Schemas (Configurable per tenant)
CREATE TABLE IF NOT EXISTS login_schemas (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  identifier_field VARCHAR(50) DEFAULT 'USN',
  credential_type VARCHAR(50) DEFAULT 'DOB_DROPDOWN',
  allowed_terms_json TEXT NULL,
  rate_limit_max INT DEFAULT 5,
  lockout_minutes INT DEFAULT 15,
  PRIMARY KEY (id),
  CONSTRAINT fk_login_schemas_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 4. Modules Table (Global registry)
CREATE TABLE IF NOT EXISTS modules (
  id BIGINT NOT NULL AUTO_INCREMENT,
  module_key VARCHAR(50) NOT NULL UNIQUE,
  default_label VARCHAR(100) NOT NULL,
  default_icon VARCHAR(50) NOT NULL,
  default_order INT NOT NULL,
  default_route VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
);

-- 5. Tenant Modules (Per-tenant feature flags & navigation order)
CREATE TABLE IF NOT EXISTS tenant_modules (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  module_id BIGINT NOT NULL,
  is_enabled BOOLEAN DEFAULT TRUE,
  custom_label VARCHAR(100) NULL,
  display_order INT NOT NULL,
  allowed_roles VARCHAR(100) DEFAULT 'PARENT,ADMIN',
  PRIMARY KEY (id),
  CONSTRAINT fk_tm_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_tm_module FOREIGN KEY (module_id) REFERENCES modules (id) ON DELETE CASCADE
);

-- 6. Rules Config (Grading scale, CIE formula, shortage thresholds)
CREATE TABLE IF NOT EXISTS rules_config (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  shortage_threshold DECIMAL(5,2) DEFAULT 75.00,
  cie_max_marks DECIMAL(5,2) DEFAULT 50.00,
  cie_min_eligibility DECIMAL(5,2) DEFAULT 20.00,
  grading_scale_json TEXT NULL,
  fee_due_grace_days INT DEFAULT 15,
  PRIMARY KEY (id),
  CONSTRAINT fk_rules_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 7. Roles Table
CREATE TABLE IF NOT EXISTS roles (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NULL,
  name VARCHAR(50) NOT NULL UNIQUE,
  description VARCHAR(255) NULL,
  PRIMARY KEY (id)
);

-- 8. Departments Table
CREATE TABLE IF NOT EXISTS departments (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  code VARCHAR(20) NOT NULL,
  name VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_dept_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 9. Courses Table
CREATE TABLE IF NOT EXISTS courses (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  department_id BIGINT NOT NULL,
  code VARCHAR(30) NOT NULL,
  name VARCHAR(150) NOT NULL,
  degree_level VARCHAR(50) DEFAULT 'Undergraduate',
  duration_years INT DEFAULT 4,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_course_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_courses_department FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE
);

-- 10. Terms Table
CREATE TABLE IF NOT EXISTS terms (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  code VARCHAR(50) NOT NULL,
  name VARCHAR(150) NOT NULL,
  academic_year VARCHAR(20) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_term_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 11. Term Access Links
CREATE TABLE IF NOT EXISTS term_links (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  title VARCHAR(150) NOT NULL,
  route_slug VARCHAR(50) NOT NULL,
  term_code VARCHAR(50) NOT NULL,
  margin_top INT DEFAULT 20,
  display_order INT DEFAULT 1,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_tl_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 12. Users Table
CREATE TABLE IF NOT EXISTS users (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  role_id BIGINT NOT NULL,
  username VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(150) NULL,
  phone VARCHAR(25) NULL,
  status VARCHAR(50) DEFAULT 'ACTIVE',
  last_login_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_users_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE RESTRICT
);

-- 13. Parents Table
CREATE TABLE IF NOT EXISTS parents (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  user_id BIGINT NULL,
  father_name VARCHAR(150) NOT NULL,
  mother_name VARCHAR(150) NOT NULL,
  primary_mobile VARCHAR(25) NOT NULL,
  alternate_mobile VARCHAR(25) NULL,
  email VARCHAR(150) NULL,
  address TEXT NOT NULL,
  occupation VARCHAR(100) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_parents_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_parents_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
);

-- 14. Students Table
CREATE TABLE IF NOT EXISTS students (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  parent_id BIGINT NOT NULL,
  department_id BIGINT NOT NULL,
  course_id BIGINT NOT NULL,
  usn VARCHAR(30) NOT NULL,
  roll_no VARCHAR(30) NULL,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  gender VARCHAR(20) DEFAULT 'MALE',
  dob DATE NOT NULL,
  blood_group VARCHAR(10) NULL,
  admission_year INT NULL,
  current_sem INT NULL,
  photo_url VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_student_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_students_parent FOREIGN KEY (parent_id) REFERENCES parents (id) ON DELETE CASCADE,
  CONSTRAINT fk_students_dept FOREIGN KEY (department_id) REFERENCES departments (id) ON DELETE CASCADE,
  CONSTRAINT fk_students_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE
);

-- 15. Subjects Table
CREATE TABLE IF NOT EXISTS subjects (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  department_id BIGINT NOT NULL,
  course_id BIGINT NOT NULL,
  term_id BIGINT NOT NULL,
  semester INT NOT NULL,
  code VARCHAR(30) NOT NULL,
  title VARCHAR(150) NOT NULL,
  credits DECIMAL(3,1) NOT NULL,
  type VARCHAR(50) DEFAULT 'Theory',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_subj_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 16. Attendance Table
CREATE TABLE IF NOT EXISTS attendance (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  student_id BIGINT NOT NULL,
  subject_id BIGINT NOT NULL,
  term_id BIGINT NOT NULL,
  "month" VARCHAR(20) NOT NULL,
  total_classes INT NOT NULL DEFAULT 0,
  attended_classes INT NOT NULL DEFAULT 0,
  percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  remarks VARCHAR(100) NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_att_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE,
  CONSTRAINT fk_att_subject FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE CASCADE
);

-- 17. Internal Marks Table (CIE)
CREATE TABLE IF NOT EXISTS internal_marks (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  student_id BIGINT NOT NULL,
  subject_id BIGINT NOT NULL,
  term_id BIGINT NOT NULL,
  test_name VARCHAR(50) NOT NULL,
  max_marks DECIMAL(5,2) NOT NULL DEFAULT 50.00,
  marks_obtained DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  remarks VARCHAR(100) NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_cie_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_cie_student FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE,
  CONSTRAINT fk_cie_subject FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE CASCADE
);

-- 18. Examination Results Table
CREATE TABLE IF NOT EXISTS examination_results (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  student_id BIGINT NOT NULL,
  term_id BIGINT NOT NULL,
  semester INT NOT NULL,
  subject_id BIGINT NOT NULL,
  credits DECIMAL(3,1) NOT NULL DEFAULT 4.0,
  grade_letter VARCHAR(5) NOT NULL,
  grade_points DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  result_status VARCHAR(20) NOT NULL DEFAULT 'PASS',
  sgpa DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  cgpa DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  exam_session VARCHAR(50) NOT NULL,
  declared_date DATE NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_res_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_res_student FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE,
  CONSTRAINT fk_res_subject FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE CASCADE
);

-- 19. Timetable Table
CREATE TABLE IF NOT EXISTS timetable (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  term_id BIGINT NOT NULL,
  department_id BIGINT NOT NULL,
  semester INT NOT NULL,
  day_of_week VARCHAR(20) NOT NULL,
  period_number INT NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  subject_id BIGINT NOT NULL,
  room_no VARCHAR(50) NOT NULL,
  faculty_name VARCHAR(150) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_tt_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_tt_subject FOREIGN KEY (subject_id) REFERENCES subjects (id) ON DELETE CASCADE
);

-- 20. Notices Table (Login Notice)
CREATE TABLE IF NOT EXISTS notices (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  is_login_notice BOOLEAN DEFAULT TRUE,
  priority INT DEFAULT 1,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_notice_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 21. Announcements Table
CREATE TABLE IF NOT EXISTS announcements (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  term_id BIGINT NULL,
  title VARCHAR(255) NOT NULL,
  category VARCHAR(100) DEFAULT 'Academic',
  description TEXT NOT NULL,
  attachment_file VARCHAR(255) NULL,
  is_published BOOLEAN DEFAULT TRUE,
  published_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_ann_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 22. Fee Records Table
CREATE TABLE IF NOT EXISTS fee_records (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  student_id BIGINT NOT NULL,
  term_id BIGINT NOT NULL,
  academic_year VARCHAR(20) NOT NULL,
  fee_category VARCHAR(100) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  balance_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  payment_status VARCHAR(50) DEFAULT 'Pending',
  receipt_no VARCHAR(50) NULL,
  payment_date DATE NULL,
  payment_mode VARCHAR(50) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_fee_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_fee_student FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE
);

-- 23. Calendar Events Table
CREATE TABLE IF NOT EXISTS calendar_events (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  title VARCHAR(255) NOT NULL,
  event_type VARCHAR(50) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_cal_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 24. Leave Requests Table
CREATE TABLE IF NOT EXISTS leave_requests (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  student_id BIGINT NOT NULL,
  parent_id BIGINT NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  reason TEXT NOT NULL,
  status VARCHAR(20) DEFAULT 'PENDING',
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  approved_by VARCHAR(100) NULL,
  remarks VARCHAR(255) NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_lr_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE,
  CONSTRAINT fk_lr_student FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE
);

-- 25. Feedback / Grievance Tickets Table
CREATE TABLE IF NOT EXISTS feedback_tickets (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  student_id BIGINT NOT NULL,
  parent_id BIGINT NOT NULL,
  category VARCHAR(50) NOT NULL,
  subject VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  status VARCHAR(20) DEFAULT 'OPEN',
  response TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_fb_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 26. Mentor Consultations / Appointments Table
CREATE TABLE IF NOT EXISTS mentor_appointments (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  student_id BIGINT NOT NULL,
  faculty_name VARCHAR(150) NOT NULL,
  appointment_time TIMESTAMP NOT NULL,
  mode VARCHAR(50) DEFAULT 'IN_PERSON',
  status VARCHAR(20) DEFAULT 'CONFIRMED',
  topic VARCHAR(200) NOT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_ma_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 27. Payments & Invoices Table
CREATE TABLE IF NOT EXISTS payments (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  fee_id BIGINT NOT NULL,
  student_id BIGINT NOT NULL,
  transaction_ref VARCHAR(100) NOT NULL UNIQUE,
  amount DECIMAL(10,2) NOT NULL,
  payment_method VARCHAR(50) DEFAULT 'Mock Sandbox Gateway',
  status VARCHAR(20) DEFAULT 'SUCCESS',
  receipt_no VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_pay_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 28. Notifications Table (SSE & in-app alerts)
CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NOT NULL,
  user_id BIGINT NOT NULL,
  title VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  type VARCHAR(50) DEFAULT 'INFO',
  is_read BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_notif_tenant FOREIGN KEY (tenant_id) REFERENCES tenants (id) ON DELETE CASCADE
);

-- 29. Login Attempts Table (Rate limiting)
CREATE TABLE IF NOT EXISTS login_attempts (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NULL,
  ip_address VARCHAR(45) NOT NULL,
  username VARCHAR(100) NOT NULL,
  attempt_time BIGINT NOT NULL,
  PRIMARY KEY (id)
);

-- 30. Login Logs Table (Security Audit Trail)
CREATE TABLE IF NOT EXISTS login_logs (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id BIGINT NULL,
  username VARCHAR(100) NOT NULL,
  ip_address VARCHAR(45) NOT NULL,
  user_agent TEXT NULL,
  status VARCHAR(50) NOT NULL,
  failure_reason VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
