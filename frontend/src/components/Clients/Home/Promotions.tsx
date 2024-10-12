import React, { useState, useEffect } from 'react';
import khuyenMai1 from '../../../../public/images/khuyenmai/khuyenmai1.png';
import khuyenMai2 from '../../../../public/images/khuyenmai/khuyenmai2.png';
import khuyenMai3 from '../../../../public/images/khuyenmai/khuyenmai3.png';
import khuyenMai4 from '../../../../public/images/khuyenmai/khuyenmai4.png';
import khuyenMai5 from '../../../../public/images/khuyenmai/khuyenmai5.png';

const Promotions: React.FC = () => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const promotions = [khuyenMai1, khuyenMai2, khuyenMai3, khuyenMai4, khuyenMai5];
  const slidesCount = Math.ceil(promotions.length / 3);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % slidesCount);
    }, 2000); // Change slide every 5 seconds

    return () => clearInterval(timer);
  }, [slidesCount]);

  return (
    <div className="w-full">
      <div className="flex justify-between items-center mb-5">
        <h2 className="text-xl font-extrabold">Khuyến mãi</h2>
        <a href="#" className="text-blue-500 hover:text-blue-700 text-lg">Xem tất cả</a>
      </div>

      <div className="relative overflow-hidden">
        <div 
          className="flex transition-transform duration-300 ease-in-out" 
          style={{ transform: `translateX(-${currentSlide * 100}%)` }}
        >
          {[...Array(slidesCount)].map((_, slideIndex) => (
            <div key={slideIndex} className="flex-shrink-0 w-full">
              <div className="flex flex-col space-y-4">
                {[0, 1, 2].map((index) => {
                  const imageIndex = slideIndex * 3 + index;
                  if (imageIndex < promotions.length) {
                    return (
                      <div key={index} className="w-full h-40"> {/* Adjust height as needed */}
                        <img
                          src={promotions[imageIndex]}
                          alt={`Khuyến mãi ${imageIndex + 1}`}
                          className="w-full h-full object-cover transform hover:scale-105 transition duration-300"
                        />
                      </div>
                    );
                  }
                  return null;
                })}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default Promotions;