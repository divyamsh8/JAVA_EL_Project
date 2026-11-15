import React, { createContext, useContext, useEffect, useState } from 'react';
import axios from 'axios';
import { TenantConfig } from '../types';

interface ThemeContextType {
  tenantSlug: string;
  setTenantSlug: (slug: string) => void;
  tenantConfig: TenantConfig | null;
  loading: boolean;
  isDarkMode: boolean;
  toggleDarkMode: () => void;
  refreshConfig: () => Promise<void>;
}

const ThemeContext = createContext<ThemeContextType | undefined>(undefined);

export const ThemeProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [tenantSlug, setTenantSlugState] = useState<string>(() => {
    return localStorage.getItem('portal_tenant') || 'die';
  });

  const [tenantConfig, setTenantConfig] = useState<TenantConfig | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [isDarkMode, setIsDarkMode] = useState<boolean>(() => {
    return localStorage.getItem('portal_dark_mode') === 'true';
  });

  const fetchConfig = async (slug: string) => {
    try {
      setLoading(true);
      const res = await axios.get('/api/v1/tenant/config', {
        headers: { 'X-Tenant': slug }
      });
      if (res.data?.success && res.data?.data) {
        const config: TenantConfig = res.data.data;
        setTenantConfig(config);
        applyThemeVariables(config, isDarkMode);
      }
    } catch (e) {
      console.error('Failed to load tenant config', e);
    } finally {
      setLoading(false);
    }
  };

  const applyThemeVariables = (config: TenantConfig, dark: boolean) => {
    const root = document.documentElement;
    const theme = config.theme || {};

    const primary = theme.primary || '#1e88e5';
    const primaryHover = theme.primaryHover || '#1565c0';
    const bgLeft = theme.bgLeft || '#b3dff8';
    const bgRight = theme.bgRight || '#242466';
    const navy = theme.navy || '#1b2a47';
    const radius = theme.borderRadius || '8px';

    root.style.setProperty('--tenant-primary', primary);
    root.style.setProperty('--tenant-primary-hover', primaryHover);
    root.style.setProperty('--tenant-bg-left', bgLeft);
    root.style.setProperty('--tenant-bg-right', bgRight);
    root.style.setProperty('--tenant-navy', navy);
    root.style.setProperty('--tenant-radius', radius);

    if (dark) {
      root.setAttribute('data-theme', 'dark');
      root.classList.add('dark');
    } else {
      root.removeAttribute('data-theme');
      root.classList.remove('dark');
    }
  };

  useEffect(() => {
    fetchConfig(tenantSlug);
  }, [tenantSlug]);

  const setTenantSlug = (slug: string) => {
    localStorage.setItem('portal_tenant', slug);
    setTenantSlugState(slug);
  };

  const toggleDarkMode = () => {
    const next = !isDarkMode;
    setIsDarkMode(next);
    localStorage.setItem('portal_dark_mode', String(next));
    if (tenantConfig) {
      applyThemeVariables(tenantConfig, next);
    }
  };

  return (
    <ThemeContext.Provider
      value={{
        tenantSlug,
        setTenantSlug,
        tenantConfig,
        loading,
        isDarkMode,
        toggleDarkMode,
        refreshConfig: () => fetchConfig(tenantSlug)
      }}
    >
      {children}
    </ThemeContext.Provider>
  );
};

export const useTheme = () => {
  const context = useContext(ThemeContext);
  if (!context) {
    throw new Error('useTheme must be used within a ThemeProvider');
  }
  return context;
};
