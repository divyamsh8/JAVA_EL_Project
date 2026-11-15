export interface UserSummary {
  id: number;
  username: string;
  role: string;
  displayName: string;
  email: string | null;
  studentId: number | null;
  usn: string | null;
  departmentId: number | null;
  departmentName: string | null;
}

export interface SiblingSummary {
  studentId: number;
  usn: string;
  fullName: string;
  departmentCode: string;
  courseName: string;
  currentSem: number;
  photoUrl?: string;
}

export interface LoginResponse {
  success: boolean;
  token?: string;
  message?: string;
  attemptsRemaining?: number;
  user?: UserSummary;
  siblings?: SiblingSummary[];
}

export interface ApiResponse<T> {
  success: boolean;
  message?: string;
  data: T;
}

export interface StudentProfile {
  id: number;
  usn: string;
  rollNo?: string;
  firstName: string;
  lastName: string;
  fullName: string;
  gender: string;
  dob: string;
  bloodGroup: string;
  admissionYear: number;
  currentSem: number;
  photoUrl?: string;
  departmentId: number;
  departmentCode: string;
  departmentName: string;
  courseName: string;
  degreeLevel: string;
  fatherName: string;
  motherName: string;
  primaryMobile: string;
  alternateMobile?: string;
  parentEmail?: string;
  address: string;
  occupation?: string;
}

export interface SubjectAttendance {
  subjectId: number;
  subjectCode: string;
  subjectTitle: string;
  credits: number;
  totalClasses: number;
  attendedClasses: number;
  percentage: number;
  isShortage: boolean;
  remarks?: string;
}

export interface AttendanceData {
  overallTotalClasses: number;
  overallAttendedClasses: number;
  overallPercentage: number;
  shortageAlert: boolean;
  subjects: SubjectAttendance[];
}

export interface SubjectCie {
  subjectId: number;
  subjectCode: string;
  subjectTitle: string;
  credits: number;
  cie1: number;
  cie2: number;
  assignment1: number;
  assignment2: number;
  cieTotal: number;
  maxMarks: number;
  status: string;
}

export interface CieMarksData {
  subjects: SubjectCie[];
}

export interface SubjectResult {
  subjectId: number;
  subjectCode: string;
  subjectTitle: string;
  credits: number;
  gradeLetter: string;
  gradePoints: number;
  resultStatus: string;
}

export interface ResultsData {
  semester: number;
  examSession: string;
  declaredDate: string;
  sgpa: number;
  cgpa: number;
  overallStatus: string;
  subjects: SubjectResult[];
}

export interface PeriodSlot {
  periodNumber: number;
  startTime: string;
  endTime: string;
  subjectCode: string;
  subjectTitle: string;
  roomNo: string;
  facultyName: string;
}

export interface TimetableData {
  departmentName: string;
  semester: number;
  schedule: Record<string, PeriodSlot[]>;
}

export interface AnnouncementItem {
  id: number;
  termId?: number;
  termName?: string;
  title: string;
  category: string;
  description: string;
  attachmentFile?: string;
  publishedDate: string;
}

export interface FeeItem {
  id: number;
  academicYear: string;
  feeCategory: string;
  totalAmount: number;
  paidAmount: number;
  balanceAmount: number;
  paymentStatus: string;
  receiptNo?: string;
  paymentDate?: string;
  paymentMode?: string;
}

export interface FeeData {
  totalPayable: number;
  totalPaid: number;
  totalBalance: number;
  overallStatus: string;
  records: FeeItem[];
}

export interface NoticeItem {
  id: number;
  title: string;
  content: string;
  isLoginNotice: boolean;
  priority: number;
}

export interface TermItem {
  id: number;
  code: string;
  name: string;
  academicYear: string;
  startDate: string;
  endDate: string;
  isActive: boolean;
}

export interface TermLinkItem {
  id: number;
  title: string;
  routeSlug: string;
  termCode: string;
  marginTop?: number;
  displayOrder?: number;
}

export interface LoginLogItem {
  id: number;
  username: string;
  ipAddress: string;
  userAgent?: string;
  status: string;
  failureReason?: string;
  createdAt: string;
}

export interface AdminStatsData {
  totalStudents: number;
  totalDepartments: number;
  totalCourses: number;
  shortageCount: number;
  totalFeesPending: number;
  shortageStudents: StudentProfile[];
  recentLogs: LoginLogItem[];
}

export interface TenantConfig {
  id: number;
  slug: string;
  name: string;
  code: string;
  theme: {
    primary?: string;
    primaryHover?: string;
    bgLeft?: string;
    bgRight?: string;
    navy?: string;
    crestIcon?: string;
    fontFamily?: string;
    borderRadius?: string;
    badgeColor?: string;
  };
  welcomeText: string;
  noticeText: string;
  footerText: string;
  contactEmail: string;
  contactPhone: string;
  defaultLanguage: string;
  loginSchema: {
    identifierField: string;
    credentialType: string;
    allowedTerms: string[];
    rateLimitMax: number;
    lockoutMinutes: number;
  };
  modules: Array<{
    key: string;
    label: string;
    icon: string;
    route: string;
    order: number;
    enabled: boolean;
  }>;
  terms: TermItem[];
  termLinks: TermLinkItem[];
  rules: {
    shortageThreshold: number;
    cieMaxMarks: number;
    cieMinEligibility: number;
    feeDueGraceDays: number;
  };
}

export interface CalendarEventItem {
  id: number;
  title: string;
  eventType: string;
  startDate: string;
  endDate: string;
  description?: string;
}

export interface LeaveRequestItem {
  id: number;
  studentId: number;
  studentName: string;
  usn: string;
  startDate: string;
  endDate: string;
  reason: string;
  status: 'PENDING' | 'APPROVED' | 'REJECTED';
  submittedAt: string;
  approvedBy?: string;
  remarks?: string;
}

export interface FeedbackItem {
  id: number;
  studentId: number;
  studentName: string;
  usn: string;
  category: string;
  subject: string;
  message: string;
  status: 'OPEN' | 'RESOLVED';
  response?: string;
  createdAt: string;
}

export interface MentorAppointmentItem {
  id: number;
  tenantId: number;
  studentId: number;
  facultyName: string;
  appointmentTime: string;
  mode: string;
  status: string;
  topic: string;
  notes?: string;
  createdAt?: string;
}

export interface PaymentItem {
  id: number;
  feeId: number;
  studentId: number;
  transactionRef: string;
  amount: number;
  paymentMethod: string;
  status: string;
  receiptNo: string;
  createdAt: string;
}

export interface NotificationItem {
  id: number;
  title: string;
  message: string;
  type: string;
  isRead: boolean;
  createdAt: string;
}
