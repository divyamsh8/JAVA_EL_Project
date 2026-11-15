import React, { useEffect, useState } from 'react';
import { adminApi } from '../api/client';
import { LoginLogItem } from '../types';
import { CheckCircle2, XCircle, Lock, RefreshCw } from 'lucide-react';

export const AdminLogsPage: React.FC = () => {
  const [logs, setLogs] = useState<LoginLogItem[]>([]);
  const [loading, setLoading] = useState(true);

  const loadLogs = async () => {
    setLoading(true);
    try {
      const res = await adminApi.getStats();
      if (res.success && res.data) {
        setLogs(res.data.recentLogs || []);
      }
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadLogs();
  }, []);

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 className="fw-bold text-dark mb-1">Security Audit & Login Logs</h4>
          <p className="text-muted small mb-0">Live authentication activity, IP addresses, and rate-limiting triggers</p>
        </div>
        <button
          onClick={loadLogs}
          className="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"
          disabled={loading}
        >
          <RefreshCw size={14} className={loading ? 'spin' : ''} />
          <span>Refresh</span>
        </button>
      </div>

      <div className="card shadow-sm border-0">
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light small">
                <tr>
                  <th>Timestamp</th>
                  <th>Username / USN</th>
                  <th>IP Address</th>
                  <th>Auth Status</th>
                  <th>Failure Reason</th>
                </tr>
              </thead>
              <tbody>
                {logs.map((log) => (
                  <tr key={log.id}>
                    <td className="small text-muted">{log.createdAt}</td>
                    <td className="fw-bold text-navy">{log.username}</td>
                    <td><code className="text-secondary">{log.ipAddress}</code></td>
                    <td>
                      {log.status === 'SUCCESS' ? (
                        <span className="badge bg-success-subtle text-success border border-success d-inline-flex align-items-center gap-1">
                          <CheckCircle2 size={12} /> Success
                        </span>
                      ) : log.status === 'LOCKED' ? (
                        <span className="badge bg-danger text-white d-inline-flex align-items-center gap-1">
                          <Lock size={12} /> Locked (5 fails)
                        </span>
                      ) : (
                        <span className="badge bg-danger-subtle text-danger border border-danger d-inline-flex align-items-center gap-1">
                          <XCircle size={12} /> Failed
                        </span>
                      )}
                    </td>
                    <td className="small text-muted">{log.failureReason || '—'}</td>
                  </tr>
                ))}
                {logs.length === 0 && (
                  <tr>
                    <td colSpan={5} className="text-center p-5 text-muted">
                      No security audit logs recorded yet.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};
