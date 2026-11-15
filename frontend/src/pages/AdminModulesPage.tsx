import React, { useState, useEffect } from 'react';
import { useTheme } from '../theme/ThemeContext';
import { adminApi } from '../api/client';
import { Sliders, Save, CheckCircle2, AlertCircle, RefreshCw } from 'lucide-react';

interface ModuleState {
  key: string;
  label: string;
  icon: string;
  route: string;
  order: number;
  enabled: boolean;
}

export const AdminModulesPage: React.FC = () => {
  const { tenantConfig, tenantSlug, refreshConfig } = useTheme();
  const [modules, setModules] = useState<ModuleState[]>([]);
  const [savingKey, setSavingKey] = useState<string | null>(null);
  const [successMsg, setSuccessMsg] = useState('');
  const [errorMsg, setErrorMsg] = useState('');

  useEffect(() => {
    if (tenantConfig && tenantConfig.modules) {
      setModules([...tenantConfig.modules].sort((a, b) => a.order - b.order));
    }
  }, [tenantConfig]);

  const handleToggle = (key: string) => {
    setModules((prev) =>
      prev.map((m) => (m.key === key ? { ...m, enabled: !m.enabled } : m))
    );
  };

  const handleLabelChange = (key: string, label: string) => {
    setModules((prev) =>
      prev.map((m) => (m.key === key ? { ...m, label } : m))
    );
  };

  const handleOrderChange = (key: string, orderStr: string) => {
    const order = parseInt(orderStr, 10) || 0;
    setModules((prev) =>
      prev.map((m) => (m.key === key ? { ...m, order } : m))
    );
  };

  const handleSaveModule = async (mod: ModuleState) => {
    if (!tenantConfig) return;
    setSavingKey(mod.key);
    setErrorMsg('');
    setSuccessMsg('');
    try {
      await adminApi.updateTenantModule(tenantConfig.id, {
        moduleKey: mod.key,
        isEnabled: mod.enabled,
        customLabel: mod.label,
        displayOrder: mod.order,
      });
      setSuccessMsg(`Module "${mod.key}" updated successfully!`);
      await refreshConfig();
    } catch (err: any) {
      setErrorMsg(err.response?.data?.message || `Failed to update module "${mod.key}".`);
    } finally {
      setSavingKey(null);
    }
  };

  return (
    <div className="container-fluid py-3">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 className="fw-bold mb-1 d-flex align-items-center gap-2">
            <Sliders className="text-primary" />
            Modules Registry ({tenantConfig?.name || tenantSlug.toUpperCase()})
          </h3>
          <p className="text-muted small mb-0">
            Enable or disable parent modules, configure custom labels, and set navigation display ordering.
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

      <div className="card shadow-sm border-0">
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light">
                <tr>
                  <th style={{ width: '80px' }} className="text-center">Status</th>
                  <th>Module Key</th>
                  <th>Icon</th>
                  <th>Display Label</th>
                  <th>Route</th>
                  <th style={{ width: '100px' }} className="text-center">Order</th>
                  <th style={{ width: '120px' }} className="text-end pe-4">Action</th>
                </tr>
              </thead>
              <tbody>
                {modules.length === 0 ? (
                  <tr>
                    <td colSpan={7} className="text-center py-4 text-muted">
                      No modules registered for this tenant.
                    </td>
                  </tr>
                ) : (
                  modules.map((m) => (
                    <tr key={m.key} className={m.enabled ? '' : 'table-light text-muted'}>
                      <td className="text-center">
                        <div className="form-check form-switch d-inline-block">
                          <input
                            className="form-check-input"
                            type="checkbox"
                            role="switch"
                            checked={m.enabled}
                            onChange={() => handleToggle(m.key)}
                          />
                        </div>
                      </td>
                      <td>
                        <span className="badge bg-secondary font-monospace">{m.key}</span>
                      </td>
                      <td>
                        <span className="badge bg-light text-dark border font-monospace">{m.icon}</span>
                      </td>
                      <td>
                        <input
                          type="text"
                          className="form-control form-control-sm"
                          value={m.label}
                          onChange={(e) => handleLabelChange(m.key, e.target.value)}
                        />
                      </td>
                      <td>
                        <code className="text-primary">{m.route}</code>
                      </td>
                      <td className="text-center">
                        <input
                          type="number"
                          className="form-control form-control-sm text-center"
                          value={m.order}
                          onChange={(e) => handleOrderChange(m.key, e.target.value)}
                        />
                      </td>
                      <td className="text-end pe-4">
                        <button
                          className="btn btn-sm btn-primary d-inline-flex align-items-center gap-1"
                          disabled={savingKey === m.key}
                          onClick={() => handleSaveModule(m)}
                        >
                          {savingKey === m.key ? (
                            <span className="spinner-border spinner-border-sm" />
                          ) : (
                            <Save size={14} />
                          )}
                          Save
                        </button>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};
