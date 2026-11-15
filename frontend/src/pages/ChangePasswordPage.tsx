import React, { useState } from 'react';
import { parentApi } from '../api/client';
import { CheckCircle2, AlertCircle } from 'lucide-react';

export const ChangePasswordPage: React.FC = () => {
  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');

  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setSuccess('');

    if (newPassword !== confirmPassword) {
      setError('New passwords do not match');
      return;
    }

    if (newPassword.length < 6) {
      setError('New password must be at least 6 characters long');
      return;
    }

    setLoading(true);
    try {
      const res = await parentApi.changePassword({
        currentPassword,
        newPassword,
        confirmPassword,
      });

      if (res.success) {
        setSuccess('Password updated successfully!');
        setCurrentPassword('');
        setNewPassword('');
        setConfirmPassword('');
      } else {
        setError(res.message || 'Failed to update password');
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Current password / DOB does not match records');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ maxWidth: 540 }}>
      <div className="mb-4">
        <h4 className="fw-bold text-navy mb-1">Change Account Password</h4>
        <p className="text-muted small mb-0">Update your institutional parent portal access credentials</p>
      </div>

      <div className="card shadow-sm border-0">
        <div className="card-body p-4">
          {error && (
            <div className="alert alert-danger d-flex align-items-center gap-2 small py-2 mb-3">
              <AlertCircle size={16} />
              <span>{error}</span>
            </div>
          )}

          {success && (
            <div className="alert alert-success d-flex align-items-center gap-2 small py-2 mb-3">
              <CheckCircle2 size={16} />
              <span>{success}</span>
            </div>
          )}

          <form onSubmit={handleSubmit}>
            <div className="mb-3">
              <label className="form-label fw-bold small text-secondary">
                Current Password / Date of Birth (YYYY-MM-DD)
              </label>
              <input
                type="password"
                className="form-control"
                placeholder="e.g. 2005-04-15"
                value={currentPassword}
                onChange={(e) => setCurrentPassword(e.target.value)}
                required
              />
            </div>

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
                placeholder="Re-type new password"
                value={confirmPassword}
                onChange={(e) => setConfirmPassword(e.target.value)}
                required
              />
            </div>

            <button type="submit" className="btn btn-primary w-100 py-2 fw-semibold" disabled={loading}>
              {loading ? 'Updating...' : 'Update Password'}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
};
