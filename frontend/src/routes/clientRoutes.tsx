import React from 'react';
import { RouteObject } from 'react-router-dom';
import LayoutClient from '../layout/LayoutClient';
import HomePage from '../pages/clients/Home';
import LichChieu from '../pages/clients/LichChieu';

const clientRoutes: RouteObject[] = [
  {
    path: '/', 
    element: <LayoutClient />,
    children: [
      {
        path: '/', 
        element: <HomePage />, // Hiển thị trang Home
      },
      {
        path: '/lich-chieu',
        element: <LichChieu />, // Hiển thị trang LichChieu
         
      },
     
    ],
  },
];

export default clientRoutes;
