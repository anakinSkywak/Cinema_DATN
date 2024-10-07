import React from 'react';
import NowShowingMovies from './NowShowingMovies';
import UpcomingMovies from './UpcomingMovies';
import Promotions from './Promotions';
import Events from './Events';

const HomePage: React.FC = () => {
  return (
    <main className="container mx-auto px-10 py-6">
      <div className="flex">
        <div className="flex-4 mr-4">
          <NowShowingMovies />

          <hr className="my-8 border-gray-700" />
          <UpcomingMovies />
        </div>

        <div className="w-1/5 ml-12">
          <Promotions />
          <Events />
        </div>
      </div>
    </main>
  );
};

export default HomePage;
