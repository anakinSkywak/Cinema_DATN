import React from 'react';
import { Outlet } from 'react-router-dom';
import Header from '../components/Clients/Header';
import Footer from '../components/Clients/Footer';

const LayoutClient: React.FC = () => {
  return (
    <div>
      <Header/>
      <Outlet />
      <Footer />
    </div>
  );
};

export default LayoutClient;
