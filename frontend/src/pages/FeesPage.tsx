import React, { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { parentApi } from '../api/client';
import { FeeData, FeeItem } from '../types';
import { CreditCard, CheckCircle2, AlertCircle, Download, Smartphone, Building, ShieldCheck } from 'lucide-react';
import { Modal } from 'react-bootstrap';
import { useTranslation } from 'react-i18next';

export const FeesPage: React.FC = () => {
  const { activeStudentId } = useAuth();
  const [data, setData] = useState<FeeData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const { t } = useTranslation();

  // Payment Modal state
  const [payModalOpen, setPayModalOpen] = useState(false);
  const [selectedFee, setSelectedFee] = useState<FeeItem | null>(null);
  const [payAmount, setPayAmount] = useState<number>(0);
  const [payMode, setPayMode] = useState<string>('UPI');
  const [processing, setProcessing] = useState(false);
  const [paySuccessReceipt, setPaySuccessReceipt] = useState<string | null>(null);

  const loadFees = () => {
    if (activeStudentId) {
      setLoading(true);
      parentApi.getFees(activeStudentId).then((res) => {
        if (res.success && res.data) {
          setData(res.data);
        }
        setLoading(false);
      });
    }
  };

  useEffect(() => {
    loadFees();
  }, [activeStudentId]);

  const openPayModal = (fee: FeeItem) => {
    setSelectedFee(fee);
    setPayAmount(fee.balanceAmount);
    setPaySuccessReceipt(null);
    setPayModalOpen(true);
  };

  const handlePaySubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedFee || payAmount <= 0) return;

    setProcessing(true);
    try {
      const res = await parentApi.payFee(
        selectedFee.id,
        payAmount,
        payMode,
        activeStudentId || undefined
      );

      if (res.success && res.data) {
        setPaySuccessReceipt(res.data.receiptNo);
        loadFees();
      }
    } catch (e) {
      console.error(e);
    } finally {
      setProcessing(false);
    }
  };

  if (loading) {
    return <div className="p-5 text-center"><div className="spinner-border text-primary" /></div>;
  }

  if (!data) {
    return <div className="p-4 text-center text-muted">Fee records not found.</div>;
  }

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
          <h4 className="fw-bold text-navy mb-1">{t('fees.title')}</h4>
          <p className="text-muted small mb-0">Institutional tuition and laboratory fee ledger with instant receipt generation</p>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="row g-4 mb-4">
        <div className="col-12 col-md-4">
          <div className="stat-card">
            <div className="text-muted small fw-semibold text-uppercase">{t('fees.totalPayable')}</div>
            <h2 className="fw-bold mb-1 text-navy">₹{Number(data.totalPayable).toLocaleString('en-IN')}</h2>
            <div className="text-muted small">Academic Year 2025-26</div>
          </div>
        </div>

        <div className="col-12 col-md-4">
          <div className="stat-card">
            <div className="text-muted small fw-semibold text-uppercase">{t('fees.totalPaid')}</div>
            <h2 className="fw-bold mb-1 text-success">₹{Number(data.totalPaid).toLocaleString('en-IN')}</h2>
            <div className="text-muted small">Verified receipts</div>
          </div>
        </div>

        <div className="col-12 col-md-4">
          <div className="stat-card">
            <div className="text-muted small fw-semibold text-uppercase">{t('fees.balance')}</div>
            <h2 className={`fw-bold mb-1 ${Number(data.totalBalance) > 0 ? 'text-danger' : 'text-success'}`}>
              ₹{Number(data.totalBalance).toLocaleString('en-IN')}
            </h2>
            <div className="small">
              {Number(data.totalBalance) > 0 ? (
                <span className="text-danger fw-semibold">Installment Pending</span>
              ) : (
                <span className="text-success fw-semibold">No Dues Pending</span>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* Fee Table */}
      <div className="card shadow-sm border-0">
        <div className="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
          <h6 className="fw-bold mb-0 d-flex align-items-center gap-2">
            <CreditCard size={18} className="text-primary" /> Fee Installment Breakdown
          </h6>
          <span className="badge bg-light text-muted border">Status: {data.overallStatus}</span>
        </div>
        <div className="card-body p-0">
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light small">
                <tr>
                  <th>Category</th>
                  <th>Year</th>
                  <th className="text-end">Total Amount</th>
                  <th className="text-end">Paid Amount</th>
                  <th className="text-end">Balance</th>
                  <th className="text-center">Receipt Details</th>
                  <th className="text-center">Status & Action</th>
                </tr>
              </thead>
              <tbody>
                {data.records.map((f) => (
                  <tr key={f.id}>
                    <td className="fw-bold text-navy">{f.feeCategory}</td>
                    <td className="text-muted">{f.academicYear}</td>
                    <td className="text-end">₹{Number(f.totalAmount).toLocaleString('en-IN')}</td>
                    <td className="text-end text-success fw-semibold">₹{Number(f.paidAmount).toLocaleString('en-IN')}</td>
                    <td className="text-end text-danger fw-bold">₹{Number(f.balanceAmount).toLocaleString('en-IN')}</td>
                    <td className="text-center small">
                      {f.receiptNo ? (
                        <div>
                          <div className="fw-bold text-dark">{f.receiptNo}</div>
                          <div className="text-muted">{f.paymentDate} • {f.paymentMode}</div>
                        </div>
                      ) : (
                        <span className="text-muted">Pending Payment</span>
                      )}
                    </td>
                    <td className="text-center">
                      {f.paymentStatus === 'Paid' ? (
                        <div className="d-flex flex-column align-items-center gap-1">
                          <span className="badge bg-success-subtle text-success border border-success d-inline-flex align-items-center gap-1">
                            <CheckCircle2 size={12} /> Paid
                          </span>
                          <a
                            href={`/api/v1/parent/fees/receipt/1.pdf`}
                            target="_blank"
                            rel="noreferrer"
                            className="btn btn-outline-secondary btn-sm py-0 px-2 d-inline-flex align-items-center gap-1"
                            style={{ fontSize: '0.72rem' }}
                          >
                            <Download size={11} /> PDF Receipt
                          </a>
                        </div>
                      ) : (
                        <div className="d-flex flex-column align-items-center gap-1">
                          <span className="badge bg-danger-subtle text-danger border border-danger d-inline-flex align-items-center gap-1">
                            <AlertCircle size={12} /> Pending
                          </span>
                          <button
                            onClick={() => openPayModal(f)}
                            className="btn btn-primary btn-sm py-0 px-2 fw-semibold"
                            style={{ fontSize: '0.75rem' }}
                          >
                            Pay Online
                          </button>
                        </div>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Sandbox Payment Gateway Modal */}
      <Modal show={payModalOpen} onHide={() => setPayModalOpen(false)} centered>
        <Modal.Header closeButton>
          <Modal.Title className="fs-6 fw-bold text-navy d-flex align-items-center gap-2">
            <ShieldCheck size={20} className="text-success" />
            Sandbox Digital Payment Gateway
          </Modal.Title>
        </Modal.Header>
        <Modal.Body className="p-4">
          {paySuccessReceipt ? (
            <div className="text-center py-3">
              <CheckCircle2 size={48} className="text-success mb-2" />
              <h5 className="fw-bold text-success mb-1">Payment Successful!</h5>
              <p className="text-muted small mb-3">
                Transaction processed instantly in institutional sandbox ledger.
              </p>
              <div className="p-3 bg-light rounded border text-start mb-3" style={{ fontSize: '0.85rem' }}>
                <div><strong>Receipt Number:</strong> {paySuccessReceipt}</div>
                <div><strong>Amount Cleared:</strong> ₹{payAmount.toLocaleString('en-IN')}</div>
                <div><strong>Payment Mode:</strong> {payMode}</div>
                <div><strong>Status:</strong> CLEARED (Demo Instant Settlement)</div>
              </div>

              <div className="d-flex gap-2">
                <a
                  href={`/api/v1/parent/fees/receipt/1.pdf`}
                  target="_blank"
                  rel="noreferrer"
                  className="btn btn-outline-primary w-100 d-flex justify-content-center align-items-center gap-1"
                >
                  <Download size={15} /> Download PDF Receipt
                </a>
                <button className="btn btn-secondary w-100" onClick={() => setPayModalOpen(false)}>
                  Done
                </button>
              </div>
            </div>
          ) : (
            <form onSubmit={handlePaySubmit}>
              <div className="mb-3">
                <label className="form-label small fw-semibold">Fee Category</label>
                <input
                  type="text"
                  className="form-control"
                  value={selectedFee?.feeCategory || ''}
                  disabled
                />
              </div>

              <div className="mb-3">
                <label className="form-label small fw-semibold">Payment Amount (INR)</label>
                <input
                  type="number"
                  className="form-control fw-bold fs-5 text-primary"
                  value={payAmount}
                  onChange={(e) => setPayAmount(Number(e.target.value))}
                  max={selectedFee?.balanceAmount}
                  min={100}
                  required
                />
                <div className="form-text small text-muted">
                  Outstanding Balance: ₹{selectedFee?.balanceAmount?.toLocaleString('en-IN')}
                </div>
              </div>

              <div className="mb-4">
                <label className="form-label small fw-semibold">Payment Mode</label>
                <div className="d-flex flex-column gap-2">
                  <label className={`card p-2 border cursor-pointer ${payMode === 'UPI' ? 'border-primary bg-primary-subtle' : ''}`}>
                    <div className="d-flex align-items-center gap-2">
                      <input
                        type="radio"
                        name="payMode"
                        value="UPI"
                        checked={payMode === 'UPI'}
                        onChange={() => setPayMode('UPI')}
                      />
                      <Smartphone size={16} className="text-primary" />
                      <span className="small fw-semibold">UPI / QR (PhonePe, GooglePay, Paytm)</span>
                    </div>
                  </label>

                  <label className={`card p-2 border cursor-pointer ${payMode === 'NETBANKING' ? 'border-primary bg-primary-subtle' : ''}`}>
                    <div className="d-flex align-items-center gap-2">
                      <input
                        type="radio"
                        name="payMode"
                        value="NETBANKING"
                        checked={payMode === 'NETBANKING'}
                        onChange={() => setPayMode('NETBANKING')}
                      />
                      <Building size={16} className="text-success" />
                      <span className="small fw-semibold">NetBanking (SBI, HDFC, ICICI, Canara)</span>
                    </div>
                  </label>

                  <label className={`card p-2 border cursor-pointer ${payMode === 'CARD' ? 'border-primary bg-primary-subtle' : ''}`}>
                    <div className="d-flex align-items-center gap-2">
                      <input
                        type="radio"
                        name="payMode"
                        value="CARD"
                        checked={payMode === 'CARD'}
                        onChange={() => setPayMode('CARD')}
                      />
                      <CreditCard size={16} className="text-warning" />
                      <span className="small fw-semibold">Debit / Credit Card (Visa, RuPay, MasterCard)</span>
                    </div>
                  </label>
                </div>
              </div>

              <button
                type="submit"
                className="btn btn-primary w-100 py-2 fw-bold d-flex justify-content-center align-items-center gap-2"
                disabled={processing}
              >
                <ShieldCheck size={18} />
                <span>{processing ? 'Authorizing Sandbox Gateway...' : `Authorize Payment ₹${payAmount.toLocaleString('en-IN')}`}</span>
              </button>
            </form>
          )}
        </Modal.Body>
      </Modal>
    </div>
  );
};
