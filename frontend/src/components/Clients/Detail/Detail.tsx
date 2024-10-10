import React, { useState } from 'react';
import MovieDetail from './MovieDetail';
import DateSelection from './DateSelection';
import SeatGrid from './SeatGrid';
import SeatLegend from './SeatLegend';
import SeatSummary from './SeatSumary';
import TrailerModal from '../Modal/ModalTrailer';
import Time from './Time';
import { useNavigate } from 'react-router-dom';

const Detail: React.FC = () => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [videoUrl, setVideoUrl] = useState('');
  const [selectedTime, setSelectedTime] = useState<string | null>(null);
  const [selectedSeats, setSelectedSeats] = useState<{ seat: string; type: string }[]>([]);
  const [totalPrice, setTotalPrice] = useState<number>(0);
  const [movieDetail, setMovieDetail] = useState({
    title: 'TRANSFORMERS MỘT-T13 (Phụ đề)',
    genre: 'Hành động, Mỹ',
    director: 'Josh Cooley',
    releaseDate: '27/09/2024'
  });

  const navigate = useNavigate();

  const openTrailerModal = (url: string) => {
    setVideoUrl(url);
    setIsModalOpen(true);
  };

  const closeTrailerModal = () => {
    setIsModalOpen(false);
    setVideoUrl('');
  };

  const handleSeatSelection = (seats: { seat: string; type: string }[], price: number) => {
    setSelectedSeats(seats);
    setTotalPrice(price);
  };


  return (
    <>
      <MovieDetail onOpenTrailer={openTrailerModal} />

      <DateSelection setSelectedTime={setSelectedTime} isTimeSelected={!!selectedTime} />

      {selectedTime && <Time selectedTime={selectedTime} />}

      {selectedTime && (
        <>
          <SeatGrid onSeatSelect={handleSeatSelection} />
          <SeatLegend />
          <SeatSummary selectedSeats={selectedSeats} totalPrice={totalPrice} />
        </>
      )}

      <TrailerModal isOpen={isModalOpen} videoUrl={videoUrl} onClose={closeTrailerModal} />
    </>
  );
};

export default Detail;
