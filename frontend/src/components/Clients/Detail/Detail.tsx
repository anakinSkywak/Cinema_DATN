import React, { useState } from 'react';
import MovieDetail from './MovieDetail';
import DateSelection from './DateSelection';
import SeatGrid from './SeatGrid';
import SeatLegend from './SeatLegend';
import SeatSummary from './SeatSumary';
import TrailerModal from './ModalTrailer';
import Time from './Time';

const Detail: React.FC = () => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [videoUrl, setVideoUrl] = useState('');
  const [selectedTime, setSelectedTime] = useState<string | null>(null); // Track selected time

  const openTrailerModal = (url: string) => {
    setVideoUrl(url);
    setIsModalOpen(true);
  };

  const closeTrailerModal = () => {
    setIsModalOpen(false);
    setVideoUrl('');
  };

  const handlePageReload = () => {
    window.location.reload(); 
  };

  return (
    <>
      <MovieDetail onOpenTrailer={openTrailerModal} />

      <DateSelection setSelectedTime={setSelectedTime} isTimeSelected={!!selectedTime} />

      {selectedTime && <Time selectedTime={selectedTime} />}

      {selectedTime && (
        <>
          <SeatGrid />
          <SeatLegend />
          <SeatSummary />

          <div className="text-center mt-4">
            <button onClick={handlePageReload} className="bg-blue-500 text-white px-4 py-2 rounded-full">
              Chọn lại
            </button>
          </div>
        </>
      )}

      <TrailerModal isOpen={isModalOpen} videoUrl={videoUrl} onClose={closeTrailerModal} />
    </>
  );
};

export default Detail;
