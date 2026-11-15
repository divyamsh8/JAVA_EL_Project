import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { StudentProfile } from '../types';
import { User, Phone, Mail, MapPin, Briefcase, Calendar, ShieldCheck } from 'lucide-react';

export const ProfilePage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [profile, setProfile] = useState<StudentProfile | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    if (activeStudentId) {
      setLoading(true);
      parentApi.getProfile(activeStudentId).then((res) => {
        if (res.success && res.data) {
          setProfile(res.data);
        }
        setLoading(false);
      });
    }
  }, [activeStudentId]);

  if (loading) {
    return <div className="p-5 text-center"><div className="spinner-border text-primary" /></div>;
  }

  if (!profile) {
    return <div className="p-4 text-center text-muted">Student profile record not found.</div>;
  }

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 className="fw-bold text-navy mb-1">Student & Parent Profile</h4>
          <p className="text-muted small mb-0">Contineo Institutional Records Database</p>
        </div>
        <span className="badge bg-success-subtle text-success border px-3 py-2">
          <ShieldCheck size={14} className="me-1" /> Verified Enrollment
        </span>
      </div>

      <div className="row g-4">
        {/* Student Biodata */}
        <div className="col-12 col-lg-6">
          <div className="card shadow-sm border-0 h-100">
            <div className="card-header bg-white py-3 border-0">
              <h5 className="fw-bold mb-0 d-flex align-items-center gap-2 text-primary">
                <User size={20} /> Student Information
              </h5>
            </div>
            <div className="card-body p-4 pt-0">
              <table className="table table-borderless align-middle mb-0">
                <tbody>
                  <tr>
                    <td className="text-muted fw-semibold" style={{ width: '38%' }}>Full Name:</td>
                    <td className="fw-bold text-dark">{profile.fullName}</td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">University Seat No (USN):</td>
                    <td><span className="badge bg-light text-dark border fs-6">{profile.usn}</span></td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Roll Number:</td>
                    <td className="text-dark">{profile.rollNo || 'N/A'}</td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Department:</td>
                    <td className="text-dark">{profile.departmentName} ({profile.departmentCode})</td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Enrolled Course:</td>
                    <td className="text-dark">{profile.courseName}</td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Current Semester:</td>
                    <td className="fw-bold text-primary">Semester {profile.currentSem}</td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Date of Birth:</td>
                    <td className="text-dark d-flex align-items-center gap-1">
                      <Calendar size={14} /> {profile.dob}
                    </td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Gender & Blood Group:</td>
                    <td className="text-dark">{profile.gender} | <span className="badge bg-danger-subtle text-danger">{profile.bloodGroup}</span></td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Admission Year:</td>
                    <td className="text-dark">{profile.admissionYear}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {/* Parent / Guardian Biodata */}
        <div className="col-12 col-lg-6">
          <div className="card shadow-sm border-0 h-100">
            <div className="card-header bg-white py-3 border-0">
              <h5 className="fw-bold mb-0 d-flex align-items-center gap-2 text-info">
                <Phone size={20} /> Parent & Guardian Records
              </h5>
            </div>
            <div className="card-body p-4 pt-0">
              <table className="table table-borderless align-middle mb-0">
                <tbody>
                  <tr>
                    <td className="text-muted fw-semibold" style={{ width: '38%' }}>Father's Name:</td>
                    <td className="fw-bold text-dark">{profile.fatherName}</td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Mother's Name:</td>
                    <td className="fw-bold text-dark">{profile.motherName}</td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Primary Mobile:</td>
                    <td className="text-dark d-flex align-items-center gap-1">
                      <Phone size={14} className="text-success" />
                      <span className="fw-medium">{profile.primaryMobile}</span>
                    </td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Alternate Contact:</td>
                    <td className="text-muted">{profile.alternateMobile || 'None specified'}</td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Contact Email:</td>
                    <td className="text-dark d-flex align-items-center gap-1">
                      <Mail size={14} className="text-primary" />
                      {profile.parentEmail || 'N/A'}
                    </td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold">Parent Occupation:</td>
                    <td className="text-dark d-flex align-items-center gap-1">
                      <Briefcase size={14} /> {profile.occupation || 'Private Service / Business'}
                    </td>
                  </tr>
                  <tr>
                    <td className="text-muted fw-semibold align-top">Residential Address:</td>
                    <td className="text-dark d-flex align-items-start gap-1">
                      <MapPin size={16} className="text-danger flex-shrink-0 mt-1" />
                      <span>{profile.address}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
