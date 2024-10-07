import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

interface TimeProps {
  selectedTime: string;
}

const Time: React.FC<TimeProps> = ({ selectedTime }) => {
  const [time, setTime] = useState(600); // Countdown starts from 600 seconds (10 minutes)
  const navigate = useNavigate(); 

  useEffect(() => {
    if (time > 0) {
      const timer = setInterval(() => {
        setTime((prevTime) => prevTime - 1);
      }, 1000);

      return () => clearInterval(timer); // Cleanup interval on component unmount
    } else {
      navigate('/'); // Redirect to home when the timer hits 0
    }
  }, [time, navigate]);

  const formatTime = (seconds: number) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
  };

  return (
    <div className="flex justify-between items-center p-4 max-w-screen-lg mx-auto">
  <div className="flex items-center">
    <p className="mr-4 text-lg">
      Giờ chiếu: <span className="font-bold">{selectedTime}</span>
    </p>
  </div>
  <div className="flex space-x-4">
    <button id="timer" className="bg-red-600 text-white px-4 py-2 rounded-full">
      Thời gian chọn ghế: {formatTime(time)}
    </button>
  </div>
</div>

  );
};

export default Time;
