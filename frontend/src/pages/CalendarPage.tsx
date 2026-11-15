import React, { useEffect, useState } from 'react';
import { parentApi } from '../api/client';
import { CalendarEventItem } from '../types';
import { Calendar as CalendarIcon, Download, Clock, Tag } from 'lucide-react';
import { useTranslation } from 'react-i18next';

export const CalendarPage: React.FC = () => {
  const [events, setEvents] = useState<CalendarEventItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState<string>('ALL');
  const { t } = useTranslation();

  useEffect(() => {
    parentApi.getCalendarEvents().then((res) => {
      if (res.success && res.data) {
        setEvents(res.data);
      }
      setLoading(false);
    });
  }, []);

  const handleExportIcs = () => {
    window.location.href = '/api/v1/calendar/export.ics';
  };

  const filteredEvents = events.filter((e) => {
    if (filter === 'ALL') return true;
    return e.eventType === filter;
  });

  const getBadgeClass = (type: string) => {
    switch (type) {
      case 'EXAM':
        return 'bg-danger text-white';
      case 'HOLIDAY':
        return 'bg-success text-white';
      case 'FEE_DUE':
        return 'bg-warning text-dark';
      case 'ACADEMIC':
      default:
        return 'bg-primary text-white';
    }
  };

  return (
    <div className="container-fluid py-4">
      {/* Title & Actions */}
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
          <h4 className="fw-bold text-navy mb-1 d-flex align-items-center gap-2">
            <CalendarIcon className="text-primary" size={24} />
            {t('calendar.title')}
          </h4>
          <p className="text-muted small mb-0">
            Official semester timeline, examination schedules, public holidays, and academic milestones
          </p>
        </div>

        <button
          onClick={handleExportIcs}
          className="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm"
        >
          <Download size={16} />
          <span>{t('calendar.exportIcs')}</span>
        </button>
      </div>

      {/* Filter Tabs */}
      <div className="d-flex gap-2 mb-4 flex-wrap">
        {['ALL', 'EXAM', 'ACADEMIC', 'HOLIDAY', 'FEE_DUE'].map((cat) => (
          <button
            key={cat}
            className={`btn btn-sm px-3 rounded-pill ${
              filter === cat ? 'btn-primary' : 'btn-outline-secondary'
            }`}
            onClick={() => setFilter(cat)}
          >
            {cat === 'ALL' ? 'All Events' : cat}
          </button>
        ))}
      </div>

      {/* Events List */}
      {loading ? (
        <div className="text-center py-5">
          <div className="spinner-border text-primary" role="status" />
        </div>
      ) : filteredEvents.length === 0 ? (
        <div className="card p-5 text-center text-muted">No calendar events found for this category.</div>
      ) : (
        <div className="row g-3">
          {filteredEvents.map((evt) => (
            <div key={evt.id} className="col-12 col-md-6 col-lg-4">
              <div className="card h-100 shadow-sm border-0 border-start border-4 border-primary">
                <div className="card-body">
                  <div className="d-flex justify-content-between align-items-start mb-2">
                    <span className={`badge ${getBadgeClass(evt.eventType)} px-2 py-1`}>
                      <Tag size={12} className="me-1 inline-block" />
                      {evt.eventType}
                    </span>
                    <span className="text-muted small d-flex align-items-center gap-1">
                      <Clock size={13} />
                      {evt.startDate}
                    </span>
                  </div>

                  <h6 className="fw-bold text-dark mb-2">{evt.title}</h6>
                  <p className="text-muted small mb-3">{evt.description || 'Institutional academic milestone'}</p>

                  <div className="pt-2 border-top text-muted" style={{ fontSize: '0.78rem' }}>
                    <strong>Date Range:</strong> {evt.startDate} to {evt.endDate}
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};
