import React from 'react';
import { NavLink } from 'react-router-dom';
import {
  LayoutDashboard,
  Users,
  BellRing,
  FileText,
  ArrowLeft,
  Shield,
  Palette,
  Sliders,
  Settings
} from 'lucide-react';

export const AdminSidebar: React.FC = () => {
  return (
    <aside className="portal-sidebar bg-dark">
      <div className="portal-sidebar-header">
        <Shield size={24} className="text-warning" />
        <div>
          <div className="fw-bold text-white fs-6">ADMIN CONSOLE</div>
          <div className="text-muted small">Institutional Administration</div>
        </div>
      </div>

      <ul className="portal-sidebar-nav">
        <li>
          <NavLink to="/admin" end className={({ isActive }) => (isActive ? 'active' : '')}>
            <LayoutDashboard size={18} />
            <span>Overview & Alerts</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/admin/theme" className={({ isActive }) => (isActive ? 'active' : '')}>
            <Palette size={18} />
            <span>Theme & Branding</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/admin/modules" className={({ isActive }) => (isActive ? 'active' : '')}>
            <Sliders size={18} />
            <span>Modules Registry</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/admin/rules" className={({ isActive }) => (isActive ? 'active' : '')}>
            <Settings size={18} />
            <span>Rules Engine</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/admin/students" className={({ isActive }) => (isActive ? 'active' : '')}>
            <Users size={18} />
            <span>Students Directory</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/admin/notices" className={({ isActive }) => (isActive ? 'active' : '')}>
            <BellRing size={18} />
            <span>Notice Board Editor</span>
          </NavLink>
        </li>
        <li>
          <NavLink to="/admin/logs" className={({ isActive }) => (isActive ? 'active' : '')}>
            <FileText size={18} />
            <span>Security Audit Logs</span>
          </NavLink>
        </li>

        <li className="mt-4 pt-3 border-top border-secondary">
          <NavLink to="/dashboard" className="text-info">
            <ArrowLeft size={18} />
            <span>Back to Parent View</span>
          </NavLink>
        </li>
      </ul>
    </aside>
  );
};
