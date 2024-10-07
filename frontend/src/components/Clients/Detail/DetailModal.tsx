import React from 'react';

interface DetailModalProps {
  isOpen: boolean;
  content: string;
  onClose: () => void;
}

const DetailModal: React.FC<DetailModalProps> = ({ isOpen, content, onClose }) => {
  if (!isOpen) return null;

  return (
    <div
      className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
      onClick={onClose}
    >
      <div className="relative bg-white p-6 rounded-lg max-w-2xl w-full">
        <button onClick={onClose} className="absolute top-2 right-2 text-black text-2xl">
          &times;
        </button>
        <h2 className="text-2xl font-bold mb-4">Chi tiết nội dung</h2>
        <p className="text-black">{content}</p>
      </div>
    </div>
  );
};

export default DetailModal;
