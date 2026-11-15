import React, { useState, useEffect } from 'react';
import { useTheme } from '../theme/ThemeContext';
import { adminApi } from '../api/client';
import { Settings, Save, CheckCircle2, AlertCircle, RefreshCw, AlertTriangle, Award, Calendar } from 'lucide-react';

export const AdminRulesPage: React.FC = () => {
  const { tenantConfig, tenantSlug, refreshConfig } = useTheme();

  const [shortageThreshold, setShortageThreshold] = useState<number>(75);
  const [cieMaxMarks, setCieMaxMarks] = useState<number>(50);
  const [cieMinEligibility, setCieMinEligibility] = useState<number>(20);
  const [feeDueGraceDays, setFeeDueGraceDays] = useState<number>(10);

  const [saving, setSaving] = useState(false);
  const [successMsg, setSuccessMsg] = useState('');
  const [errorMsg, setErrorMsg] = useState('');

  useEffect(() => {
    if (tenantConfig && tenantConfig.rules) {
      setShortageThreshold(tenantConfig.rules.shortageThreshold ?? 75);
      setCieMaxMarks(tenantConfig.rules.cieMaxMarks ?? 50);
      setCieMinEligibility(tenantConfig.rules.cieMinEligibility ?? 20);
      setFeeDueGraceDays(tenantConfig.rules.feeDueGraceDays ?? 10);
    }
  }, [tenantConfig]);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!tenantConfig) return;
    setSaving(true);
    setSuccessMsg('');
    setErrorMsg('');
    try {
      await adminApi.updateRules(tenantConfig.id, {
        shortageThreshold,
        cieMaxMarks,
        cieMinEligibility,
        feeDueGraceDays,
      });
      setSuccessMsg('Academic and financial business rules successfully saved!');
      await refreshConfig();
    } catch (err: any) {
      setErrorMsg(err.response?.data?.message || 'Failed to update rules configuration.');
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="container-fluid py-3">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 className="fw-bold mb-1 d-flex align-items-center gap-2">
            <Settings className="text-primary" />
            Institutional Rules Engine ({tenantConfig?.name || tenantSlug.toUpperCase()})
          </h3>
          <p className="text-muted small mb-0">
            Define compliance criteria, academic shortage triggers, continuous internal evaluation passing bars, and billing grace policies.
          </p>
        </div>
        <button
          className="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"
          onClick={() => refreshConfig()}
        >
          <RefreshCw size={14} /> Refresh
        </button>
      </div>

      {successMsg && (
        <div className="alert alert-success d-flex align-items-center gap-2 py-2 mb-3">
          <CheckCircle2 size={18} />
          <span>{successMsg}</span>
        </div>
      )}

      {errorMsg && (
        <div className="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3">
          <AlertCircle size={18} />
          <span>{errorMsg}</span>
        </div>
      )}

      <form onSubmit={handleSave}>
        <div className="row g-4">
          {/* Attendance Rules */}
          <div className="col-md-6">
            <div className="card shadow-sm border-0 h-100">
              <div className="card-header bg-white border-bottom py-3">
                <div className="d-flex align-items-center gap-2">
                  <AlertTriangle className="text-warning" size={20} />
                  <h5 className="mb-0 fw-bold">Attendance Shortage Threshold</h5>
                </div>
              </div>
              <div className="card-body">
                <p className="text-muted small">
                  Students whose aggregate or subject-level attendance drops below this percentage are automatically flagged in red with a shortage notice on the parent dashboard.
                </p>
                <div className="mb-3">
                  <label className="form-label fw-bold">Minimum Required Attendance (%)</label>
                  <div className="input-group">
                    <input
                      type="number"
                      min="50"
                      max="100"
                      step="1"
                      className="form-control"
                      value={shortageThreshold}
                      onChange={(e) => setShortageThreshold(parseFloat(e.target.value) || 0)}
                      required
                    />
                    <span className="input-group-text">%</span>
                  </div>
                  <div className="form-text">
                    Contineo standard: 75% minimum (AICTE / VTU guidelines).
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* CIE Internal Marks Rules */}
          <div className="col-md-6">
            <div className="card shadow-sm border-0 h-100">
              <div className="card-header bg-white border-bottom py-3">
                <div className="d-flex align-items-center gap-2">
                  <Award className="text-primary" size={20} />
                  <h5 className="mb-0 fw-bold">CIE Evaluation Criteria</h5>
                </div>
              </div>
              <div className="card-body">
                <p className="text-muted small">
                  Configure maximum aggregate Continuous Internal Evaluation marks and minimum eligibility cutoff required to sit for Semester End Examinations (SEE).
                </p>
                <div className="mb-3">
                  <label className="form-label fw-bold">Maximum CIE Marks</label>
                  <input
                    type="number"
                    min="20"
                    max="100"
                    className="form-control"
                    value={cieMaxMarks}
                    onChange={(e) => setCieMaxMarks(parseInt(e.target.value, 10) || 0)}
                    required
                  />
                  <div className="form-text">Autonomous college standard: 50 Marks.</div>
                </div>
                <div className="mb-3">
                  <label className="form-label fw-bold">Minimum Eligibility Threshold (Passing Marks)</label>
                  <input
                    type="number"
                    min="0"
                    max={cieMaxMarks}
                    className="form-control"
                    value={cieMinEligibility}
                    onChange={(e) => setCieMinEligibility(parseInt(e.target.value, 10) || 0)}
                    required
                  />
                  <div className="form-text">Typically 40% of max marks (e.g. 20 out of 50).</div>
                </div>
              </div>
            </div>
          </div>

          {/* Fee Grace Period */}
          <div className="col-md-6">
            <div className="card shadow-sm border-0 h-100">
              <div className="card-header bg-white border-bottom py-3">
                <div className="d-flex align-items-center gap-2">
                  <Calendar className="text-success" size={20} />
                  <h5 className="mb-0 fw-bold">Fee Settlement Grace Period</h5>
                </div>
              </div>
              <div className="card-body">
                <p className="text-muted small">
                  Number of calendar days after the semester fee due date before overdue penalties or hall-ticket holds are initiated.
                </p>
                <div className="mb-3">
                  <label className="form-label fw-bold">Grace Period (Days)</label>
                  <div className="input-group">
                    <input
                      type="number"
                      min="0"
                      max="60"
                      className="form-control"
                      value={feeDueGraceDays}
                      onChange={(e) => setFeeDueGraceDays(parseInt(e.target.value, 10) || 0)}
                      required
                    />
                    <span className="input-group-text">Days</span>
                  </div>
                  <div className="form-text">Default: 10 days post due date.</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="mt-4 d-flex justify-content-end">
          <button
            type="submit"
            className="btn btn-primary px-4 py-2 d-flex align-items-center gap-2 shadow-sm"
            disabled={saving}
          >
            {saving ? <span className="spinner-border spinner-border-sm" /> : <Save size={18} />}
            <span>Save Rules Configuration</span>
          </button>
        </div>
      </form>
    </div>
  );
};
