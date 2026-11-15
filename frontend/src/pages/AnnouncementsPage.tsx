import React, { useEffect, useState } from 'react';
import { parentApi } from '../api/client';
import { AnnouncementItem } from '../types';
import { Calendar, Tag, FileText } from 'lucide-react';

export const AnnouncementsPage: React.FC = () => {
  const [list, setList] = useState<AnnouncementItem[]>([]);
  const [filter, setFilter] = useState<string>('All');
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    parentApi.getAnnouncements().then((res) => {
      if (res.success && res.data) {
        setList(res.data);
      }
      setLoading(false);
    });
  }, []);

  if (loading) {
    return <div className="p-5 text-center"><div className="spinner-border text-primary" /></div>;
  }

  const categories = ['All', ...Array.from(new Set(list.map((a) => a.category)))];
  const filtered = filter === 'All' ? list : list.filter((a) => a.category === filter);

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 className="fw-bold text-navy mb-1">Institutional Circulars & Announcements</h4>
          <p className="text-muted small mb-0">Official academic, fee, and examination notifications</p>
        </div>
      </div>

      {/* Filter Tabs */}
      <div className="d-flex flex-wrap gap-2 mb-4">
        {categories.map((cat) => (
          <button
            key={cat}
            className={`btn btn-sm ${filter === cat ? 'btn-primary' : 'btn-light border'} px-3 py-1`}
            onClick={() => setFilter(cat)}
          >
            {cat}
          </button>
        ))}
      </div>

      {/* Announcements List */}
      <div className="row g-3">
        {filtered.map((item) => (
          <div key={item.id} className="col-12">
            <div className="card shadow-sm border-0">
              <div className="card-body p-4">
                <div className="d-flex justify-content-between align-items-start mb-2">
                  <div className="d-flex align-items-center gap-2">
                    <span className="badge bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1">
                      <Tag size={12} /> {item.category}
                    </span>
                    <span className="badge bg-light text-muted border">{item.termName}</span>
                  </div>
                  <span className="text-muted small d-flex align-items-center gap-1">
                    <Calendar size={14} /> {item.publishedDate}
                  </span>
                </div>

                <h5 className="fw-bold text-dark mb-2">{item.title}</h5>
                <p className="text-secondary mb-3">{item.description}</p>

                {item.attachmentFile && (
                  <div className="d-flex align-items-center gap-2 text-primary small fw-semibold">
                    <FileText size={16} />
                    <span>Attached Document: {item.attachmentFile}</span>
                  </div>
                )}
              </div>
            </div>
          </div>
        ))}

        {filtered.length === 0 && (
          <div className="col-12 text-center p-5 text-muted">No circulars found in this category.</div>
        )}
      </div>
    </div>
  );
};
