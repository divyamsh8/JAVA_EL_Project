import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { useTheme } from '../theme/ThemeContext';
import { publicApi, parentApi } from '../api/client';
import { TermItem, NotificationItem } from '../types';
import { LogOut, Calendar, Shield, Users, Globe, Sun, Moon, Bell } from 'lucide-react';
import { Dropdown } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

export const Header: React.FC = () => {
  const { user, activeTerm, setActiveTerm, siblings, activeStudentId, switchStudent, logout } = useAuth();
  const { tenantConfig, tenantSlug, setTenantSlug, isDarkMode, toggleDarkMode } = useTheme();
  const { i18n } = useTranslation();
  const [terms, setTerms] = useState<TermItem[]>([]);
  const [notifications, setNotifications] = useState<NotificationItem[]>([]);
  const navigate = useNavigate();

  useEffect(() => {
    publicApi.getTerms().then((res) => {
      if (res.success && res.data) {
        setTerms(res.data);
      }
    });

    if (user) {
      parentApi.getNotifications().then((res) => {
        if (res.success && res.data) {
          setNotifications(res.data);
        }
      });

      // Connect to Live SSE stream for real-time announcements/alerts
      try {
        const es = new EventSource('/api/v1/notifications/stream');
        es.addEventListener('NOTIFICATION', (event) => {
          try {
            const data: NotificationItem = JSON.parse(event.data);
            setNotifications((prev) => [data, ...prev]);
          } catch (e) {
            console.error('Failed to parse SSE notification', e);
          }
        });
        return () => es.close();
      } catch (err) {
        console.warn('SSE not supported or connection error', err);
      }
    }
  }, [user]);

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const changeLanguage = (lng: string) => {
    i18n.changeLanguage(lng);
    localStorage.setItem('portal_lang', lng);
  };

  const markRead = (id: number) => {
    parentApi.markNotificationRead(id).then(() => {
      setNotifications((prev) =>
        prev.map((n) => (n.id === id ? { ...n, isRead: true } : n))
      );
    });
  };

  const unreadCount = notifications.filter((n) => !n.isRead).length;
  const currentTermObj = terms.find((t) => t.code === activeTerm);
  const activeSibling = siblings.find((s) => s.studentId === activeStudentId) || (siblings.length > 0 ? siblings[0] : null);

  return (
    <header className="portal-topbar">
      {/* Brand & Crest */}
      <div className="d-flex align-items-center gap-2">
        <span className="fs-4">{tenantConfig?.theme?.crestIcon || '🏛️'}</span>
        <div>
          <h5 className="mb-0 fw-bold text-navy text-truncate" style={{ maxWidth: 280 }}>
            {tenantConfig?.name || 'Demo Institute of Engineering'}
          </h5>
          <span className="badge bg-secondary-subtle text-secondary border d-none d-md-inline" style={{ fontSize: '0.7rem' }}>
            {tenantConfig?.slug?.toUpperCase() || 'DIE'} Autonomous Portal
          </span>
        </div>
      </div>

      <div className="d-flex align-items-center gap-2 flex-wrap">
        {/* Dual-Tenant Switcher Pill */}
        <div className="btn-group btn-group-sm d-none d-lg-flex border rounded" role="group">
          <button
            type="button"
            className={`btn btn-sm ${tenantSlug === 'die' ? 'btn-primary' : 'btn-light text-muted'}`}
            onClick={() => setTenantSlug('die')}
            title="Demo Institute of Engineering (Contineo USN+DOB style)"
          >
            🏛️ DIE
          </button>
          <button
            type="button"
            className={`btn btn-sm ${tenantSlug === 'ait' ? 'btn-success' : 'btn-light text-muted'}`}
            onClick={() => setTenantSlug('ait')}
            title="Apex Institute of Tech (Emerald Password style)"
          >
            🎓 AIT
          </button>
        </div>

        {/* Multi-Child / Sibling Switcher Dropdown */}
        {siblings && siblings.length > 1 && (
          <Dropdown>
            <Dropdown.Toggle
              variant="outline-success"
              size="sm"
              className="d-flex align-items-center gap-1 fw-medium"
            >
              <Users size={14} />
              <span className="d-none d-sm-inline">Ward:</span>
              <span className="text-truncate" style={{ maxWidth: 120 }}>
                {activeSibling ? activeSibling.fullName : 'Select Ward'}
              </span>
            </Dropdown.Toggle>

            <Dropdown.Menu align="end">
              <Dropdown.Header className="small text-uppercase">Switch Enrolled Ward / Sibling</Dropdown.Header>
              {siblings.map((s) => (
                <Dropdown.Item
                  key={s.studentId}
                  active={s.studentId === activeStudentId}
                  onClick={() => switchStudent(s.studentId)}
                >
                  <div className="fw-bold">{s.fullName} ({s.usn})</div>
                  <div className="small text-muted">{s.departmentCode} · Semester {s.currentSem}</div>
                </Dropdown.Item>
              ))}
            </Dropdown.Menu>
          </Dropdown>
        )}

        {/* Term Switcher */}
        <Dropdown>
          <Dropdown.Toggle
            variant="outline-primary"
            size="sm"
            className="d-flex align-items-center gap-1 fw-medium"
          >
            <Calendar size={14} />
            <span className="d-none d-md-inline">{currentTermObj ? currentTermObj.name : 'Term'}</span>
          </Dropdown.Toggle>

          <Dropdown.Menu align="end">
            <Dropdown.Header>Active Academic Terms</Dropdown.Header>
            {terms.map((t) => (
              <Dropdown.Item
                key={t.id}
                active={t.code === activeTerm}
                onClick={() => setActiveTerm(t.code)}
              >
                {t.name} ({t.academicYear})
              </Dropdown.Item>
            ))}
          </Dropdown.Menu>
        </Dropdown>

        {/* Multilingual Selector */}
        <Dropdown>
          <Dropdown.Toggle variant="outline-secondary" size="sm" className="d-flex align-items-center gap-1 px-2">
            <Globe size={14} />
            <span className="text-uppercase" style={{ fontSize: '0.75rem' }}>{i18n.language.slice(0, 2)}</span>
          </Dropdown.Toggle>
          <Dropdown.Menu align="end">
            <Dropdown.Item onClick={() => changeLanguage('en')}>English (EN)</Dropdown.Item>
            <Dropdown.Item onClick={() => changeLanguage('kn')}>ಕನ್ನಡ (KN)</Dropdown.Item>
            <Dropdown.Item onClick={() => changeLanguage('hi')}>हिंदी (HI)</Dropdown.Item>
          </Dropdown.Menu>
        </Dropdown>

        {/* Dark Mode Toggle */}
        <button
          className="btn btn-outline-secondary btn-sm p-1 rounded-circle"
          onClick={toggleDarkMode}
          title={isDarkMode ? "Light Mode" : "Dark Mode"}
          style={{ width: 32, height: 32 }}
        >
          {isDarkMode ? <Sun size={15} className="text-warning" /> : <Moon size={15} />}
        </button>

        {/* Real-time Notification Center (SSE) */}
        <Dropdown>
          <Dropdown.Toggle
            variant="outline-secondary"
            size="sm"
            className="position-relative p-1 rounded-circle"
            style={{ width: 32, height: 32 }}
          >
            <Bell size={15} />
            {unreadCount > 0 && (
              <span
                className="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style={{ fontSize: '0.6rem' }}
              >
                {unreadCount}
              </span>
            )}
          </Dropdown.Toggle>

          <Dropdown.Menu align="end" style={{ width: 320, maxHeight: 380, overflowY: 'auto' }}>
            <Dropdown.Header className="d-flex justify-content-between align-items-center py-2 border-bottom">
              <span className="fw-bold">Notifications Center</span>
              <span className="badge bg-primary">{notifications.length}</span>
            </Dropdown.Header>
            {notifications.length === 0 ? (
              <div className="p-3 text-center text-muted small">No notifications yet</div>
            ) : (
              notifications.map((n) => (
                <Dropdown.Item
                  key={n.id}
                  className={`text-wrap border-bottom py-2 ${!n.isRead ? 'bg-light' : ''}`}
                  onClick={() => markRead(n.id)}
                >
                  <div className="d-flex justify-content-between align-items-center">
                    <span className="fw-semibold small text-primary">{n.title}</span>
                    <span className="badge bg-secondary-subtle text-secondary" style={{ fontSize: '0.65rem' }}>{n.type}</span>
                  </div>
                  <div className="text-muted small mt-1" style={{ fontSize: '0.78rem' }}>{n.message}</div>
                </Dropdown.Item>
              ))
            )}
          </Dropdown.Menu>
        </Dropdown>

        {/* User Info & Logout */}
        <div className="d-flex align-items-center gap-2 border-start ps-2">
          <div className="text-end d-none d-xl-block">
            <div className="fw-bold text-dark fs-6 text-truncate" style={{ maxWidth: 140 }}>
              {user?.displayName || user?.username}
            </div>
            <div className="text-muted small" style={{ fontSize: '0.72rem' }}>
              {user?.role?.includes('ADMIN') ? (
                <span className="text-danger fw-semibold d-inline-flex align-items-center gap-1">
                  <Shield size={12} /> Admin
                </span>
              ) : (
                <span>Parent ({user?.usn || user?.username})</span>
              )}
            </div>
          </div>

          <button
            onClick={handleLogout}
            className="btn btn-outline-danger btn-sm d-flex align-items-center gap-1"
            title="Sign Out"
          >
            <LogOut size={15} />
            <span className="d-none d-sm-inline">Logout</span>
          </button>
        </div>
      </div>
    </header>
  );
};
