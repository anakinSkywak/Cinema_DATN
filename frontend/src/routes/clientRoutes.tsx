import React from 'react';
import { RouteObject } from 'react-router-dom';
import LayoutClient from '../layout/LayoutClient';
import HomePage from '../pages/clients/Home';
import LichChieu from '../pages/clients/LichChieu';
import ChiTiet from '../pages/clients/ChiTiet';

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
      {
        path: '/chi-tiet',
        element: <ChiTiet />, // Hiển thị trang ChiTiet
         
      },
     
    ],
  },
];

export default clientRoutes;
