import React from 'react';
import './AdminLayout.scss';
import Header from '../components/Header';
import Navbar from '../components/Navbar';

function AdminLayout({ children }) {
  return (
    <div className="admin-layout">
      <Header/>
      <div className="admin-content">
        <Navbar />
        <div className="admin-main-wrapper">
          <main className="admin-main-content">{children}</main>
        </div>
      </div>
    </div>
  );
}

export default AdminLayout;