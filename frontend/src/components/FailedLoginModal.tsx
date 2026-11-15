import React from 'react';
import { Modal, Button } from 'react-bootstrap';
import { AlertCircle } from 'lucide-react';

interface FailedLoginModalProps {
  show: boolean;
  onHide: () => void;
  message?: string;
  attemptsRemaining?: number;
}

export const FailedLoginModal: React.FC<FailedLoginModalProps> = ({
  show,
  onHide,
  message = 'You have entered an invalid username or password.',
  attemptsRemaining,
}) => {
  return (
    <Modal show={show} onHide={onHide} centered>
      <Modal.Header closeButton className="border-0 pb-0">
        <Modal.Title className="text-danger d-flex align-items-center gap-2 fs-5 fw-bold">
          <AlertCircle size={24} />
          Login Failed
        </Modal.Title>
      </Modal.Header>
      <Modal.Body className="py-3">
        <p className="text-dark mb-2">{message}</p>
        {attemptsRemaining !== undefined && (
          <div className="alert alert-warning py-2 px-3 mt-3 mb-0 d-flex justify-content-between align-items-center">
            <span className="fw-medium">Attempts remaining:</span>
            <span className={`badge ${attemptsRemaining <= 1 ? 'bg-danger' : 'bg-dark'} fs-6`}>
              {attemptsRemaining} / 5
            </span>
          </div>
        )}
      </Modal.Body>
      <Modal.Footer className="border-0 pt-0">
        <Button variant="secondary" onClick={onHide} className="px-4">
          Close
        </Button>
      </Modal.Footer>
    </Modal>
  );
};
