import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { FeedbackItem } from '../types';
import { MessageSquare, Send, CheckCircle2, AlertCircle, Clock } from 'lucide-react';

export const FeedbackPage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [tickets, setTickets] = useState<FeedbackItem[]>([]);
  const [loading, setLoading] = useState(true);

  // Form fields
  const [category, setCategory] = useState('ACADEMIC');
  const [subject, setSubject] = useState('');
  const [message, setMessage] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [successMsg, setSuccessMsg] = useState('');

  const loadTickets = () => {
    if (activeStudentId) {
      setLoading(true);
      parentApi.getFeedback(activeStudentId).then((res) => {
        if (res.success && res.data) {
          setTickets(res.data);
        }
        setLoading(false);
      });
    }
  };

  useEffect(() => {
    loadTickets();
  }, [activeStudentId]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!subject || !message) return;

    setSubmitting(true);
    try {
      const res = await parentApi.submitFeedback(
        { category, subject, message },
        activeStudentId || undefined
      );
      if (res.success) {
        setSuccessMsg('Grievance ticket created. Assigned to Dean of Student Affairs.');
        setSubject('');
        setMessage('');
        loadTickets();
        setTimeout(() => setSuccessMsg(''), 4000);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="container-fluid py-4">
      <div className="mb-4">
        <h4 className="fw-bold text-navy mb-1 d-flex align-items-center gap-2">
          <MessageSquare className="text-primary" size={24} />
          Parent Grievance & Feedback Portal
        </h4>
        <p className="text-muted small mb-0">
          Direct escalation channel to Institutional Grievance Redressal Committee & Dean
        </p>
      </div>

      <div className="row g-4">
        {/* Ticket Submission Form */}
        <div className="col-12 col-lg-5">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-white py-3 border-bottom">
              <h6 className="fw-bold mb-0 text-navy">Submit Grievance / Query</h6>
            </div>
            <div className="card-body p-4">
              {successMsg && (
                <div className="alert alert-success d-flex align-items-center gap-2 py-2 small" role="alert">
                  <CheckCircle2 size={16} />
                  <span>{successMsg}</span>
                </div>
              )}

              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label className="form-label small fw-semibold">Category</label>
                  <select
                    className="form-select"
                    value={category}
                    onChange={(e) => setCategory(e.target.value)}
                  >
                    <option value="ACADEMIC">Academic & Evaluation</option>
                    <option value="ATTENDANCE">Attendance Correction</option>
                    <option value="EXAMINATION">Examinations & CIE</option>
                    <option value="FEES">Fee Accounts & Receipts</option>
                    <option value="TRANSPORT">Campus Transport & Bus</option>
                    <option value="HOSTEL">Hostel & Amenities</option>
                  </select>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Subject</label>
                  <input
                    type="text"
                    className="form-control"
                    placeholder="Brief summary of issue"
                    value={subject}
                    onChange={(e) => setSubject(e.target.value)}
                    required
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Detailed Description</label>
                  <textarea
                    className="form-control"
                    rows={4}
                    placeholder="Provide relevant subject codes, dates or references..."
                    value={message}
                    onChange={(e) => setMessage(e.target.value)}
                    required
                  />
                </div>

                <button
                  type="submit"
                  className="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2"
                  disabled={submitting}
                >
                  <Send size={16} />
                  <span>{submitting ? 'Submitting...' : 'Submit Grievance Ticket'}</span>
                </button>
              </form>
            </div>
          </div>
        </div>

        {/* Tickets History */}
        <div className="col-12 col-lg-7">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-white py-3 border-bottom">
              <h6 className="fw-bold mb-0 text-navy">Ticket History & Administrative Responses</h6>
            </div>
            <div className="card-body p-3">
              {loading ? (
                <div className="text-center py-5">
                  <div className="spinner-border text-primary" role="status" />
                </div>
              ) : tickets.length === 0 ? (
                <div className="text-center py-5 text-muted small">
                  <AlertCircle size={28} className="text-secondary mb-2" />
                  <div>No grievance tickets recorded.</div>
                </div>
              ) : (
                <div className="d-flex flex-column gap-3">
                  {tickets.map((t) => (
                    <div key={t.id} className="card border p-3 bg-light">
                      <div className="d-flex justify-content-between align-items-start mb-2">
                        <div>
                          <span className="badge bg-secondary-subtle text-secondary me-2">{t.category}</span>
                          <span className="fw-bold text-dark">{t.subject}</span>
                        </div>
                        <span className={`badge ${t.status === 'RESOLVED' ? 'bg-success' : 'bg-warning text-dark'}`}>
                          {t.status === 'RESOLVED' ? (
                            <span className="d-inline-flex align-items-center gap-1"><CheckCircle2 size={12} /> Resolved</span>
                          ) : (
                            <span className="d-inline-flex align-items-center gap-1"><Clock size={12} /> Under Investigation</span>
                          )}
                        </span>
                      </div>

                      <p className="text-muted small mb-2">{t.message}</p>

                      {t.response ? (
                        <div className="p-2 rounded bg-white border border-success-subtle mt-2">
                          <div className="fw-semibold text-success small mb-1">Administrative Response:</div>
                          <div className="small text-dark">{t.response}</div>
                        </div>
                      ) : (
                        <div className="small text-muted fst-italic">Awaiting Dean / Department review.</div>
                      )}
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
