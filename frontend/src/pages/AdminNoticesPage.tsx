import React, { useEffect, useState } from 'react';
import { publicApi, adminApi } from '../api/client';
import { NoticeItem } from '../types';
import { CheckCircle2, AlertCircle, Save } from 'lucide-react';

export const AdminNoticesPage: React.FC = () => {
  const [notices, setNotices] = useState<NoticeItem[]>([]);
  const [activeNoticeId, setActiveNoticeId] = useState<number | null>(null);
  const [title, setTitle] = useState('');
  const [content, setContent] = useState('');

  const [message, setMessage] = useState('');
  const [error, setError] = useState('');
  const [saving, setSaving] = useState(false);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    publicApi.getAllNotices().then((res) => {
      if (res.success && res.data && res.data.length > 0) {
        setNotices(res.data);
        const loginNotice = res.data.find((n) => n.isLoginNotice) || res.data[0];
        setActiveNoticeId(loginNotice.id);
        setTitle(loginNotice.title);
        setContent(loginNotice.content);
      }
      setLoading(false);
    });
  }, []);

  const handleSelectNotice = (id: number) => {
    const selected = notices.find((n) => n.id === id);
    if (selected) {
      setActiveNoticeId(selected.id);
      setTitle(selected.title);
      setContent(selected.content);
      setMessage('');
      setError('');
    }
  };

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!activeNoticeId) return;

    setSaving(true);
    setMessage('');
    setError('');

    try {
      const res = await adminApi.updateNotice(activeNoticeId, {
        title,
        content,
        isLoginNotice: true,
      });

      if (res.success) {
        setMessage('Notice Board announcement updated successfully! Changes are live on the login page.');
        // Refresh local list
        setNotices((prev) =>
          prev.map((n) => (n.id === activeNoticeId ? { ...n, title, content } : n))
        );
      } else {
        setError(res.message || 'Failed to update notice');
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to update notice');
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return <div className="p-5 text-center"><div className="spinner-border text-warning" /></div>;
  }

  return (
    <div style={{ maxWidth: 800 }}>
      <div className="mb-4">
        <h4 className="fw-bold text-dark mb-1">Notice Board Editor</h4>
        <p className="text-muted small mb-0">
          Edit and publish announcements shown on the public Contineo parent login screen
        </p>
      </div>

      {message && (
        <div className="alert alert-success d-flex align-items-center gap-2 small py-2 mb-3">
          <CheckCircle2 size={16} />
          <span>{message}</span>
        </div>
      )}

      {error && (
        <div className="alert alert-danger d-flex align-items-center gap-2 small py-2 mb-3">
          <AlertCircle size={16} />
          <span>{error}</span>
        </div>
      )}

      {/* Select Notice */}
      <div className="card shadow-sm border-0 mb-4">
        <div className="card-body p-4">
          <label className="form-label fw-bold small text-secondary">Select Notice to Edit</label>
          <div className="d-flex flex-wrap gap-2 mb-4">
            {notices.map((n) => (
              <button
                key={n.id}
                type="button"
                className={`btn btn-sm ${activeNoticeId === n.id ? 'btn-primary' : 'btn-light border'} px-3`}
                onClick={() => handleSelectNotice(n.id)}
              >
                {n.title} {n.isLoginNotice && '(Active on Login)'}
              </button>
            ))}
          </div>

          <form onSubmit={handleSave}>
            <div className="mb-3">
              <label className="form-label fw-bold small text-secondary">Announcement Title</label>
              <input
                type="text"
                className="form-control"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                required
              />
            </div>

            <div className="mb-4">
              <label className="form-label fw-bold small text-secondary">Notice Board Content</label>
              <textarea
                className="form-control"
                rows={5}
                value={content}
                onChange={(e) => setContent(e.target.value)}
                required
              />
              <div className="form-text small text-muted">
                This message appears inside the yellow highlighted notice box on the parent portal entrance.
              </div>
            </div>

            <button type="submit" className="btn btn-primary px-4 fw-semibold d-inline-flex align-items-center gap-2" disabled={saving}>
              <Save size={16} />
              {saving ? 'Publishing...' : 'Save & Publish Notice'}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
};
