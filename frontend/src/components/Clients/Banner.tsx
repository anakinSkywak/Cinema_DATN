import React, { useState, useEffect } from 'react';
import bannerImage1 from '../../../public/images/banner.png';
import bannerImage2 from '../../../public/images/banner2.png';
import bannerImage3 from '../../../public/images/banner.png';

const Banner: React.FC = () => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const images = [bannerImage1, bannerImage2, bannerImage3];

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentSlide((prevSlide) => (prevSlide + 1) % images.length);
    }, 5000);

    return () => clearInterval(timer);
  }, []);

  return (
    <div className="banner">
      {images.map((image, index) => (
        <img
          key={index}
          src={image}
          alt={`Banner Image ${index + 1}`}
          style={{
            width: '100%',
            display: index === currentSlide ? 'block' : 'none',
            transition: 'opacity 0.5s ease-in-out',
          }}
        />
      ))}
    </div>
  );
}

export default Banner;