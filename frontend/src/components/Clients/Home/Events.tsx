import React, { useState, useEffect } from 'react';
import suKien1 from '../../../../public/images/sukien/sukien1.png';
import suKien2 from '../../../../public/images/sukien/sukien2.png';
import suKien3 from '../../../../public/images/sukien/sukien3.png';
import suKien4 from '../../../../public/images/sukien/sukien4.png';
import suKien5 from '../../../../public/images/sukien/sukien5.png';

const Events: React.FC = () => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const events = [suKien1, suKien2, suKien3, suKien4, suKien5];
  const slidesCount = Math.ceil(events.length / 3);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % slidesCount);
    }, 5000); // Change slide every 5 seconds

    return () => clearInterval(timer);
  }, [slidesCount]);

  return (
    <div className="w-full">
      <div className="flex justify-between items-center mb-5 mt-60">
        <h2 className="text-xl font-extrabold">Sự kiện</h2>
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
                  if (imageIndex < events.length) {
                    return (
                      <div key={index} className="w-full h-48"> {/* Adjusted height to match original */}
                        <img
                          src={events[imageIndex]}
                          alt={`Sự kiện ${imageIndex + 1}`}
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

export default Events;