import React from 'react';

const SeatGrid: React.FC = () => {
  const rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
  const bookedSeats = ['A5', 'B10', 'D12', 'F9', 'G8'];
  return (
    <div className="flex flex-wrap justify-center max-w-4xl mx-auto">
      <img src="../../../public/images/screen.webp" alt="Screen" className="mb-4 w-full" />
      {rows.map((row) => (
        <div key={row} className="flex flex-wrap justify-center mb-2 w-full">
          {Array.from({ length: row === 'K' ? 16 : 18 }).map((_, index) => {
            const seatNumber = `${row}${(row === 'K' ? 15 : 17) - index}`;
            const isBooked = bookedSeats.includes(seatNumber);

            return (
              <div
                key={index}
                className={`w-10 h-10 m-1 rounded-lg text-white flex items-center justify-center ${
                  isBooked
                    ? 'bg-gray-500 text-red-500 line-through'  // Booked seat with X and color
                    : row === 'D' && [16, 15, 12, 11, 9, 8, 5, 4].includes(index)
                    ? 'bg-yellow-500'  // VIP seats
                    : row === 'K'
                    ? 'bg-red-500'  // Double seats
                    : 'bg-gray-700'  // Available seats
                }`}
              >
                {isBooked ? '✖' : seatNumber}
              </div>
            );
          })}
        </div>
      ))}
    </div>
  );
};

export default SeatGrid;
