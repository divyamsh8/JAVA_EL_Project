import React from 'react';
import { StudentProfile } from '../types';
import { AlertTriangle, CheckCircle, GraduationCap } from 'lucide-react';

interface StudentBannerProps {
  profile?: StudentProfile;
  attendancePercentage?: number;
  shortageAlert?: boolean;
}

export const StudentBanner: React.FC<StudentBannerProps> = ({
  profile,
  attendancePercentage,
  shortageAlert,
}) => {
  if (!profile) return null;

  return (
    <div className="student-banner-card">
      <div className="d-flex align-items-center gap-4">
        <div className="student-avatar">
          {profile.firstName.charAt(0)}
          {profile.lastName.charAt(0)}
        </div>

        <div>
          <h4 className="fw-bold mb-1 d-flex align-items-center gap-2">
            {profile.fullName}
            <span className="badge bg-light text-dark fs-6 fw-normal px-2 py-1">
              USN: {profile.usn}
            </span>
          </h4>
          <div className="text-white-50 d-flex flex-wrap align-items-center gap-3 fs-6">
            <span className="d-flex align-items-center gap-1">
              <GraduationCap size={16} />
              {profile.courseName} ({profile.departmentCode})
            </span>
            <span>• Semester {profile.currentSem}</span>
            <span>• Roll No: {profile.rollNo || 'N/A'}</span>
          </div>
        </div>
      </div>

      <div className="text-end d-none d-md-block">
        {attendancePercentage !== undefined && (
          <div className="bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-25">
            <div className="small text-white-50 mb-1">Overall Attendance</div>
            <div className="d-flex align-items-center justify-content-end gap-2">
              <span className="fs-3 fw-bold">{attendancePercentage.toFixed(1)}%</span>
              {shortageAlert ? (
                <span className="badge bg-danger d-flex align-items-center gap-1">
                  <AlertTriangle size={14} /> Shortage (&lt;75%)
                </span>
              ) : (
                <span className="badge bg-success d-flex align-items-center gap-1">
                  <CheckCircle size={14} /> Normal
                </span>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
