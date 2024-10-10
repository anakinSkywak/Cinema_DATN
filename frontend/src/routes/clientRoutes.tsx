import React from 'react';
import { RouteObject } from 'react-router-dom';
import LayoutClient from '../layout/LayoutClient';
import HomePage from '../pages/clients/Home';
import LichChieu from '../pages/clients/LichChieu';
import ChiTiet from '../pages/clients/ChiTiet';
import CheckOut from '../pages/clients/CheckOut';
import Profile from '../components/Clients/Profile/Profile';
import HistoryPoints from '../components/Clients/Profile/HistoryPoint';
import TicketHistory from '../components/Clients/Profile/TicketHistory';

const clientRoutes: RouteObject[] = [
  {
    path: '/',
    element: <LayoutClient />,
    children: [
      { path: '/', element: <HomePage /> },
      { path: '/check-out', element: <CheckOut /> },
      { path: '/chi-tiet', element: <ChiTiet /> },
      { path: '/profile', element: <Profile /> },
      { path: '/history-point', element: <HistoryPoints /> },
      { path: '/history-ticket', element: <TicketHistory /> },
    ],
  },
];

export default clientRoutes;
