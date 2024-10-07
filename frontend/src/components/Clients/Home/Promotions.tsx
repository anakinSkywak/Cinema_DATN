import React from 'react';
import khuyenMai1 from '../../../../public/images/khuyenmai/khuyenmai1.png';
import khuyenMai2 from '../../../../public/images/khuyenmai/khuyenmai2.png';
import khuyenMai3 from '../../../../public/images/khuyenmai/khuyenmai3.png';

const Promotions: React.FC = () => {
  return (
    <div className="w-full">
      <div className="flex justify-between items-center mb-5">
        <h2 className="text-xl font-extrabold">Khuyến mãi</h2>
        <a href="#" className="text-blue-500 hover:text-blue-700 text-lg">Xem tất cả</a>
      </div>

      <img
        src={khuyenMai1}
        alt="Khuyến mãi 1"
        className="w-full h-48 mb-2 transform hover:scale-105 transition duration-300"
      />
      <img
        src={khuyenMai2}
        alt="Khuyến mãi 2"
        className="w-full h-48 mb-2 transform hover:scale-105 transition duration-300"
      />
      <img
        src={khuyenMai3}
        alt="Khuyến mãi 3"
        className="w-full h-48 mb-6 transform hover:scale-105 transition duration-300"
      />
    </div>
  );
};

export default Promotions;
