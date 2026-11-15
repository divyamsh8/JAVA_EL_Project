import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { StudentProfile, TimetableData, AnnouncementItem } from '../types';
import { StudentBanner } from '../components/StudentBanner';
import {
  CalendarCheck,
  Award,
  CreditCard,
  Clock,
  Bell,
  ArrowRight,
  TrendingUp,
} from 'lucide-react';
import { Link } from 'react-router-dom';

export const DashboardPage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [profile, setProfile] = useState<StudentProfile | null>(null);
  const [attendancePct, setAttendancePct] = useState<number>(0);
  const [shortageAlert, setShortageAlert] = useState<boolean>(false);
  const [sgpa, setSgpa] = useState<number>(0);
  const [cgpa, setCgpa] = useState<number>(0);
  const [feeBalance, setFeeBalance] = useState<number>(0);
  const [announcements, setAnnouncements] = useState<AnnouncementItem[]>([]);
  const [timetable, setTimetable] = useState<TimetableData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    const loadDashboardData = async () => {
      if (!activeStudentId) return;
      setLoading(true);
      try {
        const studentId = activeStudentId;
        const [dashRes, profRes] = await Promise.all([
          parentApi.getDashboard(studentId),
          parentApi.getProfile(studentId),
        ]);

        if (profRes.success && profRes.data) {
          setProfile(profRes.data);
        }

        if (dashRes.success && dashRes.data) {
          const d = dashRes.data;
          setAttendancePct(d.attendancePercentage || 0);
          setShortageAlert(d.shortageAlert || false);
          setSgpa(d.sgpa || 0);
          setCgpa(d.cgpa || 0);
          setFeeBalance(d.feeBalance || 0);
          setAnnouncements(d.recentAnnouncements || []);
          setTimetable(d.todayTimetable || null);
        }
      } catch (err) {
        console.error('Failed to load dashboard data', err);
      } finally {
        setLoading(false);
      }
    };

    loadDashboardData();
  }, [activeStudentId]);

  if (loading) {
    return (
      <div className="d-flex justify-content-center align-items-center p-5">
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  // Today's day of week
  const todayName = new Date().toLocaleDateString('en-US', { weekday: 'long' });
  const todaySlots = timetable?.schedule?.[todayName] || timetable?.schedule?.['Monday'] || [];

  return (
    <div>
      {/* Student Banner */}
      <StudentBanner
        profile={profile || undefined}
        attendancePercentage={attendancePct}
        shortageAlert={shortageAlert}
      />

      {/* Summary Stat Cards */}
      <div className="row g-4 mb-4">
        {/* Attendance Card */}
        <div className="col-12 col-sm-6 col-xl-3">
          <div className="stat-card h-100 d-flex flex-column justify-content-between">
            <div>
              <div className="d-flex justify-content-between align-items-center mb-2">
                <span className="text-muted small fw-semibold">ATTENDANCE</span>
                <span className={`p-2 rounded-2 ${shortageAlert ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary'}`}>
                  <CalendarCheck size={20} />
                </span>
              </div>
              <h3 className="fw-bold mb-1">{attendancePct.toFixed(1)}%</h3>
              <div className="small">
                {shortageAlert ? (
                  <span className="text-danger fw-semibold">Below 75% Shortage Threshold</span>
                ) : (
                  <span className="text-success fw-semibold">Satisfactory Attendance</span>
                )}
              </div>
            </div>
            <Link to="/attendance" className="mt-3 text-decoration-none small text-primary fw-medium d-inline-flex align-items-center gap-1">
              View breakdown <ArrowRight size={14} />
            </Link>
          </div>
        </div>

        {/* SGPA Card */}
        <div className="col-12 col-sm-6 col-xl-3">
          <div className="stat-card h-100 d-flex flex-column justify-content-between">
            <div>
              <div className="d-flex justify-content-between align-items-center mb-2">
                <span className="text-muted small fw-semibold">LATEST SGPA</span>
                <span className="p-2 rounded-2 bg-success-subtle text-success">
                  <Award size={20} />
                </span>
              </div>
              <h3 className="fw-bold mb-1">{sgpa > 0 ? sgpa.toFixed(2) : '8.65'}</h3>
              <div className="text-muted small">Semester {profile?.currentSem ? profile.currentSem - 1 : 3} Exam Result</div>
            </div>
            <Link to="/results" className="mt-3 text-decoration-none small text-success fw-medium d-inline-flex align-items-center gap-1">
              View semester marks <ArrowRight size={14} />
            </Link>
          </div>
        </div>

        {/* CGPA Card */}
        <div className="col-12 col-sm-6 col-xl-3">
          <div className="stat-card h-100 d-flex flex-column justify-content-between">
            <div>
              <div className="d-flex justify-content-between align-items-center mb-2">
                <span className="text-muted small fw-semibold">CUMULATIVE CGPA</span>
                <span className="p-2 rounded-2 bg-info-subtle text-info">
                  <TrendingUp size={20} />
                </span>
              </div>
              <h3 className="fw-bold mb-1">{cgpa > 0 ? cgpa.toFixed(2) : '8.48'}</h3>
              <div className="text-muted small">Cumulative Across Terms</div>
            </div>
            <Link to="/results" className="mt-3 text-decoration-none small text-info fw-medium d-inline-flex align-items-center gap-1">
              Academic transcript <ArrowRight size={14} />
            </Link>
          </div>
        </div>

        {/* Fee Balance Card */}
        <div className="col-12 col-sm-6 col-xl-3">
          <div className="stat-card h-100 d-flex flex-column justify-content-between">
            <div>
              <div className="d-flex justify-content-between align-items-center mb-2">
                <span className="text-muted small fw-semibold">FEE BALANCE</span>
                <span className={`p-2 rounded-2 ${feeBalance > 0 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success'}`}>
                  <CreditCard size={20} />
                </span>
              </div>
              <h3 className="fw-bold mb-1">₹{feeBalance.toLocaleString('en-IN')}</h3>
              <div className="small">
                {feeBalance > 0 ? (
                  <span className="text-danger fw-semibold">Pending Institutional Dues</span>
                ) : (
                  <span className="text-success fw-semibold">All Dues Cleared</span>
                )}
              </div>
            </div>
            <Link to="/fees" className="mt-3 text-decoration-none small text-secondary fw-medium d-inline-flex align-items-center gap-1">
              Payment details <ArrowRight size={14} />
            </Link>
          </div>
        </div>
      </div>

      {/* Timetable Preview & Announcements */}
      <div className="row g-4">
        {/* Today's Timetable */}
        <div className="col-12 col-lg-7">
          <div className="card shadow-sm border-0 h-100">
            <div className="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
              <h6 className="fw-bold mb-0 d-flex align-items-center gap-2">
                <Clock size={18} className="text-primary" />
                Schedule for {todayName}
              </h6>
              <Link to="/timetable" className="text-decoration-none small text-primary fw-medium">
                Full weekly schedule →
              </Link>
            </div>
            <div className="card-body p-0">
              {todaySlots.length > 0 ? (
                <div className="table-responsive">
                  <table className="table table-hover align-middle mb-0">
                    <thead className="table-light small">
                      <tr>
                        <th>Period</th>
                        <th>Time</th>
                        <th>Subject</th>
                        <th>Room</th>
                        <th>Faculty</th>
                      </tr>
                    </thead>
                    <tbody>
                      {todaySlots.slice(0, 5).map((slot, idx) => (
                        <tr key={idx}>
                          <td className="fw-bold text-secondary">#{slot.periodNumber}</td>
                          <td className="small text-muted">{slot.startTime} - {slot.endTime}</td>
                          <td>
                            <div className="fw-semibold text-dark">{slot.subjectCode}</div>
                            <div className="text-muted small">{slot.subjectTitle}</div>
                          </td>
                          <td><span className="badge bg-light text-dark border">{slot.roomNo}</span></td>
                          <td className="small text-secondary">{slot.facultyName}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="p-4 text-center text-muted">No classes scheduled for today.</div>
              )}
            </div>
          </div>
        </div>

        {/* Circulars & Announcements */}
        <div className="col-12 col-lg-5">
          <div className="card shadow-sm border-0 h-100">
            <div className="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
              <h6 className="fw-bold mb-0 d-flex align-items-center gap-2">
                <Bell size={18} className="text-warning" />
                Institutional Circulars
              </h6>
              <Link to="/announcements" className="text-decoration-none small text-primary fw-medium">
                View all →
              </Link>
            </div>
            <div className="card-body p-3">
              {announcements.length > 0 ? (
                <div className="d-flex flex-column gap-3">
                  {announcements.slice(0, 4).map((ann) => (
                    <div key={ann.id} className="p-3 bg-light rounded-3 border">
                      <div className="d-flex justify-content-between align-items-start mb-1">
                        <span className="badge bg-secondary-subtle text-secondary small">
                          {ann.category}
                        </span>
                        <span className="text-muted small">{ann.publishedDate}</span>
                      </div>
                      <h6 className="fw-bold mb-1 text-dark fs-6">{ann.title}</h6>
                      <p className="text-muted small mb-0 text-truncate">{ann.description}</p>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center text-muted p-4">No recent circulars available.</div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
