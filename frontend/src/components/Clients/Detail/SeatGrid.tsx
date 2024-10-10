import React, { useState, useEffect } from 'react';

interface SeatGridProps {
  onSeatSelect: (seats: { seat: string; type: string }[], price: number) => void;
}

const SeatGrid: React.FC<SeatGridProps> = ({ onSeatSelect }) => {
  const rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
  const seatPrices = { vip: 150000, double: 120000, regular: 100000 };
  const bookedSeats = ['A5', 'B10', 'D12', 'F9', 'G8', 'L2'];
  const [selectedSeats, setSelectedSeats] = useState<{ seat: string; type: string; price: number }[]>([]);
  const [totalPrice, setTotalPrice] = useState<number>(0);
  const [notification, setNotification] = useState<string | null>(null);

  const handleSeatClick = (seatNumber: string, price: number, type: string) => {
    if (bookedSeats.includes(seatNumber)) {
      setNotification(`Ghế ${seatNumber} đã được đặt. Vui lòng chọn ghế khác.`);
      return;
    }

    setSelectedSeats((prevSeats) => {
      const seatExists = prevSeats.find(seat => seat.seat === seatNumber);

      if (seatExists) {
        const updatedSeats = prevSeats.filter((seat) => seat.seat !== seatNumber);
        setTotalPrice((prevTotal) => prevTotal - price);
        setNotification(`Bỏ chọn ghế ${seatNumber} (${type}).`);
        return updatedSeats;
      } else {
        setTotalPrice((prevTotal) => prevTotal + price);
        setNotification(`Bạn đang chọn ghế ${seatNumber} (${type}).`);
        return [...prevSeats, { seat: seatNumber, type, price }];
      }
    });
  };

  const isSelected = (seatNumber: string) => selectedSeats.some((seat) => seat.seat === seatNumber);

  useEffect(() => {
    onSeatSelect(selectedSeats.map(seat => ({ seat: seat.seat, type: seat.type })), totalPrice);
  }, [selectedSeats, totalPrice, onSeatSelect]);

  useEffect(() => {
    if (notification) {
      const timer = setTimeout(() => setNotification(null), 1000); 
      return () => clearTimeout(timer);
    }
  }, [notification]);

  return (
    <>
      {notification && (
        <div className="fixed top-4 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white py-2 px-4 rounded-lg z-50">
          {notification}
        </div>
      )}

      <div className="flex flex-wrap justify-center max-w-4xl mx-auto">
        <img src="../../../public/images/screen.webp" alt="Screen" className="mb-0 w-full" />
        {rows.map((row) => (
          <div key={row} className="flex flex-wrap justify-center w-full">
            {Array.from({ length: row === 'L' ? 16 : 18 }).map((_, index) => {
              const seatNumber = `${row}${(row === 'L' ? 15 : 17) - index}`;
              const isBooked = bookedSeats.includes(seatNumber);
              const isVip = row === 'D' && [16, 15, 12, 11, 9, 8, 5, 4].includes(index);
              const isDouble = row === 'L';
              const seatPrice = isVip ? seatPrices.vip : isDouble ? seatPrices.double : seatPrices.regular;
              const seatType = isVip ? 'Ghế VIP' : isDouble ? 'Ghế đôi' : 'Ghế thường';

              return (
                <div
                  key={index}
                  className={`w-10 h-10 m-1 rounded-lg text-white flex items-center justify-center cursor-pointer ${
                    isBooked
                      ? 'bg-gray-500 text-red-500 line-through' 
                      : isSelected(seatNumber)
                      ? 'bg-blue-500' 
                      : isVip
                      ? 'bg-yellow-500' 
                      : isDouble
                      ? 'bg-red-500' 
                      : 'bg-gray-700'
                  }`}
                  onClick={() => handleSeatClick(seatNumber, seatPrice, seatType)}
                >
                  {isBooked ? '✖' : seatNumber}
                </div>
              );
            })}
          </div>
        ))}
      </div>
    </>
  );
};

export default SeatGrid;
