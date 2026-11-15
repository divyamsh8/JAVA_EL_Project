import React, { useState, useEffect } from 'react';
import { useNavigate, useLocation, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useTheme } from '../theme/ThemeContext';
import { authApi, publicApi } from '../api/client';
import { NoticeItem, TermLinkItem } from '../types';
import { FailedLoginModal } from '../components/FailedLoginModal';
import { Bell, Lock, User as UserIcon, Shield, Sparkles, Calendar, Moon, Sun } from 'lucide-react';

export const LoginPage: React.FC = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const { login, setActiveTerm } = useAuth();
  const { tenantConfig, tenantSlug, setTenantSlug, isDarkMode, toggleDarkMode } = useTheme();

  // Mode: 'student' or 'admin'
  const [mode, setMode] = useState<'student' | 'admin'>('student');

  // Form fields
  const [username, setUsername] = useState('1DM24CS001');
  const [day, setDay] = useState('15');
  const [month, setMonth] = useState('Apr');
  const [year, setYear] = useState('2005');
  const [password, setPassword] = useState('');
  const [adminPassword, setAdminPassword] = useState('Admin@123');

  // Term links & Notice
  const [termLinks, setTermLinks] = useState<TermLinkItem[]>([]);
  const [notice, setNotice] = useState<NoticeItem | null>(null);
  const [selectedTermCode, setSelectedTermCode] = useState('parentseven');

  // Modal error state
  const [modalOpen, setModalOpen] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [attemptsRemaining, setAttemptsRemaining] = useState<number | undefined>(undefined);
  const [submitting, setSubmitting] = useState(false);

  // Sync active term based on route
  useEffect(() => {
    const path = location.pathname;
    let code = 'parentseven';
    if (path.includes('parentssupplypart2')) {
      code = 'parentssupplypart2';
    } else if (path.includes('parentssupply')) {
      code = 'parentssupply';
    }
    setSelectedTermCode(code);
    setActiveTerm(code);
  }, [location.pathname, setActiveTerm]);

  // Adjust defaults when tenant changes
  useEffect(() => {
    if (tenantSlug === 'ait') {
      setUsername('2AT24AI001');
      setPassword('Student@123');
    } else {
      setUsername('1DM24CS001');
      setDay('15');
      setMonth('Apr');
      setYear('2005');
    }
  }, [tenantSlug]);

  // Load public notices & term links
  useEffect(() => {
    publicApi.getLoginNotice().then((res) => {
      if (res.success && res.data) {
        setNotice(res.data);
      }
    });

    if (tenantConfig && tenantConfig.termLinks && tenantConfig.termLinks.length > 0) {
      setTermLinks(tenantConfig.termLinks);
    } else {
      publicApi.getTermLinks().then((res) => {
        if (res.success && res.data) {
          setTermLinks(res.data);
        }
      });
    }
  }, [tenantConfig]);

  const isDobDropdown = tenantConfig?.loginSchema?.credentialType === 'DOB_DROPDOWN';

  const handleStudentSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);

    try {
      const loginPayload: any = {
        username: username.trim(),
        termCode: selectedTermCode,
      };

      if (isDobDropdown) {
        loginPayload.day = day;
        loginPayload.month = month;
        loginPayload.year = year;
      } else {
        loginPayload.password = password;
      }

      const res = await authApi.login(loginPayload);

      if (res.success && res.token && res.user) {
        login(res.token, res.user, res.siblings);
        navigate('/dashboard');
      } else {
        setErrorMessage(res.message || 'You have entered an invalid username or password.');
        setAttemptsRemaining(res.attemptsRemaining);
        setModalOpen(true);
      }
    } catch (err: any) {
      const data = err.response?.data;
      setErrorMessage(data?.message || 'You have entered an invalid username or password.');
      setAttemptsRemaining(data?.attemptsRemaining);
      setModalOpen(true);
    } finally {
      setSubmitting(false);
    }
  };

  const handleAdminSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);

    try {
      const res = await authApi.login({
        username: username.trim(),
        password: adminPassword,
      });

      if (res.success && res.token && res.user) {
        login(res.token, res.user, res.siblings);
        if (res.user.role.includes('ADMIN')) {
          navigate('/admin');
        } else {
          navigate('/dashboard');
        }
      } else {
        setErrorMessage(res.message || 'Invalid administrator credentials.');
        setAttemptsRemaining(res.attemptsRemaining);
        setModalOpen(true);
      }
    } catch (err: any) {
      const data = err.response?.data;
      setErrorMessage(data?.message || 'Invalid administrator credentials.');
      setAttemptsRemaining(data?.attemptsRemaining);
      setModalOpen(true);
    } finally {
      setSubmitting(false);
    }
  };

  // Quick autofill demo accounts
  const autofillDemo = (type: 'die-aarav' | 'die-ananya' | 'ait-dev' | 'admin') => {
    if (type === 'die-aarav') {
      setTenantSlug('die');
      setMode('student');
      setUsername('1DM24CS001');
      setDay('15');
      setMonth('Apr');
      setYear('2005');
    } else if (type === 'die-ananya') {
      setTenantSlug('die');
      setMode('student');
      setUsername('1DM24EC002');
      setDay('22');
      setMonth('Aug');
      setYear('2005');
    } else if (type === 'ait-dev') {
      setTenantSlug('ait');
      setMode('student');
      setUsername('2AT24AI001');
      setPassword('Student@123');
    } else if (type === 'admin') {
      setMode('admin');
      setUsername(tenantSlug === 'ait' ? 'apex_admin' : 'admin');
      setAdminPassword('Admin@123');
    }
  };

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: currentYear - 1980 + 1 }, (_, i) => String(currentYear - i));
  const days = Array.from({ length: 31 }, (_, i) => String(i + 1).padStart(2, '0'));
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

  return (
    <div className="cn-landing">
      {/* Quick Demo Switcher Bar at top */}
      <div className="position-absolute top-0 start-0 end-0 py-1 px-3 bg-dark text-white d-flex justify-content-between align-items-center z-3" style={{ fontSize: '0.75rem', opacity: 0.95 }}>
        <div className="d-flex align-items-center gap-2 flex-wrap">
          <span className="text-warning fw-bold d-none d-sm-inline">
            <Sparkles size={13} className="me-1" />
            Quick Demo Autofill:
          </span>
          <button
            type="button"
            className="btn btn-outline-light btn-sm py-0 px-2"
            style={{ fontSize: '0.72rem' }}
            onClick={() => autofillDemo('die-aarav')}
          >
            🏛️ Aarav (DIE · DOB)
          </button>
          <button
            type="button"
            className="btn btn-outline-light btn-sm py-0 px-2"
            style={{ fontSize: '0.72rem' }}
            onClick={() => autofillDemo('die-ananya')}
          >
            👧 Ananya (DIE · Sibling)
          </button>
          <button
            type="button"
            className="btn btn-outline-light btn-sm py-0 px-2"
            style={{ fontSize: '0.72rem' }}
            onClick={() => autofillDemo('ait-dev')}
          >
            🎓 Dev (AIT · Password)
          </button>
          <button
            type="button"
            className="btn btn-outline-warning btn-sm py-0 px-2"
            style={{ fontSize: '0.72rem' }}
            onClick={() => autofillDemo('admin')}
          >
            🛡️ Admin
          </button>
        </div>

        <div className="d-flex align-items-center gap-2">
          {/* Tenant Switcher Pill */}
          <div className="btn-group btn-group-sm" role="group">
            <button
              type="button"
              className={`btn btn-sm py-0 px-2 ${tenantSlug === 'die' ? 'btn-primary' : 'btn-outline-secondary text-white'}`}
              style={{ fontSize: '0.72rem' }}
              onClick={() => setTenantSlug('die')}
            >
              DIE (Contineo)
            </button>
            <button
              type="button"
              className={`btn btn-sm py-0 px-2 ${tenantSlug === 'ait' ? 'btn-success' : 'btn-outline-secondary text-white'}`}
              style={{ fontSize: '0.72rem' }}
              onClick={() => setTenantSlug('ait')}
            >
              AIT (Emerald)
            </button>
          </div>

          <button
            type="button"
            className="btn btn-outline-light btn-sm py-0 px-1 rounded-circle"
            onClick={toggleDarkMode}
            title={isDarkMode ? 'Light Mode' : 'Dark Mode'}
          >
            {isDarkMode ? <Sun size={12} className="text-warning" /> : <Moon size={12} />}
          </button>
        </div>
      </div>

      {/* Left Panel */}
      <div className="cn-bgpart1" style={{ paddingTop: '3.2rem' }}>
        <div className="cn-logo-badge">
          <div className="cn-crest-icon">{tenantConfig?.theme?.crestIcon || '🏛️'}</div>
          <div>
            <h1 className="cn-inst-title">{tenantConfig?.name || 'Demo Institute of Engineering'}</h1>
            <p className="cn-inst-subtitle">
              An Autonomous Institution | Approved by AICTE, New Delhi | Accredited by NAAC with 'A+' Grade
            </p>
          </div>
        </div>

        <p className="cn-welcome-text">
          {tenantConfig?.welcomeText || (
            <>
              Welcome to the official <strong>Contineo Institutional Parent Portal</strong> of Demo Institute of
              Engineering. This portal provides parents and guardians with secure, real-time access to their ward's
              academic progress, internal assessment marks (CIE), class attendance records, weekly timetables, and fee
              accounts.
            </>
          )}
        </p>

        {/* Notice Board */}
        <div className="cn-notice-card">
          <div className="cn-notice-header">
            <Bell size={20} />
            <span>Notice Board</span>
          </div>
          <p className="cn-notice-content">
            {tenantConfig?.noticeText || notice?.content || (
              <>
                Welcome to the preview of the new mobile friendly parent portal. Even term 2026 academic sessions are
                now active. Please check the attendance and CIE marks regularly.
              </>
            )}
          </p>
        </div>

        {/* External Sub-Portal Links */}
        <div className="cn-term-links">
          <div className="text-muted small mb-2 fw-semibold">Term Sub-Portals:</div>
          {termLinks.length > 0 ? (
            termLinks.map((link) => (
              <a
                key={link.id}
                href={link.routeSlug}
                className="cn-term-link"
                onClick={(e) => {
                  e.preventDefault();
                  setSelectedTermCode(link.termCode);
                  setActiveTerm(link.termCode);
                }}
              >
                {link.title}
              </a>
            ))
          ) : (
            <>
              <Link to="/parentseven/" className="cn-term-link">
                Click here to access Even term 2026
              </Link>
              <Link to="/parentssupply/" className="cn-term-link">
                Click here to access Supply/Summer term 2025-2026
              </Link>
              <Link to="/parentssupplypart2/" className="cn-term-link">
                Click here to access Supply/Summer term 2025-2026 Part 2
              </Link>
            </>
          )}
        </div>
      </div>

      {/* Right Panel: Form Card */}
      <div className="cn-bgpart2" style={{ paddingTop: '3.2rem' }}>
        <div className="cn-login-card">
          {/* Mode Switcher Tabs */}
          <div className="d-flex border-bottom mb-3">
            <button
              type="button"
              className={`btn btn-link text-decoration-none flex-fill pb-2 fw-bold ${
                mode === 'student' ? 'text-primary border-bottom border-primary border-2' : 'text-muted'
              }`}
              onClick={() => {
                setMode('student');
                if (tenantSlug === 'ait') {
                  setUsername('2AT24AI001');
                } else {
                  setUsername('1DM24CS001');
                }
              }}
            >
              <UserIcon size={16} className="me-1 inline-block" />
              Parent Login
            </button>
            <button
              type="button"
              className={`btn btn-link text-decoration-none flex-fill pb-2 fw-bold ${
                mode === 'admin' ? 'text-primary border-bottom border-primary border-2' : 'text-muted'
              }`}
              onClick={() => {
                setMode('admin');
                setUsername(tenantSlug === 'ait' ? 'apex_admin' : 'admin');
              }}
            >
              <Shield size={16} className="me-1 inline-block" />
              Staff / Admin
            </button>
          </div>

          <h2 className="cn-login-title">
            {mode === 'student' ? (
              tenantSlug === 'ait' ? 'Apex Student / Parent Access' : 'Parent Login (Even term 2026)'
            ) : (
              'Institutional Administrator Login'
            )}
          </h2>

          {mode === 'student' ? (
            /* Student/Parent Login Form */
            <form onSubmit={handleStudentSubmit}>
              {/* Username / USN */}
              <div className="mb-3">
                <label className="form-label cn-label">
                  <UserIcon size={14} className="me-1" />
                  {tenantConfig?.loginSchema?.identifierField === 'ROLL_NO' ? 'Roll Number' : 'Student USN'}
                </label>
                <input
                  type="text"
                  className="form-control cn-input"
                  placeholder={tenantSlug === 'ait' ? 'e.g., 2AT24AI001' : 'e.g., 1DM24CS001'}
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  required
                />
              </div>

              {/* Dynamic Credential: DOB Dropdowns OR Password */}
              {isDobDropdown ? (
                <div className="mb-3">
                  <label className="form-label cn-label">
                    <Calendar size={14} className="me-1" />
                    Student Date of Birth (DOB)
                  </label>
                  <div className="cn-dob-grid">
                    <select
                      className="form-select cn-select"
                      value={day}
                      onChange={(e) => setDay(e.target.value)}
                      required
                    >
                      <option value="">Day</option>
                      {days.map((d) => (
                        <option key={d} value={d}>
                          {d}
                        </option>
                      ))}
                    </select>

                    <select
                      className="form-select cn-select"
                      value={month}
                      onChange={(e) => setMonth(e.target.value)}
                      required
                    >
                      <option value="">Month</option>
                      {months.map((m) => (
                        <option key={m} value={m}>
                          {m}
                        </option>
                      ))}
                    </select>

                    <select
                      className="form-select cn-select"
                      value={year}
                      onChange={(e) => setYear(e.target.value)}
                      required
                    >
                      <option value="">Year</option>
                      {years.map((y) => (
                        <option key={y} value={y}>
                          {y}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div className="form-text small text-muted">Format: DD / MMM / YYYY (Matches student register)</div>
                </div>
              ) : (
                <div className="mb-3">
                  <label className="form-label cn-label">
                    <Lock size={14} className="me-1" />
                    Student Portal Password
                  </label>
                  <input
                    type="password"
                    className="form-control cn-input"
                    placeholder="Enter student password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required
                  />
                  <div className="form-text small text-muted">Demo Password: Student@123</div>
                </div>
              )}

              {/* Submit Button */}
              <button
                type="submit"
                className="btn btn-primary cn-btn-submit w-100 mb-3"
                disabled={submitting}
              >
                {submitting ? 'Authenticating...' : 'Sign In to Parent Portal'}
              </button>

              <div className="text-center">
                <Link to="/forgot-password" className="text-decoration-none small text-muted">
                  Forgot Password / Reset Instructions?
                </Link>
              </div>
            </form>
          ) : (
            /* Administrator Login Form */
            <form onSubmit={handleAdminSubmit}>
              <div className="mb-3">
                <label className="form-label cn-label">
                  <UserIcon size={14} className="me-1" />
                  Admin Username
                </label>
                <input
                  type="text"
                  className="form-control cn-input"
                  placeholder="admin or hod_cse"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  required
                />
              </div>

              <div className="mb-3">
                <label className="form-label cn-label">
                  <Lock size={14} className="me-1" />
                  Password
                </label>
                <input
                  type="password"
                  className="form-control cn-input"
                  placeholder="Enter administrator password"
                  value={adminPassword}
                  onChange={(e) => setAdminPassword(e.target.value)}
                  required
                />
                <div className="form-text small text-muted">Demo Password: Admin@123</div>
              </div>

              <button
                type="submit"
                className="btn btn-warning cn-btn-submit text-dark fw-bold w-100 mb-3"
                disabled={submitting}
              >
                {submitting ? 'Verifying Admin...' : 'Sign In as Administrator'}
              </button>
            </form>
          )}

          {/* Security Notice */}
          <div className="cn-security-notice mt-3 pt-3 border-top text-center text-muted small">
            🔒 Protected by Spring Security 6 & Bucket4j Rate Limiting. 5 failed attempts locks access for 15 minutes.
          </div>
        </div>
      </div>

      {/* Failed Login Modal */}
      <FailedLoginModal
        show={modalOpen}
        onHide={() => setModalOpen(false)}
        message={errorMessage}
        attemptsRemaining={attemptsRemaining}
      />
    </div>
  );
};
