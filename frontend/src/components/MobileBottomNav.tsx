import React, { useState } from 'react';
import { NavLink } from 'react-router-dom';
import { LayoutDashboard, CalendarCheck, FileSpreadsheet, CreditCard, MoreHorizontal, Clock, Award, Calendar, Send, MessageSquare, UserCheck, X } from 'lucide-react';
import { Modal } from 'react-bootstrap';
import { useTranslation } from 'react-i18next';

export const MobileBottomNav: React.FC = () => {
  const [showMore, setShowMore] = useState(false);
  const { t } = useTranslation();

  return (
    <>
      <nav className="mobile-bottom-nav d-md-none fixed-bottom bg-white border-top shadow-lg py-1 px-2 d-flex justify-content-around align-items-center">
        <NavLink
          to="/dashboard"
          className={({ isActive }) =>
            `d-flex flex-column align-items-center text-decoration-none py-1 px-2 rounded ${
              isActive ? 'text-primary fw-bold' : 'text-muted'
            }`
          }
          style={{ fontSize: '0.72rem' }}
        >
          <LayoutDashboard size={20} />
          <span>{t('nav.dashboard')}</span>
        </NavLink>

        <NavLink
          to="/attendance"
          className={({ isActive }) =>
            `d-flex flex-column align-items-center text-decoration-none py-1 px-2 rounded ${
              isActive ? 'text-primary fw-bold' : 'text-muted'
            }`
          }
          style={{ fontSize: '0.72rem' }}
        >
          <CalendarCheck size={20} />
          <span>{t('nav.attendance')}</span>
        </NavLink>

        <NavLink
          to="/cie"
          className={({ isActive }) =>
            `d-flex flex-column align-items-center text-decoration-none py-1 px-2 rounded ${
              isActive ? 'text-primary fw-bold' : 'text-muted'
            }`
          }
          style={{ fontSize: '0.72rem' }}
        >
          <FileSpreadsheet size={20} />
          <span>CIE</span>
        </NavLink>

        <NavLink
          to="/fees"
          className={({ isActive }) =>
            `d-flex flex-column align-items-center text-decoration-none py-1 px-2 rounded ${
              isActive ? 'text-primary fw-bold' : 'text-muted'
            }`
          }
          style={{ fontSize: '0.72rem' }}
        >
          <CreditCard size={20} />
          <span>{t('nav.fees')}</span>
        </NavLink>

        <button
          onClick={() => setShowMore(true)}
          className="btn btn-link d-flex flex-column align-items-center text-decoration-none text-muted py-1 px-2 p-0 border-0"
          style={{ fontSize: '0.72rem' }}
        >
          <MoreHorizontal size={20} />
          <span>More</span>
        </button>
      </nav>

      {/* Action Sheet Modal for Additional Modules */}
      <Modal show={showMore} onHide={() => setShowMore(false)} centered className="mobile-action-sheet">
        <Modal.Header className="border-bottom pb-2">
          <Modal.Title className="fs-6 fw-bold">Parent Portal Services</Modal.Title>
          <button className="btn btn-sm btn-light p-1 rounded-circle" onClick={() => setShowMore(false)}>
            <X size={18} />
          </button>
        </Modal.Header>
        <Modal.Body className="p-3">
          <div className="row g-3">
            <div className="col-4 text-center">
              <NavLink
                to="/timetable"
                onClick={() => setShowMore(false)}
                className="d-flex flex-column align-items-center p-2 rounded text-decoration-none border bg-light text-dark h-100"
              >
                <Clock size={24} className="text-primary mb-1" />
                <span style={{ fontSize: '0.75rem' }}>Timetable</span>
              </NavLink>
            </div>

            <div className="col-4 text-center">
              <NavLink
                to="/results"
                onClick={() => setShowMore(false)}
                className="d-flex flex-column align-items-center p-2 rounded text-decoration-none border bg-light text-dark h-100"
              >
                <Award size={24} className="text-warning mb-1" />
                <span style={{ fontSize: '0.75rem' }}>Results</span>
              </NavLink>
            </div>

            <div className="col-4 text-center">
              <NavLink
                to="/calendar"
                onClick={() => setShowMore(false)}
                className="d-flex flex-column align-items-center p-2 rounded text-decoration-none border bg-light text-dark h-100"
              >
                <Calendar size={24} className="text-info mb-1" />
                <span style={{ fontSize: '0.75rem' }}>Calendar</span>
              </NavLink>
            </div>

            <div className="col-4 text-center">
              <NavLink
                to="/leave"
                onClick={() => setShowMore(false)}
                className="d-flex flex-column align-items-center p-2 rounded text-decoration-none border bg-light text-dark h-100"
              >
                <Send size={24} className="text-success mb-1" />
                <span style={{ fontSize: '0.75rem' }}>Leave App</span>
              </NavLink>
            </div>

            <div className="col-4 text-center">
              <NavLink
                to="/mentors"
                onClick={() => setShowMore(false)}
                className="d-flex flex-column align-items-center p-2 rounded text-decoration-none border bg-light text-dark h-100"
              >
                <UserCheck size={24} className="text-primary mb-1" />
                <span style={{ fontSize: '0.75rem' }}>Mentor</span>
              </NavLink>
            </div>

            <div className="col-4 text-center">
              <NavLink
                to="/feedback"
                onClick={() => setShowMore(false)}
                className="d-flex flex-column align-items-center p-2 rounded text-decoration-none border bg-light text-dark h-100"
              >
                <MessageSquare size={24} className="text-danger mb-1" />
                <span style={{ fontSize: '0.75rem' }}>Grievance</span>
              </NavLink>
            </div>
          </div>
        </Modal.Body>
      </Modal>
    </>
  );
};
