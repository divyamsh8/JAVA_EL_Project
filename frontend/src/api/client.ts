import axios from 'axios';
import {
  ApiResponse,
  LoginResponse,
  UserSummary,
  StudentProfile,
  AttendanceData,
  CieMarksData,
  ResultsData,
  TimetableData,
  AnnouncementItem,
  FeeData,
  NoticeItem,
  TermItem,
  TermLinkItem,
  AdminStatsData,
  SiblingSummary,
  CalendarEventItem,
  LeaveRequestItem,
  FeedbackItem,
  MentorAppointmentItem,
  PaymentItem,
  NotificationItem,
  TenantConfig,
} from '../types';

const api = axios.create({
  baseURL: '/api/v1',
  headers: {
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  const tenant = localStorage.getItem('portal_tenant') || 'die';
  config.headers['X-Tenant'] = tenant;
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401 && !window.location.pathname.includes('/login')) {
      // Don't auto-redirect if already on login page
    }
    return Promise.reject(error);
  }
);

export const authApi = {
  login: async (data: {
    username: string;
    day?: string;
    month?: string;
    year?: string;
    password?: string;
    termCode?: string;
  }): Promise<LoginResponse> => {
    const res = await api.post<LoginResponse>('/auth/login', data);
    return res.data;
  },

  getCurrentUser: async (): Promise<ApiResponse<UserSummary>> => {
    const res = await api.get<ApiResponse<UserSummary>>('/auth/me');
    return res.data;
  },

  logout: async (): Promise<void> => {
    try {
      await api.post('/auth/logout');
    } finally {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    }
  },

  forgotPassword: async (data: {
    step: number;
    usn?: string;
    day?: string;
    month?: string;
    year?: string;
    otp?: string;
    newPassword?: string;
    confirmPassword?: string;
  }): Promise<ApiResponse<string>> => {
    const res = await api.post<ApiResponse<string>>('/auth/forgot-password', data);
    return res.data;
  },
};

export const publicApi = {
  getTerms: async (): Promise<ApiResponse<TermItem[]>> => {
    const res = await api.get<ApiResponse<TermItem[]>>('/public/terms');
    return res.data;
  },

  getTermLinks: async (): Promise<ApiResponse<TermLinkItem[]>> => {
    const res = await api.get<ApiResponse<TermLinkItem[]>>('/public/term-links');
    return res.data;
  },

  getLoginNotice: async (): Promise<ApiResponse<NoticeItem | null>> => {
    const res = await api.get<ApiResponse<NoticeItem | null>>('/public/notices/login');
    return res.data;
  },

  getAllNotices: async (): Promise<ApiResponse<NoticeItem[]>> => {
    const res = await api.get<ApiResponse<NoticeItem[]>>('/public/notices');
    return res.data;
  },
};

