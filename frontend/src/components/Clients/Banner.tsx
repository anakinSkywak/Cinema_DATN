import React from 'react';
import bannerImage from '../../../public/images/banner.png'; 

const Banner: React.FC = () => {
  return (
    <div className="banner">
      <img 
        src={bannerImage} 
        alt="Banner Image" 
        style={{ width: '100%' }} 
      />
    </div>
  );
}

export default Banner;
