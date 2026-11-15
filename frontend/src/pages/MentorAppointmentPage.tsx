import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { MentorAppointmentItem } from '../types';
import { UserCheck, Calendar, Video, MapPin, CheckCircle2, Clock } from 'lucide-react';

export const MentorAppointmentPage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [appointments, setAppointments] = useState<MentorAppointmentItem[]>([]);
  const [loading, setLoading] = useState(true);

  // Booking Form
  const [facultyName, setFacultyName] = useState('Dr. Ramesh Babu (Associate Professor & CSE Proctor)');
  const [appointmentTime, setAppointmentTime] = useState('');
  const [mode, setMode] = useState('ONLINE');
  const [topic, setTopic] = useState('Mid-Term Academic Progress & Attendance Review');
  const [notes, setNotes] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [successMsg, setSuccessMsg] = useState('');

  const loadAppointments = () => {
    if (activeStudentId) {
      setLoading(true);
      parentApi.getMentorAppointments(activeStudentId).then((res) => {
        if (res.success && res.data) {
          setAppointments(res.data);
        }
        setLoading(false);
      });
    }
  };

  useEffect(() => {
    loadAppointments();
  }, [activeStudentId]);

  const handleBook = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!appointmentTime) return;

    setSubmitting(true);
    try {
      const res = await parentApi.bookMentorAppointment(
        {
          facultyName,
          appointmentTime,
          mode,
          topic,
          notes,
        },
        activeStudentId || undefined
      );

      if (res.success) {
        setSuccessMsg('Mentor appointment scheduled and invitation sent to faculty.');
        setNotes('');
        loadAppointments();
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
          <UserCheck className="text-primary" size={24} />
          Parent–Faculty Mentor Consultation
        </h4>
        <p className="text-muted small mb-0">
          Book 1-on-1 consultations with your ward's assigned faculty proctor and academic mentor
        </p>
      </div>

      <div className="row g-4">
        {/* Booking Form */}
        <div className="col-12 col-lg-5">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-white py-3 border-bottom">
              <h6 className="fw-bold mb-0 text-navy">Schedule Consultation Meeting</h6>
            </div>
            <div className="card-body p-4">
              {successMsg && (
                <div className="alert alert-success d-flex align-items-center gap-2 py-2 small" role="alert">
                  <CheckCircle2 size={16} />
                  <span>{successMsg}</span>
                </div>
              )}

              <form onSubmit={handleBook}>
                <div className="mb-3">
                  <label className="form-label small fw-semibold">Faculty Mentor / Proctor</label>
                  <select
                    className="form-select"
                    value={facultyName}
                    onChange={(e) => setFacultyName(e.target.value)}
                  >
                    <option value="Dr. Ramesh Babu (Associate Professor & CSE Proctor)">
                      Dr. Ramesh Babu (Associate Professor & CSE Proctor)
                    </option>
                    <option value="Prof. Priya Kulkarni (Assistant Professor & ECE Proctor)">
                      Prof. Priya Kulkarni (Assistant Professor & ECE Proctor)
                    </option>
                    <option value="Dr. M. S. Suresh (Head of Department)">
                      Dr. M. S. Suresh (Head of Department)
                    </option>
                  </select>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Consultation Mode</label>
                  <div className="d-flex gap-3">
                    <div className="form-check">
                      <input
                        className="form-check-input"
                        type="radio"
                        name="mode"
                        id="modeOnline"
                        value="ONLINE"
                        checked={mode === 'ONLINE'}
                        onChange={() => setMode('ONLINE')}
                      />
                      <label className="form-check-label small" htmlFor="modeOnline">
                        <Video size={14} className="me-1 inline-block text-primary" />
                        Virtual (Google Meet)
                      </label>
                    </div>
                    <div className="form-check">
                      <input
                        className="form-check-input"
                        type="radio"
                        name="mode"
                        id="modeInPerson"
                        value="IN_PERSON"
                        checked={mode === 'IN_PERSON'}
                        onChange={() => setMode('IN_PERSON')}
                      />
                      <label className="form-check-label small" htmlFor="modeInPerson">
                        <MapPin size={14} className="me-1 inline-block text-success" />
                        In-Person (Faculty Cabin)
                      </label>
                    </div>
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Date & Time Slot</label>
                  <input
                    type="datetime-local"
                    className="form-control"
                    value={appointmentTime}
                    onChange={(e) => setAppointmentTime(e.target.value)}
                    required
                  />
                  <div className="form-text small text-muted">
                    Faculty consultation hours: Monday – Friday, 3:00 PM – 5:00 PM.
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Discussion Topic</label>
                  <input
                    type="text"
                    className="form-control"
                    value={topic}
                    onChange={(e) => setTopic(e.target.value)}
                    required
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Parent Notes / Questions (Optional)</label>
                  <textarea
                    className="form-control"
                    rows={2}
                    placeholder="Specific questions regarding ward's tests or lab performance..."
                    value={notes}
                    onChange={(e) => setNotes(e.target.value)}
                  />
                </div>

                <button
                  type="submit"
                  className="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2"
                  disabled={submitting}
                >
                  <Calendar size={16} />
                  <span>{submitting ? 'Confirming...' : 'Book Consultation'}</span>
                </button>
              </form>
            </div>
          </div>
        </div>

        {/* Scheduled Appointments */}
        <div className="col-12 col-lg-7">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-white py-3 border-bottom">
              <h6 className="fw-bold mb-0 text-navy">Scheduled & Upcoming Consultations</h6>
            </div>
            <div className="card-body p-3">
              {loading ? (
                <div className="text-center py-5">
                  <div className="spinner-border text-primary" role="status" />
                </div>
              ) : appointments.length === 0 ? (
                <div className="text-center py-5 text-muted small">
                  <Clock size={28} className="text-secondary mb-2" />
                  <div>No scheduled appointments found.</div>
                </div>
              ) : (
                <div className="d-flex flex-column gap-3">
                  {appointments.map((a) => (
                    <div key={a.id} className="card border p-3 bg-light">
                      <div className="d-flex justify-content-between align-items-start mb-2">
                        <div>
                          <span className="fw-bold text-dark">{a.facultyName}</span>
                          <div className="text-primary small fw-semibold">{a.topic}</div>
                        </div>
                        <span className="badge bg-success d-inline-flex align-items-center gap-1">
                          <CheckCircle2 size={12} /> {a.status}
                        </span>
                      </div>

                      <div className="d-flex align-items-center gap-3 text-muted small mb-2">
                        <span>
                          <Calendar size={13} className="me-1 inline-block" />
                          {a.appointmentTime ? a.appointmentTime.replace('T', ' ') : 'Upcoming'}
                        </span>
                        <span>
                          {a.mode === 'ONLINE' ? (
                            <><Video size={13} className="me-1 inline-block text-primary" /> Google Meet Video</>
                          ) : (
                            <><MapPin size={13} className="me-1 inline-block text-success" /> Cabin 204, CSE Block</>
                          )}
                        </span>
                      </div>

                      {a.notes && (
                        <div className="small text-muted p-2 rounded bg-white border">
                          <strong>Parent Notes:</strong> {a.notes}
                        </div>
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
