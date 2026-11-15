import React, { useEffect, useState } from 'react';
import { adminApi } from '../api/client';
import { AdminStatsData } from '../types';
import { Users, Building2, AlertTriangle, CreditCard, Shield } from 'lucide-react';
import { Link } from 'react-router-dom';

export const AdminDashboardPage: React.FC = () => {
  const [stats, setStats] = useState<AdminStatsData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    adminApi.getStats().then((res) => {
      if (res.success && res.data) {
        setStats(res.data);
      }
      setLoading(false);
    });
  }, []);

  if (loading) {
    return <div className="p-5 text-center"><div className="spinner-border text-warning" /></div>;
  }

  if (!stats) {
    return <div className="p-4 text-center text-muted">Admin statistics could not be loaded.</div>;
  }

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 className="fw-bold text-dark mb-1">Institutional Administration Overview</h4>
          <p className="text-muted small mb-0">Contineo Institutional Control Console</p>
        </div>
        <span className="badge bg-warning text-dark border px-3 py-2 fw-semibold">
          <Shield size={14} className="me-1" /> Super Administrator
        </span>
      </div>

      {/* Metrics Row */}
      <div className="row g-4 mb-4">
        <div className="col-12 col-sm-6 col-xl-3">
          <div className="stat-card">
            <div className="d-flex justify-content-between align-items-center mb-2">
              <span className="text-muted small fw-semibold">TOTAL STUDENTS</span>
              <span className="p-2 rounded-2 bg-primary-subtle text-primary">
                <Users size={20} />
              </span>
            </div>
            <h2 className="fw-bold mb-1 text-primary">{stats.totalStudents}</h2>
            <div className="text-muted small">Active enrolled wards</div>
          </div>
        </div>

        <div className="col-12 col-sm-6 col-xl-3">
          <div className="stat-card">
            <div className="d-flex justify-content-between align-items-center mb-2">
              <span className="text-muted small fw-semibold">DEPARTMENTS</span>
              <span className="p-2 rounded-2 bg-info-subtle text-info">
                <Building2 size={20} />
              </span>
            </div>
            <h2 className="fw-bold mb-1 text-info">{stats.totalDepartments}</h2>
            <div className="text-muted small">CSE, ECE, MECH</div>
          </div>
        </div>

        <div className="col-12 col-sm-6 col-xl-3">
          <div className="stat-card">
            <div className="d-flex justify-content-between align-items-center mb-2">
              <span className="text-muted small fw-semibold">ATTENDANCE SHORTAGES</span>
              <span className="p-2 rounded-2 bg-danger-subtle text-danger">
                <AlertTriangle size={20} />
              </span>
            </div>
            <h2 className="fw-bold mb-1 text-danger">{stats.shortageCount}</h2>
            <div className="text-danger small fw-semibold">Wards &lt; 75.0% attendance</div>
          </div>
        </div>

        <div className="col-12 col-sm-6 col-xl-3">
          <div className="stat-card">
            <div className="d-flex justify-content-between align-items-center mb-2">
              <span className="text-muted small fw-semibold">TOTAL PENDING FEES</span>
              <span className="p-2 rounded-2 bg-warning-subtle text-warning">
                <CreditCard size={20} />
              </span>
            </div>
            <h2 className="fw-bold mb-1 text-dark">₹{Number(stats.totalFeesPending).toLocaleString('en-IN')}</h2>
            <div className="text-muted small">Uncollected installments</div>
          </div>
        </div>
      </div>

      {/* Shortage Students Warning List */}
      <div className="card shadow-sm border-0 mb-4">
        <div className="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
          <h6 className="fw-bold mb-0 text-danger d-flex align-items-center gap-2">
            <AlertTriangle size={18} /> Active Attendance Shortage Cases (&lt;75%)
          </h6>
          <span className="badge bg-danger-subtle text-danger border border-danger">
            {stats.shortageStudents.length} Students At Risk
          </span>
        </div>
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light small">
                <tr>
                  <th>USN</th>
                  <th>Student Name</th>
                  <th>Department</th>
                  <th>Semester</th>
                  <th>Parent Contact</th>
                  <th className="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                {stats.shortageStudents.map((s) => (
                  <tr key={s.id}>
                    <td className="fw-bold text-navy">{s.usn}</td>
                    <td className="fw-medium">{s.fullName}</td>
                    <td>{s.departmentCode}</td>
                    <td>Sem {s.currentSem}</td>
                    <td>{s.fatherName} ({s.primaryMobile})</td>
                    <td className="text-center">
                      <Link to={`/attendance?studentId=${s.id}`} className="btn btn-outline-primary btn-sm">
                        Inspect
                      </Link>
                    </td>
                  </tr>
                ))}
                {stats.shortageStudents.length === 0 && (
                  <tr>
                    <td colSpan={6} className="text-center p-4 text-muted">
                      No attendance shortages recorded.
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
