import React from 'react';

function DashboardPage() {
  return (
    <main className="dashboard-page">
      <h1>Thống kê</h1>
      <div className="dashboard-content">
        <section className="stats-overview">
          <h2>Tổng quan</h2>
        </section>
        <section className="detailed-stats">
          <h2>Chi tiết thống kê</h2>
        </section>
      </div>
    </main>
  );
}

export default DashboardPage;