import React from 'react';
import movie1Image from '../../../../public/images/movies/movie1.png';

const UpcomingMovies: React.FC = () => {
  return (
    <>
      <div className="flex justify-between items-center mb-4">
        <h2 className="text-xl font-extrabold">
          <span className="live-dot"></span>Phim sắp chiếu
        </h2>
        <a href="#" className="text-blue-500 hover:text-blue-700 text-lg">Xem tất cả</a>
      </div>

      <div className="grid grid-cols-4 gap-10">
        {[...Array(8)].map((_, index) => (
          <div key={index} className="movie-card rounded overflow-hidden shadow-lg">
            <img src={movie1Image} alt="Movie Title" className="movie-img rounded hover:shadow-xl" />
            <div className="p-2">
              <div className="flex justify-between text-sm">
                <span>Genre: Action</span>
                <span>27/09/2024</span>
              </div>
              <h3 className="text-lg font-bold mt-1">Movie Title</h3>
            </div>
          </div>
        ))}
      </div>
    </>
  );
};

export default UpcomingMovies;
