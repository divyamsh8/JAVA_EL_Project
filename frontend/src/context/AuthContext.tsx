import React, { createContext, useContext, useState, useEffect } from 'react';
import { UserSummary, SiblingSummary } from '../types';
import { authApi, parentApi } from '../api/client';

interface AuthContextType {
  user: UserSummary | null;
  token: string | null;
  activeTerm: string;
  setActiveTerm: (term: string) => void;
  siblings: SiblingSummary[];
  activeStudentId: number | null;
  switchStudent: (studentId: number) => void;
  login: (token: string, user: UserSummary, siblings?: SiblingSummary[]) => void;
  logout: () => void;
  isLoading: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [token, setToken] = useState<string | null>(() => localStorage.getItem('token'));
  const [user, setUser] = useState<UserSummary | null>(() => {
    const saved = localStorage.getItem('user');
    return saved ? JSON.parse(saved) : null;
  });
  const [siblings, setSiblings] = useState<SiblingSummary[]>(() => {
    const saved = localStorage.getItem('siblings');
    return saved ? JSON.parse(saved) : [];
  });
  const [activeStudentId, setActiveStudentId] = useState<number | null>(() => {
    const saved = localStorage.getItem('active_student_id');
    if (saved) return Number(saved);
    const userSaved = localStorage.getItem('user');
    if (userSaved) {
      const u = JSON.parse(userSaved);
      return u.studentId || null;
    }
    return null;
  });
  const [activeTerm, setActiveTerm] = useState<string>(() => {
    return localStorage.getItem('active_term') || 'parentseven';
  });
  const [isLoading, setIsLoading] = useState<boolean>(true);

  useEffect(() => {
    const initAuth = async () => {
      const storedToken = localStorage.getItem('token');
      if (storedToken) {
        try {
          const res = await authApi.getCurrentUser();
          if (res.success && res.data) {
            setUser(res.data);
            localStorage.setItem('user', JSON.stringify(res.data));
            if (!activeStudentId && res.data.studentId) {
              setActiveStudentId(res.data.studentId);
              localStorage.setItem('active_student_id', String(res.data.studentId));
            }
            // Fetch siblings
            if (res.data.studentId) {
              const sibRes = await parentApi.getSiblings(res.data.studentId);
              if (sibRes.success && sibRes.data) {
                setSiblings(sibRes.data);
                localStorage.setItem('siblings', JSON.stringify(sibRes.data));
              }
            }
          }
        } catch {
          // Token expired or invalid
          setToken(null);
          setUser(null);
          setSiblings([]);
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          localStorage.removeItem('siblings');
          localStorage.removeItem('active_student_id');
        }
      }
      setIsLoading(false);
    };

    initAuth();
  }, []);

  const handleSetActiveTerm = (term: string) => {
    setActiveTerm(term);
    localStorage.setItem('active_term', term);
  };

  const switchStudent = (studentId: number) => {
    setActiveStudentId(studentId);
    localStorage.setItem('active_student_id', String(studentId));
  };

  const login = (newToken: string, newUser: UserSummary, newSiblings?: SiblingSummary[]) => {
    setToken(newToken);
    setUser(newUser);
    localStorage.setItem('token', newToken);
    localStorage.setItem('user', JSON.stringify(newUser));

    const sId = newUser.studentId || (newSiblings && newSiblings.length > 0 ? newSiblings[0].studentId : null);
    setActiveStudentId(sId);
    if (sId) localStorage.setItem('active_student_id', String(sId));

    if (newSiblings) {
      setSiblings(newSiblings);
      localStorage.setItem('siblings', JSON.stringify(newSiblings));
    }
  };

  const logout = () => {
    authApi.logout();
    setToken(null);
    setUser(null);
    setSiblings([]);
    setActiveStudentId(null);
    localStorage.removeItem('siblings');
    localStorage.removeItem('active_student_id');
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        token,
        activeTerm,
        setActiveTerm: handleSetActiveTerm,
        siblings,
        activeStudentId,
        switchStudent,
        login,
        logout,
        isLoading,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
