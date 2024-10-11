import React from 'react';

const SeatLegend: React.FC = () => {
  return (
    <div className="flex justify-center mt-4">
      <div className="flex items-center mr-4">
        <div className="w-6 h-6 bg-gray-500 text-red-500 flex items-center justify-center rounded mr-2">
          ✖
        </div>
        <span>Đã đặt</span>
      </div>
      <div className="flex items-center mr-4">
        <div className="w-6 h-6 bg-blue-500 rounded mr-2"></div>
        <span>Ghế bạn chọn</span>
      </div>
      <div className="flex items-center mr-4">
        <div className="w-6 h-6 bg-gray-700 rounded mr-2"></div>
        <span>Ghế Thường</span>
      </div>
      <div className="flex items-center mr-4">
        <div className="w-6 h-6 bg-yellow-500 rounded mr-2"></div>
        <span>Ghế VIP</span>
      </div>
      <div className="flex items-center">
        <div className="w-6 h-6 bg-red-500 rounded mr-2"></div>
        <span>Ghế Đôi</span>
      </div>
    </div>
  );
};

export default SeatLegend;
