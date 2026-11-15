import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { ResultsData } from '../types';
import { Award, TrendingUp, CheckCircle2, Download, AlertCircle } from 'lucide-react';

export const ResultsPage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [data, setData] = useState<ResultsData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    if (activeStudentId) {
      setLoading(true);
      parentApi.getResults(activeStudentId).then((res) => {
        if (res.success && res.data) {
          setData(res.data);
        }
        setLoading(false);
      });
    }
  }, [activeStudentId]);

  const handleDownloadReport = () => {
    const url = `/api/v1/parent/reports/report-card.pdf?studentId=${activeStudentId || 1}`;
    const win = window.open(url, '_blank');
    if (!win) {
      window.location.href = url;
    }
  };

  if (loading) {
    return <div className="p-5 text-center"><div className="spinner-border text-primary" /></div>;
  }

  if (!data) {
    return <div className="p-4 text-center text-muted">Semester results not found.</div>;
  }

  const backlogCount = data.subjects.filter((s) => s.resultStatus === 'F' || s.resultStatus === 'FAIL').length;

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
          <h4 className="fw-bold text-navy mb-1">Semester Examination Results</h4>
          <p className="text-muted small mb-0">
            {data.examSession} Examination Cycle | Declared on {data.declaredDate || 'Recent'}
          </p>
        </div>

        <div className="d-flex align-items-center gap-2">
          <button
            onClick={handleDownloadReport}
            className="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm"
          >
            <Download size={16} />
            <span>Download Official Report Card (PDF)</span>
          </button>

          <span className="badge bg-success-subtle text-success border px-3 py-2 fs-6">
            <CheckCircle2 size={16} className="me-1" /> Status: {data.overallStatus}
          </span>
        </div>
      </div>

      {/* SGPA & CGPA Cards */}
      <div className="row g-4 mb-4">
        <div className="col-12 col-md-4">
          <div className="stat-card border-primary-subtle">
            <div className="d-flex justify-content-between align-items-center mb-2">
              <span className="text-muted small fw-semibold">SEMESTER GPA (SGPA)</span>
              <Award size={24} className="text-primary" />
            </div>
            <h2 className="fw-bold mb-1 text-primary">{Number(data.sgpa).toFixed(2)}</h2>
            <div className="text-muted small">Semester {data.semester} Evaluation</div>
          </div>
        </div>

        <div className="col-12 col-md-4">
          <div className="stat-card border-info-subtle">
            <div className="d-flex justify-content-between align-items-center mb-2">
              <span className="text-muted small fw-semibold">CUMULATIVE GPA (CGPA)</span>
              <TrendingUp size={24} className="text-info" />
            </div>
            <h2 className="fw-bold mb-1 text-info">{Number(data.cgpa).toFixed(2)}</h2>
            <div className="text-muted small">Across all completed semesters</div>
          </div>
        </div>

        <div className="col-12 col-md-4">
          <div className="stat-card border-success-subtle">
            <div className="d-flex justify-content-between align-items-center mb-2">
              <span className="text-muted small fw-semibold">ACTIVE BACKLOGS</span>
              <AlertCircle size={24} className={backlogCount > 0 ? 'text-danger' : 'text-success'} />
            </div>
            <h2 className={`fw-bold mb-1 ${backlogCount > 0 ? 'text-danger' : 'text-success'}`}>
              {backlogCount}
            </h2>
            <div className="text-muted small">
              {backlogCount === 0 ? 'All courses cleared on first attempt' : 'Arrears pending summer session'}
            </div>
          </div>
        </div>
      </div>

      {/* Course Performance Table */}
      <div className="card shadow-sm border-0">
        <div className="card-header bg-white py-3 border-0">
          <h6 className="fw-bold mb-0">Individual Course Grades & Performance</h6>
        </div>
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light small">
                <tr>
                  <th>Course Code</th>
                  <th>Course Title</th>
                  <th className="text-center">Credits</th>
                  <th className="text-center">Letter Grade</th>
                  <th className="text-center">Grade Points</th>
                  <th className="text-center">Result</th>
                </tr>
              </thead>
              <tbody>
                {data.subjects.map((s) => (
                  <tr key={s.subjectId}>
                    <td className="fw-bold text-navy">{s.subjectCode}</td>
                    <td>{s.subjectTitle}</td>
                    <td className="text-center">{s.credits}</td>
                    <td className="text-center fw-bold fs-6 text-primary">{s.gradeLetter}</td>
                    <td className="text-center fw-semibold">{s.gradePoints}</td>
                    <td className="text-center">
                      <span className="badge bg-success-subtle text-success border border-success">
                        {s.resultStatus}
                      </span>
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
