import React from 'react';
import movie1Image from '../../../../public/images/movies/movie1.png';
import { Link } from 'react-router-dom';

const NowShowingMovies: React.FC = () => {
  return (
    <>
      <div className="flex justify-between items-center mb-4">
        <h2 className="text-xl font-extrabold">
          <span className="live-dot"></span>Phim đang chiếu
        </h2>
        <Link to="#" className="text-blue-500 hover:text-blue-700 text-lg">Xem tất cả</Link>
      </div>

      <div className="grid grid-cols-4 gap-10">
        {[...Array(8)].map((_, index) => (
          <div key={index} className="movie-card rounded overflow-hidden shadow-lg">
            <Link to="./chi-tiet" className="movie-img-link">
              <img src={movie1Image} alt="Movie Title" className="movie-img rounded hover:shadow-xl" />
            </Link>
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

export default NowShowingMovies;
