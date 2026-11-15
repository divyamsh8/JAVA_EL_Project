import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { TimetableData } from '../types';
import { Clock, MapPin, User as UserIcon } from 'lucide-react';

export const TimetablePage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [data, setData] = useState<TimetableData | null>(null);
  const [activeDay, setActiveDay] = useState<string>('Monday');
  const [loading, setLoading] = useState<boolean>(true);

  const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

  useEffect(() => {
    // Select current day if weekday
    const today = new Date().toLocaleDateString('en-US', { weekday: 'long' });
    if (days.includes(today)) {
      setActiveDay(today);
    }

    if (activeStudentId) {
      setLoading(true);
      parentApi.getTimetable(activeStudentId).then((res) => {
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
    return <div className="p-4 text-center text-muted">Timetable not found.</div>;
  }

  const currentSlots = data.schedule?.[activeDay] || [];

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 className="fw-bold text-navy mb-1">Weekly Academic Timetable</h4>
          <p className="text-muted small mb-0">
            {data.departmentName} — Semester {data.semester || 4} Section A
          </p>
        </div>
      </div>

      {/* Day Selector Tabs */}
      <div className="d-flex flex-wrap gap-2 mb-4 border-bottom pb-3">
        {days.map((day) => (
          <button
            key={day}
            className={`btn btn-sm ${activeDay === day ? 'btn-primary fw-bold' : 'btn-light border'} px-3 py-2`}
            onClick={() => setActiveDay(day)}
          >
            {day}
          </button>
        ))}
      </div>

      {/* Period Grid */}
      <div className="card shadow-sm border-0">
        <div className="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
          <h6 className="fw-bold mb-0 d-flex align-items-center gap-2">
            <Clock size={18} className="text-primary" /> Class Schedule for {activeDay}
          </h6>
          <span className="badge bg-light text-muted border">{currentSlots.length} Periods Scheduled</span>
        </div>
        <div className="card-body p-0">
          {currentSlots.length > 0 ? (
            <div className="table-responsive">
              <table className="table table-hover align-middle mb-0">
                <thead className="table-light small">
                  <tr>
                    <th>Period</th>
                    <th>Time Slot</th>
                    <th>Subject Code & Title</th>
                    <th>Classroom</th>
                    <th>Faculty In-Charge</th>
                  </tr>
                </thead>
                <tbody>
                  {currentSlots.map((slot, index) => (
                    <tr key={index}>
                      <td className="fw-bold text-navy">#{slot.periodNumber}</td>
                      <td>
                        <span className="badge bg-light text-secondary border">
                          {slot.startTime} - {slot.endTime}
                        </span>
                      </td>
                      <td>
                        <div className="fw-bold text-dark">{slot.subjectCode}</div>
                        <div className="text-muted small">{slot.subjectTitle}</div>
                      </td>
                      <td>
                        <span className="badge bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1">
                          <MapPin size={12} /> {slot.roomNo}
                        </span>
                      </td>
                      <td className="small text-secondary d-flex align-items-center gap-1">
                        <UserIcon size={14} className="text-muted" />
                        {slot.facultyName}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="text-center p-5 text-muted">
              No academic sessions scheduled on {activeDay}.
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
