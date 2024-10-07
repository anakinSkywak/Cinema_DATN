import React from 'react';

const SeatSummary: React.FC = () => {
  return (
    <div className="flex justify-between items-center p-4 max-w-screen-lg mx-auto">
    <div>
      <p className="text-lg">Ghế đã chọn:</p>
      <p className="text-lg">
        Tổng tiền: <span className="font-bold">0đ</span>
      </p>
    </div>
    <div className="flex space-x-4">
      <button className="px-4 py-2 border border-gray-500 rounded-full text-white hover:bg-gray-800">
        Quay lại
      </button>
      <button className="px-4 py-2 bg-red-600 rounded-full text-gray-300 hover:bg-red-700">
        <a href="checkout.html">Thanh toán</a>
      </button>
    </div>
  </div>
  );
};

export default SeatSummary;
