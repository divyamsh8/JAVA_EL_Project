import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { CieMarksData } from '../types';
import { FileSpreadsheet, Info, CheckCircle2, XCircle } from 'lucide-react';

export const CieMarksPage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [data, setData] = useState<CieMarksData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    if (activeStudentId) {
      setLoading(true);
      parentApi.getCieMarks(activeStudentId).then((res) => {
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
    return <div className="p-4 text-center text-muted">Internal assessment records not found.</div>;
  }

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 className="fw-bold text-navy mb-1">Continuous Internal Evaluation (CIE) Marks</h4>
          <p className="text-muted small mb-0">Internal assessment marks for Even Term 2026</p>
        </div>
      </div>

      {/* Formula explanation note */}
      <div className="alert alert-info d-flex align-items-center gap-2 mb-4 py-2 small">
        <Info size={16} className="text-info flex-shrink-0" />
        <span>
          <strong>CIE Scaling Formula:</strong> CIE Total (max 50) = (CIE-1 + CIE-2) / 2 + Assignment-1 + Assignment-2. Minimum 20.0 marks required for semester exam eligibility.
        </span>
      </div>

      <div className="card shadow-sm border-0">
        <div className="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
          <h6 className="fw-bold mb-0 d-flex align-items-center gap-2">
            <FileSpreadsheet size={18} className="text-primary" /> Subject Evaluation Grid
          </h6>
          <span className="badge bg-light text-muted border">Max Marks: 50.0</span>
        </div>
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light small">
                <tr>
                  <th>Code</th>
                  <th>Subject Title</th>
                  <th className="text-center">Credits</th>
                  <th className="text-center">CIE-1 (50)</th>
                  <th className="text-center">CIE-2 (50)</th>
                  <th className="text-center">Assign-1 (10)</th>
                  <th className="text-center">Assign-2 (10)</th>
                  <th className="text-center bg-light">Scaled CIE Total (50)</th>
                  <th className="text-center">Eligibility</th>
                </tr>
              </thead>
              <tbody>
                {data.subjects.map((sub) => {
                  const isEligible = Number(sub.cieTotal) >= 20;
                  return (
                    <tr key={sub.subjectId}>
                      <td className="fw-bold text-navy">{sub.subjectCode}</td>
                      <td>{sub.subjectTitle}</td>
                      <td className="text-center"><span className="badge bg-light text-dark border">{sub.credits}</span></td>
                      <td className="text-center">{Number(sub.cie1).toFixed(1)}</td>
                      <td className="text-center">{Number(sub.cie2).toFixed(1)}</td>
                      <td className="text-center">{Number(sub.assignment1).toFixed(1)}</td>
                      <td className="text-center">{Number(sub.assignment2).toFixed(1)}</td>
                      <td className="text-center bg-light fw-bold fs-6 text-primary">
                        {Number(sub.cieTotal).toFixed(1)} / 50.0
                      </td>
                      <td className="text-center">
                        {isEligible ? (
                          <span className="badge bg-success-subtle text-success border border-success d-inline-flex align-items-center gap-1">
                            <CheckCircle2 size={12} /> Eligible
                          </span>
                        ) : (
                          <span className="badge bg-danger-subtle text-danger border border-danger d-inline-flex align-items-center gap-1">
                            <XCircle size={12} /> Ineligible (&lt;20)
                          </span>
                        )}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};
