import React, { useEffect, useState } from 'react';
import { useTheme } from '../theme/ThemeContext';
import { adminApi } from '../api/client';
import { Palette, Save, CheckCircle2, RefreshCw } from 'lucide-react';

export const AdminThemePage: React.FC = () => {
  const { tenantConfig, tenantSlug, refreshConfig } = useTheme();

  const [primary, setPrimary] = useState('#1e88e5');
  const [primaryHover, setPrimaryHover] = useState('#1565c0');
  const [bgLeft, setBgLeft] = useState('#b3dff8');
  const [bgRight, setBgRight] = useState('#242466');
  const [navy, setNavy] = useState('#1b2a47');
  const [borderRadius, setBorderRadius] = useState('8px');
  const [crestIcon, setCrestIcon] = useState('🏛️');

  const [welcomeText, setWelcomeText] = useState('');
  const [noticeText, setNoticeText] = useState('');
  const [footerText, setFooterText] = useState('');

  const [saving, setSaving] = useState(false);
  const [successMsg, setSuccessMsg] = useState('');

  useEffect(() => {
    if (tenantConfig) {
      const t = tenantConfig.theme || {};
      setPrimary(t.primary || '#1e88e5');
      setPrimaryHover(t.primaryHover || '#1565c0');
      setBgLeft(t.bgLeft || '#b3dff8');
      setBgRight(t.bgRight || '#242466');
      setNavy(t.navy || '#1b2a47');
      setBorderRadius(t.borderRadius || '8px');
      setCrestIcon(t.crestIcon || '🏛️');
      setWelcomeText(tenantConfig.welcomeText || '');
      setNoticeText(tenantConfig.noticeText || '');
      setFooterText(tenantConfig.footerText || '');
    }
  }, [tenantConfig]);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    try {
      const themeJson = JSON.stringify({
        primary,
        primaryHover,
        bgLeft,
        bgRight,
        navy,
        borderRadius,
        crestIcon,
        fontFamily: '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif'
      });

      const tenantId = tenantConfig?.id || 1;
      const res = await adminApi.updateTenantSettings(tenantId, {
        themeJson,
        welcomeText,
        noticeText,
        footerText,
      });

      if (res.success) {
        setSuccessMsg('Branding and theme applied successfully.');
        await refreshConfig();
        setTimeout(() => setSuccessMsg(''), 4000);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setSaving(false);
    }
  };

  const applyPreset = (preset: 'contineo' | 'emerald') => {
    if (preset === 'contineo') {
      setPrimary('#1e88e5');
      setPrimaryHover('#1565c0');
      setBgLeft('#b3dff8');
      setBgRight('#242466');
      setNavy('#1b2a47');
      setBorderRadius('8px');
      setCrestIcon('🏛️');
    } else {
      setPrimary('#059669');
      setPrimaryHover('#047857');
      setBgLeft('#d1fae5');
      setBgRight('#064e3b');
      setNavy('#065f46');
      setBorderRadius('12px');
      setCrestIcon('🎓');
    }
  };

  return (
    <div className="container-fluid py-4">
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 className="fw-bold text-navy mb-1 d-flex align-items-center gap-2">
            <Palette className="text-warning" size={24} />
            Tenant Branding & Live Theme Customizer ({tenantSlug.toUpperCase()})
          </h4>
          <p className="text-muted small mb-0">
            Customize primary institutional colors, gradient backdrops, and welcome copy in real-time
          </p>
        </div>

        <div className="d-flex gap-2">
          <button
            type="button"
            className="btn btn-outline-primary btn-sm"
            onClick={() => applyPreset('contineo')}
          >
            Load DIE Preset
          </button>
          <button
            type="button"
            className="btn btn-outline-success btn-sm"
            onClick={() => applyPreset('emerald')}
          >
            Load AIT Preset
          </button>
        </div>
      </div>

      {successMsg && (
        <div className="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
          <CheckCircle2 size={18} />
          <span>{successMsg}</span>
        </div>
      )}

      <div className="row g-4">
        {/* Theme Settings Form */}
        <div className="col-12 col-lg-7">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-white py-3 border-bottom">
              <h6 className="fw-bold mb-0 text-navy">Theme Variables Editor ({tenantConfig?.name || 'DIE'})</h6>
            </div>
            <div className="card-body p-4">
              <form onSubmit={handleSave}>
                <div className="row g-3 mb-4">
                  <div className="col-12 col-sm-6">
                    <label className="form-label small fw-semibold">Primary Brand Color</label>
                    <div className="d-flex align-items-center gap-2">
                      <input
                        type="color"
                        className="form-control form-control-color"
                        value={primary}
                        onChange={(e) => setPrimary(e.target.value)}
                      />
                      <input
                        type="text"
                        className="form-control form-control-sm font-monospace"
                        value={primary}
                        onChange={(e) => setPrimary(e.target.value)}
                      />
                    </div>
                  </div>

                  <div className="col-12 col-sm-6">
                    <label className="form-label small fw-semibold">Primary Hover Color</label>
                    <div className="d-flex align-items-center gap-2">
                      <input
                        type="color"
                        className="form-control form-control-color"
                        value={primaryHover}
                        onChange={(e) => setPrimaryHover(e.target.value)}
                      />
                      <input
                        type="text"
                        className="form-control form-control-sm font-monospace"
                        value={primaryHover}
                        onChange={(e) => setPrimaryHover(e.target.value)}
                      />
                    </div>
                  </div>

                  <div className="col-12 col-sm-6">
                    <label className="form-label small fw-semibold">Login Gradient Left</label>
                    <div className="d-flex align-items-center gap-2">
                      <input
                        type="color"
                        className="form-control form-control-color"
                        value={bgLeft}
                        onChange={(e) => setBgLeft(e.target.value)}
                      />
                      <input
                        type="text"
                        className="form-control form-control-sm font-monospace"
                        value={bgLeft}
                        onChange={(e) => setBgLeft(e.target.value)}
                      />
                    </div>
                  </div>

                  <div className="col-12 col-sm-6">
                    <label className="form-label small fw-semibold">Login Gradient Right</label>
                    <div className="d-flex align-items-center gap-2">
                      <input
                        type="color"
                        className="form-control form-control-color"
                        value={bgRight}
                        onChange={(e) => setBgRight(e.target.value)}
                      />
                      <input
                        type="text"
                        className="form-control form-control-sm font-monospace"
                        value={bgRight}
                        onChange={(e) => setBgRight(e.target.value)}
                      />
                    </div>
                  </div>

                  <div className="col-12 col-sm-6">
                    <label className="form-label small fw-semibold">Crest Emoji / Icon</label>
                    <input
                      type="text"
                      className="form-control form-control-sm fs-5"
                      value={crestIcon}
                      onChange={(e) => setCrestIcon(e.target.value)}
                    />
                  </div>

                  <div className="col-12 col-sm-6">
                    <label className="form-label small fw-semibold">Border Radius</label>
                    <select
                      className="form-select form-select-sm"
                      value={borderRadius}
                      onChange={(e) => setBorderRadius(e.target.value)}
                    >
                      <option value="4px">4px (Compact)</option>
                      <option value="8px">8px (Standard Contineo)</option>
                      <option value="12px">12px (Modern Rounded)</option>
                      <option value="16px">16px (Pill Soft)</option>
                    </select>
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Welcome Panel Text</label>
                  <textarea
                    className="form-control form-control-sm"
                    rows={2}
                    value={welcomeText}
                    onChange={(e) => setWelcomeText(e.target.value)}
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Notice Board Headline</label>
                  <textarea
                    className="form-control form-control-sm"
                    rows={2}
                    value={noticeText}
                    onChange={(e) => setNoticeText(e.target.value)}
                  />
                </div>

                <div className="mb-4">
                  <label className="form-label small fw-semibold">Footer Disclaimer Text</label>
                  <input
                    type="text"
                    className="form-control form-control-sm"
                    value={footerText}
                    onChange={(e) => setFooterText(e.target.value)}
                  />
                </div>

                <button
                  type="submit"
                  className="btn btn-primary d-flex align-items-center gap-2 px-4"
                  disabled={saving}
                >
                  <Save size={16} />
                  <span>{saving ? 'Saving...' : 'Save & Publish Theme'}</span>
                </button>
              </form>
            </div>
          </div>
        </div>

        {/* Live Preview Card */}
        <div className="col-12 col-lg-5">
          <div className="card shadow-sm border-0 sticky-top" style={{ top: '1rem' }}>
            <div className="card-header bg-white py-3 border-bottom">
              <h6 className="fw-bold mb-0 text-navy d-flex align-items-center gap-2">
                <RefreshCw size={16} className="text-primary" />
                Live Dynamic Preview
              </h6>
            </div>
            <div
              className="card-body p-4 text-white rounded-bottom"
              style={{
                background: `linear-gradient(135deg, ${bgLeft} 0%, ${bgRight} 100%)`,
                borderRadius: borderRadius,
              }}
            >
              <div className="d-flex align-items-center gap-2 mb-3">
                <span className="fs-3">{crestIcon}</span>
                <div>
                  <h6 className="fw-bold mb-0 text-white">{tenantConfig?.name || 'Demo Institute'}</h6>
                  <span className="badge bg-light text-dark" style={{ fontSize: '0.68rem' }}>AUTONOMOUS</span>
                </div>
              </div>

              <div
                className="p-3 mb-3 text-dark bg-white shadow-sm"
                style={{ borderRadius: borderRadius }}
              >
                <div className="fw-bold small mb-1" style={{ color: primary }}>Notice Board Preview:</div>
                <div className="small text-muted">{noticeText || 'Academic sessions are active.'}</div>
              </div>

              <button
                type="button"
                className="btn text-white w-100 fw-bold shadow-sm"
                style={{ backgroundColor: primary, borderRadius: borderRadius }}
              >
                Sample Portal Action
              </button>

              <div className="text-center mt-3" style={{ fontSize: '0.72rem', opacity: 0.85 }}>
                {footerText || 'DEMO – fictional data'}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
