import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { LeaveRequestItem } from '../types';
import { Send, Clock, CheckCircle2, XCircle, AlertCircle } from 'lucide-react';

export const LeaveRequestPage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [requests, setRequests] = useState<LeaveRequestItem[]>([]);
  const [loading, setLoading] = useState(true);

  // Form fields
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');
  const [reason, setReason] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [successMsg, setSuccessMsg] = useState('');

  const loadRequests = () => {
    if (activeStudentId) {
      setLoading(true);
      parentApi.getLeaveRequests(activeStudentId).then((res) => {
        if (res.success && res.data) {
          setRequests(res.data);
        }
        setLoading(false);
      });
    }
  };

  useEffect(() => {
    loadRequests();
  }, [activeStudentId]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!startDate || !endDate || !reason) return;

    setSubmitting(true);
    try {
      const res = await parentApi.submitLeaveRequest(
        { startDate, endDate, reason },
        activeStudentId || undefined
      );
      if (res.success) {
        setSuccessMsg('Absence intimation submitted successfully to HOD & Proctor.');
        setStartDate('');
        setEndDate('');
        setReason('');
        loadRequests();
        setTimeout(() => setSuccessMsg(''), 4000);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setSubmitting(false);
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'APPROVED':
        return <span className="badge bg-success d-inline-flex align-items-center gap-1"><CheckCircle2 size={12} /> Approved</span>;
      case 'REJECTED':
        return <span className="badge bg-danger d-inline-flex align-items-center gap-1"><XCircle size={12} /> Rejected</span>;
      case 'PENDING':
      default:
        return <span className="badge bg-warning text-dark d-inline-flex align-items-center gap-1"><Clock size={12} /> Pending HOD Review</span>;
    }
  };

  return (
    <div className="container-fluid py-4">
      <div className="mb-4">
        <h4 className="fw-bold text-navy mb-1 d-flex align-items-center gap-2">
          <Send className="text-primary" size={24} />
          Student Absence & Leave Intimation
        </h4>
        <p className="text-muted small mb-0">
          Intimate department proctor and HOD regarding planned student leave or medical absence
        </p>
      </div>

      <div className="row g-4">
        {/* Intimation Form */}
        <div className="col-12 col-lg-5">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-white py-3 border-bottom">
              <h6 className="fw-bold mb-0 text-navy">Submit New Absence Intimation</h6>
            </div>
            <div className="card-body p-4">
              {successMsg && (
                <div className="alert alert-success d-flex align-items-center gap-2 py-2 small" role="alert">
                  <CheckCircle2 size={16} />
                  <span>{successMsg}</span>
                </div>
              )}

              <form onSubmit={handleSubmit}>
                <div className="row g-2 mb-3">
                  <div className="col-6">
                    <label className="form-label small fw-semibold">Start Date</label>
                    <input
                      type="date"
                      className="form-control"
                      value={startDate}
                      onChange={(e) => setStartDate(e.target.value)}
                      required
                    />
                  </div>
                  <div className="col-6">
                    <label className="form-label small fw-semibold">End Date</label>
                    <input
                      type="date"
                      className="form-control"
                      value={endDate}
                      onChange={(e) => setEndDate(e.target.value)}
                      required
                    />
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Reason for Absence</label>
                  <textarea
                    className="form-control"
                    rows={3}
                    placeholder="Provide specific medical or family reason..."
                    value={reason}
                    onChange={(e) => setReason(e.target.value)}
                    required
                  />
                  <div className="form-text small text-muted">
                    Medical leaves require physical certificate submission to department upon return.
                  </div>
                </div>

                <button
                  type="submit"
                  className="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2"
                  disabled={submitting}
                >
                  <Send size={16} />
                  <span>{submitting ? 'Submitting...' : 'Send Intimation'}</span>
                </button>
              </form>
            </div>
          </div>
        </div>

        {/* Previous Requests Table */}
        <div className="col-12 col-lg-7">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-white py-3 border-bottom">
              <h6 className="fw-bold mb-0 text-navy">Absence History & Approvals</h6>
            </div>
            <div className="card-body p-0">
              {loading ? (
                <div className="text-center py-5">
                  <div className="spinner-border text-primary" role="status" />
                </div>
              ) : requests.length === 0 ? (
                <div className="text-center py-5 text-muted small">
                  <AlertCircle size={28} className="text-secondary mb-2" />
                  <div>No absence intimations submitted yet.</div>
                </div>
              ) : (
                <div className="table-responsive">
                  <table className="table table-hover align-middle mb-0">
                    <thead className="table-light">
                      <tr style={{ fontSize: '0.82rem' }}>
                        <th>Dates</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                      </tr>
                    </thead>
                    <tbody style={{ fontSize: '0.85rem' }}>
                      {requests.map((r) => (
                        <tr key={r.id}>
                          <td className="fw-semibold text-nowrap">
                            {r.startDate} to {r.endDate}
                          </td>
                          <td className="text-muted">{r.reason}</td>
                          <td>{getStatusBadge(r.status)}</td>
                          <td className="text-muted small">
                            {r.submittedAt ? r.submittedAt.slice(0, 10) : 'Recent'}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
