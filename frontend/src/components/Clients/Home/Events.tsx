import React from 'react';
import suKien1 from '../../../../public/images/sukien/sukien1.png';
import suKien2 from '../../../../public/images/sukien/sukien2.png';
import suKien3 from '../../../../public/images/sukien/sukien3.png';

const Events: React.FC = () => {
  return (
    <div className="w-full">
      <div className="flex justify-between items-center mb-5 mt-60">
        <h2 className="text-xl font-extrabold">Sự kiện</h2>
        <a href="#" className="text-blue-500 hover:text-blue-700 text-lg">Xem tất cả</a>
      </div>

      <img
        src={suKien1}
        alt="Sự kiện 1"
        className="w-full h-48 mb-2 transform hover:scale-105 transition duration-300"
      />
      <img
        src={suKien2}
        alt="Sự kiện 2"
        className="w-full h-48 mb-2 transform hover:scale-105 transition duration-300"
      />
      <img
        src={suKien3}
        alt="Sự kiện 3"
        className="w-full h-48 transform hover:scale-105 transition duration-300"
      />
    </div>
  );
};

export default Events;
