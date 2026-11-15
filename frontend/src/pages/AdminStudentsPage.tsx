import React, { useEffect, useState } from 'react';
import { adminApi } from '../api/client';
import { StudentProfile } from '../types';
import { Search, Filter, Phone, Mail } from 'lucide-react';
import { Link } from 'react-router-dom';

export const AdminStudentsPage: React.FC = () => {
  const [students, setStudents] = useState<StudentProfile[]>([]);
  const [search, setSearch] = useState('');
  const [selectedDept, setSelectedDept] = useState<string>('all');

  useEffect(() => {
    loadStudents();
  }, []);

  const loadStudents = async (query?: string) => {
    try {
      const res = await adminApi.listStudents(query);
      if (res.success && res.data) {
        setStudents(res.data);
      }
    } catch (e) {
      console.error(e);
    }
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    loadStudents(search);
  };

  const filtered = selectedDept === 'all'
    ? students
    : students.filter((s) => s.departmentCode === selectedDept);

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 className="fw-bold text-dark mb-1">Students & Parents Directory</h4>
          <p className="text-muted small mb-0">Total Enrolled Records: {students.length}</p>
        </div>
      </div>

      {/* Filter and Search Bar */}
      <div className="card shadow-sm border-0 mb-4">
        <div className="card-body p-3">
          <div className="row g-3">
            <div className="col-12 col-md-8">
              <form onSubmit={handleSearch} className="input-group">
                <span className="input-group-text bg-white border-end-0">
                  <Search size={16} className="text-muted" />
                </span>
                <input
                  type="text"
                  className="form-control border-start-0"
                  placeholder="Search by USN or Student Name (e.g. 1DM24CS001, Aarav)..."
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                />
                <button type="submit" className="btn btn-primary px-4 fw-semibold">
                  Search
                </button>
              </form>
            </div>

            <div className="col-12 col-md-4">
              <div className="input-group">
                <span className="input-group-text bg-white">
                  <Filter size={16} className="text-muted" />
                </span>
                <select
                  className="form-select"
                  value={selectedDept}
                  onChange={(e) => setSelectedDept(e.target.value)}
                >
                  <option value="all">All Departments</option>
                  <option value="CSE">Computer Science (CSE)</option>
                  <option value="ECE">Electronics (ECE)</option>
                  <option value="MECH">Mechanical (MECH)</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Students Table */}
      <div className="card shadow-sm border-0">
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light small">
                <tr>
                  <th>USN</th>
                  <th>Student Name</th>
                  <th>Department & Sem</th>
                  <th>Father / Mother</th>
                  <th>Parent Contact</th>
                  <th className="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                {filtered.map((s) => (
                  <tr key={s.id}>
                    <td className="fw-bold text-navy">{s.usn}</td>
                    <td>
                      <div className="fw-semibold text-dark">{s.fullName}</div>
                      <div className="text-muted small">DOB: {s.dob} • {s.gender}</div>
                    </td>
                    <td>
                      <span className="badge bg-light text-dark border me-1">{s.departmentCode}</span>
                      <span className="text-muted small">Sem {s.currentSem}</span>
                    </td>
                    <td>
                      <div className="fw-medium text-dark">{s.fatherName}</div>
                      <div className="text-muted small">{s.motherName}</div>
                    </td>
                    <td>
                      <div className="d-flex align-items-center gap-1 small text-dark">
                        <Phone size={12} className="text-success" />
                        <span>{s.primaryMobile}</span>
                      </div>
                      <div className="d-flex align-items-center gap-1 small text-muted">
                        <Mail size={12} />
                        <span>{s.parentEmail}</span>
                      </div>
                    </td>
                    <td className="text-center">
                      <Link
                        to={`/profile?studentId=${s.id}`}
                        className="btn btn-outline-secondary btn-sm"
                      >
                        Profile
                      </Link>
                    </td>
                  </tr>
                ))}
                {filtered.length === 0 && (
                  <tr>
                    <td colSpan={6} className="text-center p-5 text-muted">
                      No students found matching your search.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};
