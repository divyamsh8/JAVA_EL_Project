import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { authApi } from '../api/client';
import { CheckCircle2, ArrowLeft, AlertCircle } from 'lucide-react';

export const ForgotPasswordPage: React.FC = () => {
  const navigate = useNavigate();
  const [step, setStep] = useState<number>(1);
  const [usn, setUsn] = useState('');
  const [day, setDay] = useState('15');
  const [month, setMonth] = useState('Apr');
  const [year, setYear] = useState('2005');
  const [otp, setOtp] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');

  const [error, setError] = useState('');
  const [successMsg, setSuccessMsg] = useState('');
  const [loading, setLoading] = useState(false);

  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: currentYear - 1960 + 1 }, (_, i) => String(currentYear - i));
  const days = Array.from({ length: 31 }, (_, i) => String(i + 1).padStart(2, '0'));
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

  const handleStep1 = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const res = await authApi.forgotPassword({
        step: 1,
        usn: usn.trim(),
        day,
        month,
        year,
      });
      if (res.success) {
        setStep(2);
        setOtp('123456'); // Pre-fill or show demo OTP
      } else {
        setError(res.message || 'Validation failed');
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Date of birth does not match institutional records.');
    } finally {
      setLoading(false);
    }
  };

  const handleStep2 = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const res = await authApi.forgotPassword({
        step: 2,
        usn: usn.trim(),
        otp: otp.trim(),
      });
      if (res.success) {
        setStep(3);
      } else {
        setError(res.message || 'Invalid OTP');
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Invalid OTP');
    } finally {
      setLoading(false);
    }
  };

  const handleStep3 = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    if (newPassword !== confirmPassword) {
      setError('Passwords do not match');
      return;
    }
    setLoading(true);
    try {
      const res = await authApi.forgotPassword({
        step: 3,
        usn: usn.trim(),
        newPassword,
        confirmPassword,
      });
      if (res.success) {
        setSuccessMsg('Your password has been successfully reset! Redirecting to login...');
        setTimeout(() => navigate('/login'), 2500);
      } else {
        setError(res.message || 'Reset failed');
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Reset failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-vh-100 d-flex flex-column justify-content-center align-items-center bg-light py-5">
      <div className="card shadow-sm border-0" style={{ maxWidth: 480, width: '100%' }}>
        <div className="card-body p-4 p-md-5">
          <div className="text-center mb-4">
            <div className="cn-crest-icon mx-auto mb-3">🏛️</div>
            <h3 className="fw-bold text-dark">Password Recovery</h3>
            <p className="text-muted small">Contineo Institutional Self-Service Portal</p>
          </div>

          {error && (
            <div className="alert alert-danger d-flex align-items-center gap-2 small py-2">
              <AlertCircle size={16} />
              <span>{error}</span>
            </div>
          )}

          {successMsg && (
            <div className="alert alert-success d-flex align-items-center gap-2 small py-2">
              <CheckCircle2 size={16} />
              <span>{successMsg}</span>
            </div>
          )}

          {/* Stepper indicator */}
          <div className="d-flex justify-content-between mb-4 position-relative">
            <div className={`badge ${step >= 1 ? 'bg-primary' : 'bg-secondary'} px-3 py-2`}>
              1. Verify USN
            </div>
            <div className={`badge ${step >= 2 ? 'bg-primary' : 'bg-secondary'} px-3 py-2`}>
              2. Mobile OTP
            </div>
            <div className={`badge ${step >= 3 ? 'bg-primary' : 'bg-secondary'} px-3 py-2`}>
              3. New Password
            </div>
          </div>

          {step === 1 && (
            <form onSubmit={handleStep1}>
              <div className="mb-3">
                <label className="form-label fw-bold small text-secondary">Student USN</label>
                <input
                  type="text"
                  className="form-control"
                  placeholder="e.g. 1DM24CS001"
                  value={usn}
                  onChange={(e) => setUsn(e.target.value.toUpperCase())}
                  required
                />
              </div>

              <div className="mb-4">
                <label className="form-label fw-bold small text-secondary">Registered Date of Birth</label>
                <div className="cn-dob-grid">
                  <select
                    className="form-select form-select-sm"
                    value={day}
                    onChange={(e) => setDay(e.target.value)}
                  >
                    {days.map((d) => (
                      <option key={d} value={d}>
                        {d}
                      </option>
                    ))}
                  </select>

                  <select
                    className="form-select form-select-sm"
                    value={month}
                    onChange={(e) => setMonth(e.target.value)}
                  >
                    {months.map((m) => (
                      <option key={m} value={m}>
                        {m}
                      </option>
                    ))}
                  </select>

                  <select
                    className="form-select form-select-sm"
                    value={year}
                    onChange={(e) => setYear(e.target.value)}
                  >
                    {years.map((y) => (
                      <option key={y} value={y}>
                        {y}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <button type="submit" className="btn btn-primary w-100 py-2 fw-semibold" disabled={loading}>
                {loading ? 'Verifying...' : 'Continue to OTP'}
              </button>
            </form>
          )}

          {step === 2 && (
            <form onSubmit={handleStep2}>
              <div className="alert alert-info small mb-3">
                An SMS verification code has been dispatched to the registered parent mobile number.
                <div className="fw-bold mt-1">Demo OTP: 123456</div>
              </div>

              <div className="mb-4">
                <label className="form-label fw-bold small text-secondary">Enter 6-Digit OTP</label>
                <input
                  type="text"
                  className="form-control text-center fs-4 letter-spacing-2"
                  maxLength={6}
                  value={otp}
                  onChange={(e) => setOtp(e.target.value)}
                  required
                />
              </div>

              <button type="submit" className="btn btn-primary w-100 py-2 fw-semibold" disabled={loading}>
                {loading ? 'Verifying OTP...' : 'Verify OTP'}
              </button>
            </form>
          )}

          {step === 3 && (
            <form onSubmit={handleStep3}>
              <div className="mb-3">
                <label className="form-label fw-bold small text-secondary">New Password</label>
                <input
                  type="password"
                  className="form-control"
                  placeholder="Min 6 characters"
                  value={newPassword}
                  onChange={(e) => setNewPassword(e.target.value)}
                  required
                />
              </div>

              <div className="mb-4">
                <label className="form-label fw-bold small text-secondary">Confirm New Password</label>
                <input
                  type="password"
                  className="form-control"
                  placeholder="Re-enter new password"
                  value={confirmPassword}
                  onChange={(e) => setConfirmPassword(e.target.value)}
                  required
                />
              </div>

              <button type="submit" className="btn btn-success w-100 py-2 fw-semibold" disabled={loading}>
                {loading ? 'Updating Password...' : 'Save New Password'}
              </button>
            </form>
          )}

          <div className="text-center mt-4 pt-3 border-top">
            <Link to="/login" className="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
              <ArrowLeft size={14} /> Back to Portal Login
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};
