import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';

interface SeatSummaryProps {
  selectedSeats: { seat: string; type: string }[];
  totalPrice: number;
}

const SeatSummary: React.FC<SeatSummaryProps> = ({ selectedSeats, totalPrice }) => {
  const navigate = useNavigate();

  // Ensure seat type is properly defined and avoid implicit any error
  const groupedSeats = selectedSeats.reduce((acc: Record<string, string[]>, seat: { seat: string, type: string }) => {
    if (!acc[seat.type]) {
      acc[seat.type] = [];
    }
    acc[seat.type].push(seat.seat);
    return acc;
  }, {} as Record<string, string[]>);

  const handleCheckout = () => {
    // Navigate to the checkout page
    navigate('/check-out', {
      state: {
        selectedSeats,
        totalPrice,
      },
    });
  };

  return (
    <div className="flex justify-between items-center p-4 max-w-screen-lg mx-auto">
      <div>
        <p className="text-lg">
          Ghế đã chọn:{' '}
          {Object.entries(groupedSeats).length > 0
            ? Object.entries(groupedSeats)
                .map(([type, seats]) => `${seats.join(', ')} (${type})`)
                .join('; ')
            : 'Chưa chọn ghế nào'}
        </p>
        <p className="text-lg">
          Tổng tiền: <span className="font-bold">{totalPrice.toLocaleString()}đ</span>
        </p>
      </div>
      <div className="flex space-x-4">
        <button className="px-4 py-2 border border-gray-500 rounded-full text-white hover:bg-gray-800">
          Quay lại
        </button>
        <button
          onClick={handleCheckout}
          className="px-4 py-2 bg-red-600 rounded-full text-gray-300 hover:bg-red-700"
        >
          Thanh toán
        </button>
      </div>
    </div>
  );
};

export default SeatSummary;
