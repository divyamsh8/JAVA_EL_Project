import React from 'react';
import { NavLink } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useTheme } from '../theme/ThemeContext';
import { useTranslation } from 'react-i18next';
import {
  LayoutDashboard,
  User,
  CalendarCheck,
  FileSpreadsheet,
  Award,
  Clock,
  Bell,
  CreditCard,
  KeyRound,
  ShieldAlert,
  Calendar,
  Send,
  MessageSquare,
  UserCheck,
} from 'lucide-react';

export const Sidebar: React.FC = () => {
  const { user } = useAuth();
  const { tenantConfig } = useTheme();
  const { t } = useTranslation();
  const isAdmin = user?.role?.includes('ADMIN');

  return (
    <aside className="portal-sidebar">
      <div className="portal-sidebar-header">
        <div className="cn-crest-icon" style={{ width: 42, height: 42, fontSize: 20 }}>
          {tenantConfig?.theme?.crestIcon || '🏛️'}
        </div>
        <div>
          <div className="fw-bold text-white fs-6 text-truncate" style={{ maxWidth: 170 }}>
            {tenantConfig?.code || 'DIE'} PARENT PORTAL
          </div>
          <div className="text-secondary small">Academic Year 2025-26</div>
        </div>
      </div>

      <ul className="portal-sidebar-nav">
        <li>
          <NavLink to="/dashboard" className={({ isActive }) => (isActive ? 'active' : '')}>
            <LayoutDashboard size={18} />
            <span>{t('nav.dashboard')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/profile" className={({ isActive }) => (isActive ? 'active' : '')}>
            <User size={18} />
            <span>{t('nav.profile')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/attendance" className={({ isActive }) => (isActive ? 'active' : '')}>
            <CalendarCheck size={18} />
            <span>{t('nav.attendance')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/cie" className={({ isActive }) => (isActive ? 'active' : '')}>
            <FileSpreadsheet size={18} />
            <span>{t('nav.cie')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/results" className={({ isActive }) => (isActive ? 'active' : '')}>
            <Award size={18} />
            <span>{t('nav.results')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/timetable" className={({ isActive }) => (isActive ? 'active' : '')}>
            <Clock size={18} />
            <span>{t('nav.timetable')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/calendar" className={({ isActive }) => (isActive ? 'active' : '')}>
            <Calendar size={18} />
            <span>{t('nav.calendar')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/fees" className={({ isActive }) => (isActive ? 'active' : '')}>
            <CreditCard size={18} />
            <span>{t('nav.fees')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/announcements" className={({ isActive }) => (isActive ? 'active' : '')}>
            <Bell size={18} />
            <span>{t('nav.announcements')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/leave" className={({ isActive }) => (isActive ? 'active' : '')}>
            <Send size={18} />
            <span>{t('nav.leave')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/mentors" className={({ isActive }) => (isActive ? 'active' : '')}>
            <UserCheck size={18} />
            <span>{t('nav.mentors')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/feedback" className={({ isActive }) => (isActive ? 'active' : '')}>
            <MessageSquare size={18} />
            <span>{t('nav.feedback')}</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/change-password" className={({ isActive }) => (isActive ? 'active' : '')}>
            <KeyRound size={18} />
            <span>Change Password</span>
          </NavLink>
        </li>

        {isAdmin && (
          <li className="mt-4 pt-3 border-top border-secondary">
            <NavLink to="/admin" className={({ isActive }) => (isActive ? 'active text-warning' : 'text-warning')}>
              <ShieldAlert size={18} />
              <span>Admin Console</span>
            </NavLink>
          </li>
        )}
      </ul>
    </aside>
  );
};