export const parentApi = {
  getDashboard: async (studentId?: number, termId?: number): Promise<ApiResponse<any>> => {
    const res = await api.get('/parent/dashboard', { params: { studentId, termId } });
    return res.data;
  },

  getProfile: async (studentId?: number): Promise<ApiResponse<StudentProfile>> => {
    const res = await api.get<ApiResponse<StudentProfile>>('/parent/profile', {
      params: { studentId },
    });
    return res.data;
  },

  getAttendance: async (studentId?: number, termId?: number): Promise<ApiResponse<AttendanceData>> => {
    const res = await api.get<ApiResponse<AttendanceData>>('/parent/attendance', {
      params: { studentId, termId },
    });
    return res.data;
  },

  getCieMarks: async (studentId?: number, termId?: number): Promise<ApiResponse<CieMarksData>> => {
    const res = await api.get<ApiResponse<CieMarksData>>('/parent/cie', {
      params: { studentId, termId },
    });
    return res.data;
  },

  getResults: async (studentId?: number, termId?: number): Promise<ApiResponse<ResultsData>> => {
    const res = await api.get<ApiResponse<ResultsData>>('/parent/results', {
      params: { studentId, termId },
    });
    return res.data;
  },

  getTimetable: async (studentId?: number): Promise<ApiResponse<TimetableData>> => {
    const res = await api.get<ApiResponse<TimetableData>>('/parent/timetable', {
      params: { studentId },
    });
    return res.data;
  },

  getAnnouncements: async (termId?: number): Promise<ApiResponse<AnnouncementItem[]>> => {
    const res = await api.get<ApiResponse<AnnouncementItem[]>>('/parent/announcements', {
      params: { termId },
    });
    return res.data;
  },

  getFees: async (studentId?: number, termId?: number): Promise<ApiResponse<FeeData>> => {
    const res = await api.get<ApiResponse<FeeData>>('/parent/fees', {
      params: { studentId, termId },
    });
    return res.data;
  },

  changePassword: async (data: {
    currentPassword: string;
    newPassword: string;
    confirmPassword: string;
  }): Promise<ApiResponse<string>> => {
    const res = await api.post<ApiResponse<string>>('/parent/change-password', data);
    return res.data;
  },

  getSiblings: async (studentId?: number): Promise<ApiResponse<SiblingSummary[]>> => {
    const res = await api.get<ApiResponse<SiblingSummary[]>>('/parent/siblings', {
      params: { studentId },
    });
    return res.data;
  },

  getLeaveRequests: async (studentId?: number): Promise<ApiResponse<LeaveRequestItem[]>> => {
    const res = await api.get<ApiResponse<LeaveRequestItem[]>>('/parent/leave', {
      params: { studentId },
    });
    return res.data;
  },

  submitLeaveRequest: async (data: { startDate: string; endDate: string; reason: string }, studentId?: number): Promise<ApiResponse<LeaveRequestItem>> => {
    const res = await api.post<ApiResponse<LeaveRequestItem>>('/parent/leave', data, {
      params: { studentId },
    });
    return res.data;
  },

  getFeedback: async (studentId?: number): Promise<ApiResponse<FeedbackItem[]>> => {
    const res = await api.get<ApiResponse<FeedbackItem[]>>('/parent/feedback', {
      params: { studentId },
    });
    return res.data;
  },

  submitFeedback: async (data: { category: string; subject: string; message: string }, studentId?: number): Promise<ApiResponse<FeedbackItem>> => {
    const res = await api.post<ApiResponse<FeedbackItem>>('/parent/feedback', data, {
      params: { studentId },
    });
    return res.data;
  },

  getMentorAppointments: async (studentId?: number): Promise<ApiResponse<MentorAppointmentItem[]>> => {
    const res = await api.get<ApiResponse<MentorAppointmentItem[]>>('/parent/mentors/appointments', {
      params: { studentId },
    });
    return res.data;
  },

  bookMentorAppointment: async (data: Partial<MentorAppointmentItem>, studentId?: number): Promise<ApiResponse<MentorAppointmentItem>> => {
    const res = await api.post<ApiResponse<MentorAppointmentItem>>('/parent/mentors/appointments', data, {
      params: { studentId },
    });
    return res.data;
  },

  payFee: async (feeId: number, amount: number, mode?: string, studentId?: number): Promise<ApiResponse<PaymentItem>> => {
    const res = await api.post<ApiResponse<PaymentItem>>('/parent/fees/pay', null, {
      params: { feeId, amount, mode, studentId },
    });
    return res.data;
  },

  getCalendarEvents: async (termId?: number): Promise<ApiResponse<CalendarEventItem[]>> => {
    const res = await api.get<ApiResponse<CalendarEventItem[]>>('/calendar/events', {
      params: { termId },
    });
    return res.data;
  },

  getNotifications: async (): Promise<ApiResponse<NotificationItem[]>> => {
    const res = await api.get<ApiResponse<NotificationItem[]>>('/parent/notifications');
    return res.data;
  },

  markNotificationRead: async (id: number): Promise<ApiResponse<void>> => {
    const res = await api.put<ApiResponse<void>>(`/parent/notifications/${id}/read`);
    return res.data;
  },
};

export const adminApi = {
  getStats: async (): Promise<ApiResponse<AdminStatsData>> => {
    const res = await api.get<ApiResponse<AdminStatsData>>('/admin/stats');
    return res.data;
  },

  listStudents: async (search?: string, departmentId?: number): Promise<ApiResponse<StudentProfile[]>> => {
    const res = await api.get<ApiResponse<StudentProfile[]>>('/admin/students', {
      params: { search, departmentId },
    });
    return res.data;
  },

  updateNotice: async (id: number, data: { title: string; content: string; isLoginNotice?: boolean }): Promise<ApiResponse<any>> => {
    const res = await api.put(`/admin/notices/${id}`, data);
    return res.data;
  },

  updateTenantSettings: async (id: number, data: Record<string, string>): Promise<ApiResponse<any>> => {
    const res = await api.put(`/admin/tenant/${id}/settings`, data);
    return res.data;
  },

  updateTenantModule: async (id: number, data: Record<string, any>): Promise<ApiResponse<any>> => {
    const res = await api.put(`/admin/tenant/${id}/module`, data);
    return res.data;
  },

  updateRules: async (id: number, data: Record<string, any>): Promise<ApiResponse<any>> => {
    const res = await api.put(`/admin/tenant/${id}/rules`, data);
    return res.data;
  },

  getAuditLogs: async (): Promise<ApiResponse<any[]>> => {
    const res = await api.get('/admin/audit-logs');
    return res.data;
  },
};

export const tenantApi = {
  getConfig: async (slug?: string): Promise<ApiResponse<TenantConfig>> => {
    const res = await api.get<ApiResponse<TenantConfig>>('/tenant/config', {
      params: { slug },
      headers: slug ? { 'X-Tenant': slug } : {},
    });
    return res.data;
  },
  list: async (): Promise<ApiResponse<any[]>> => {
    const res = await api.get('/tenant/list');
    return res.data;
  },
};

export default api;
