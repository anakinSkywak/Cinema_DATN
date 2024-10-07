import React from 'react';

interface TrailerModalProps {
  isOpen: boolean;
  videoUrl: string;
  onClose: () => void;
}

const TrailerModal: React.FC<TrailerModalProps> = ({ isOpen, videoUrl, onClose }) => {
  if (!isOpen) return null;

  const handleClickOutside = (e: React.MouseEvent) => {
    if (e.target === e.currentTarget) {
      onClose();
    }
  };

  return (
    <div
      className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
      onClick={handleClickOutside}  // Close modal when clicking outside
    >
      <div className="relative w-full max-w-6xl bg-gray-900 rounded-lg overflow-hidden">
        {/* Close button */}
        <button onClick={onClose} className="absolute top-2 right-2 text-white text-2xl">
          &times;
        </button>

        {/* YouTube Video */}
        <div className="aspect-w-16 aspect-h-9">
          <iframe
            src={videoUrl}
            title="YouTube video player"
            frameBorder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowFullScreen
            className="w-full h-[600px]"
          ></iframe>
        </div>
      </div>
    </div>
  );
};

export default TrailerModal;
