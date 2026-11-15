import React from 'react';
import { BrowserRouter, Routes, Route, Navigate, Outlet } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import { ThemeProvider } from './theme/ThemeContext';
import { LoginPage } from './pages/LoginPage';
import { ForgotPasswordPage } from './pages/ForgotPasswordPage';
import { DashboardPage } from './pages/DashboardPage';
import { ProfilePage } from './pages/ProfilePage';
import { AttendancePage } from './pages/AttendancePage';
import { CieMarksPage } from './pages/CieMarksPage';
import { ResultsPage } from './pages/ResultsPage';
import { TimetablePage } from './pages/TimetablePage';
import { AnnouncementsPage } from './pages/AnnouncementsPage';
import { FeesPage } from './pages/FeesPage';
import { CalendarPage } from './pages/CalendarPage';
import { LeaveRequestPage } from './pages/LeaveRequestPage';
import { FeedbackPage } from './pages/FeedbackPage';
import { MentorAppointmentPage } from './pages/MentorAppointmentPage';
import { ChangePasswordPage } from './pages/ChangePasswordPage';
import { AdminDashboardPage } from './pages/AdminDashboardPage';
import { AdminStudentsPage } from './pages/AdminStudentsPage';
import { AdminNoticesPage } from './pages/AdminNoticesPage';
import { AdminLogsPage } from './pages/AdminLogsPage';
import { AdminThemePage } from './pages/AdminThemePage';
import { AdminModulesPage } from './pages/AdminModulesPage';
import { AdminRulesPage } from './pages/AdminRulesPage';

import { Header } from './components/Header';
import { Sidebar } from './components/Sidebar';
import { AdminSidebar } from './components/AdminSidebar';
import { Footer } from './components/Footer';
import { MobileBottomNav } from './components/MobileBottomNav';

const ParentLayout: React.FC = () => {
  return (
    <div className="portal-layout">
      <Sidebar />
      <div className="portal-main">
        <Header />
        <main className="portal-content pb-5 mb-4 mb-md-0 pb-md-0">
          <Outlet />
        </main>
        <Footer />
        <MobileBottomNav />
      </div>
    </div>
  );
};

const AdminLayout: React.FC = () => {
  return (
    <div className="portal-layout">
      <AdminSidebar />
      <div className="portal-main">
        <Header />
        <main className="portal-content">
          <Outlet />
        </main>
        <Footer />
      </div>
    </div>
  );
};

const ProtectedRoute: React.FC<{ children: React.ReactElement }> = ({ children }) => {
  const { user, isLoading } = useAuth();

  if (isLoading) {
    return (
      <div className="min-vh-100 d-flex justify-content-center align-items-center">
        <div className="spinner-border text-primary" />
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  return children;
};

const AdminRoute: React.FC<{ children: React.ReactElement }> = ({ children }) => {
  const { user, isLoading } = useAuth();

  if (isLoading) {
    return (
      <div className="min-vh-100 d-flex justify-content-center align-items-center">
        <div className="spinner-border text-warning" />
      </div>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  const role = user.role.toUpperCase();
  if (!role.includes('ADMIN')) {
    return <Navigate to="/dashboard" replace />;
  }

  return children;
};

export const App: React.FC = () => {
  return (
    <ThemeProvider>
      <AuthProvider>
        <BrowserRouter>
          <Routes>
            {/* Public & Term-Scoped Login Routes */}
            <Route path="/" element={<LoginPage />} />
            <Route path="/login" element={<LoginPage />} />
            <Route path="/parentseven" element={<LoginPage />} />
            <Route path="/parentseven/" element={<LoginPage />} />
            <Route path="/parentssupply" element={<LoginPage />} />
            <Route path="/parentssupply/" element={<LoginPage />} />
            <Route path="/parentssupplypart2" element={<LoginPage />} />
            <Route path="/parentssupplypart2/" element={<LoginPage />} />
            <Route path="/forgot-password" element={<ForgotPasswordPage />} />

            {/* Authenticated Parent Portal Routes */}
            <Route
              element={
                <ProtectedRoute>
                  <ParentLayout />
                </ProtectedRoute>
              }
            >
              <Route path="/dashboard" element={<DashboardPage />} />
              <Route path="/profile" element={<ProfilePage />} />
              <Route path="/attendance" element={<AttendancePage />} />
              <Route path="/cie" element={<CieMarksPage />} />
              <Route path="/results" element={<ResultsPage />} />
              <Route path="/timetable" element={<TimetablePage />} />
              <Route path="/calendar" element={<CalendarPage />} />
              <Route path="/announcements" element={<AnnouncementsPage />} />
              <Route path="/fees" element={<FeesPage />} />
              <Route path="/leave" element={<LeaveRequestPage />} />
              <Route path="/feedback" element={<FeedbackPage />} />
              <Route path="/mentors" element={<MentorAppointmentPage />} />
              <Route path="/change-password" element={<ChangePasswordPage />} />
            </Route>

            {/* Authenticated Admin Console Routes */}
            <Route
              path="/admin"
              element={
                <AdminRoute>
                  <AdminLayout />
                </AdminRoute>
              }
            >
              <Route index element={<AdminDashboardPage />} />
              <Route path="theme" element={<AdminThemePage />} />
              <Route path="modules" element={<AdminModulesPage />} />
              <Route path="rules" element={<AdminRulesPage />} />
              <Route path="students" element={<AdminStudentsPage />} />
              <Route path="notices" element={<AdminNoticesPage />} />
              <Route path="logs" element={<AdminLogsPage />} />
            </Route>

            {/* Fallback */}
            <Route path="*" element={<Navigate to="/dashboard" replace />} />
          </Routes>
        </BrowserRouter>
      </AuthProvider>
    </ThemeProvider>
  );
};

export default App;
