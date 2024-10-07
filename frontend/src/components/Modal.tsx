import React from 'react';

interface ModalProps {
  show: boolean;
  onClose: () => void;
  title?: string;
  children: React.ReactNode;
}

const Modal: React.FC<ModalProps> = ({ show, onClose, title, children }) => {
  if (!show) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
      <div className="bg-gray-900 p-10 rounded-xl max-w-lg mx-auto text-white w-full relative">
        <button onClick={onClose} className="absolute top-4 right-4 text-white text-2xl font-bold text-red-500">
          &times;
        </button>
        {title && <h3 className="text-xl font-bold mb-6">{title}</h3>}
        <div>{children}</div>
      </div>
    </div>
  );
};

export default Modal;
