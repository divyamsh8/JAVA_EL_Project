import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { AttendanceData } from '../types';
import {
  AlertTriangle,
  CheckCircle,
  BarChart3,
} from 'lucide-react';
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  ReferenceLine,
  Cell,
} from 'recharts';

export const AttendancePage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [data, setData] = useState<AttendanceData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    if (activeStudentId) {
      setLoading(true);
      parentApi.getAttendance(activeStudentId).then((res) => {
        if (res.success && res.data) {
          setData(res.data);
        }
        setLoading(false);
      });
    }
  }, [activeStudentId]);

  if (loading) {
    return <div className="p-5 text-center"><div className="spinner-border text-primary" /></div>;
  }

  if (!data) {
    return <div className="p-4 text-center text-muted">Attendance records not found.</div>;
  }

  // Prepare chart data
  const chartData = data.subjects.map((s) => ({
    name: s.subjectCode,
    title: s.subjectTitle,
    percentage: Number(s.percentage),
    isShortage: s.isShortage,
  }));

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 className="fw-bold text-navy mb-1">Subject-wise Class Attendance</h4>
          <p className="text-muted small mb-0">Minimum 75.0% aggregate attendance required for semester exam eligibility</p>
        </div>
      </div>

      {/* Shortage Alert Banner */}
      {data.shortageAlert && (
        <div className="alert alert-danger d-flex align-items-center gap-3 mb-4 shadow-sm border-danger">
          <AlertTriangle size={28} className="text-danger flex-shrink-0" />
          <div>
            <h6 className="fw-bold mb-1">Attendance Shortage Warning Alert (&lt; 75%)</h6>
            <p className="mb-0 small">
              Your ward has attendance below the mandatory institutional threshold of 75.0% in one or more subjects.
              Please ensure immediate regular attendance to prevent semester exam condonation or debarment.
            </p>
          </div>
        </div>
      )}

      {/* Summary Cards */}
      <div className="row g-3 mb-4">
        <div className="col-12 col-md-4">
          <div className="stat-card">
            <div className="text-muted small fw-semibold">AGGREGATE ATTENDANCE</div>
            <h2 className="fw-bold mb-1 text-navy">{Number(data.overallPercentage).toFixed(1)}%</h2>
            <div className="small">
              {data.shortageAlert ? (
                <span className="text-danger fw-semibold">Shortage Alert Triggered</span>
              ) : (
                <span className="text-success fw-semibold">Satisfactory Attendance Status</span>
              )}
            </div>
          </div>
        </div>

        <div className="col-12 col-md-4">
          <div className="stat-card">
            <div className="text-muted small fw-semibold">TOTAL SESSIONS CONDUCTED</div>
            <h2 className="fw-bold mb-1">{data.overallTotalClasses}</h2>
            <div className="text-muted small">Total subject hours across term</div>
          </div>
        </div>

        <div className="col-12 col-md-4">
          <div className="stat-card">
            <div className="text-muted small fw-semibold">TOTAL SESSIONS ATTENDED</div>
            <h2 className="fw-bold mb-1 text-success">{data.overallAttendedClasses}</h2>
            <div className="text-muted small">Verified present session hours</div>
          </div>
        </div>
      </div>

      {/* Recharts Bar Chart */}
      <div className="card shadow-sm border-0 mb-4">
        <div className="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
          <h6 className="fw-bold mb-0 d-flex align-items-center gap-2">
            <BarChart3 size={18} className="text-primary" /> Attendance Percentage by Subject
          </h6>
          <span className="badge bg-light text-muted border">Threshold: 75%</span>
        </div>
        <div className="card-body p-3">
          <div style={{ width: '100%', height: 320 }}>
            <ResponsiveContainer>
              <BarChart data={chartData} margin={{ top: 20, right: 30, left: 0, bottom: 20 }}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} />
                <XAxis dataKey="name" />
                <YAxis domain={[0, 100]} unit="%" />
                <Tooltip
                  formatter={(val: any, _name: any, item: any) => [
                    `${val}% (${item.payload.title})`,
                    'Attendance',
                  ]}
                />
                <ReferenceLine y={75} stroke="#dc2626" strokeDasharray="4 4" label={{ value: '75% Threshold', fill: '#dc2626', position: 'top' }} />
                <Bar dataKey="percentage" radius={[6, 6, 0, 0]}>
                  {chartData.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={entry.percentage < 75 ? '#ef4444' : '#0284c7'} />
                  ))}
                </Bar>
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>
      </div>

      {/* Detailed Table */}
      <div className="card shadow-sm border-0">
        <div className="card-header bg-white py-3 border-0">
          <h6 className="fw-bold mb-0">Subject-wise Session Breakdown</h6>
        </div>
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light small">
                <tr>
                  <th>Subject Code</th>
                  <th>Title</th>
                  <th className="text-center">Credits</th>
                  <th className="text-center">Conducted</th>
                  <th className="text-center">Attended</th>
                  <th className="text-center">Percentage</th>
                  <th className="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                {data.subjects.map((sub) => (
                  <tr key={sub.subjectId}>
                    <td className="fw-bold text-navy">{sub.subjectCode}</td>
                    <td>{sub.subjectTitle}</td>
                    <td className="text-center"><span className="badge bg-light text-dark border">{sub.credits}</span></td>
                    <td className="text-center">{sub.totalClasses}</td>
                    <td className="text-center fw-semibold text-dark">{sub.attendedClasses}</td>
                    <td className="text-center">
                      <span className={`fw-bold ${sub.isShortage ? 'text-danger' : 'text-success'}`}>
                        {Number(sub.percentage).toFixed(1)}%
                      </span>
                    </td>
                    <td className="text-center">
                      {sub.isShortage ? (
                        <span className="badge-shortage d-inline-flex align-items-center gap-1">
                          <AlertTriangle size={12} /> Shortage (&lt;75%)
                        </span>
                      ) : (
                        <span className="badge-ok d-inline-flex align-items-center gap-1">
                          <CheckCircle size={12} /> Eligible
                        </span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};
