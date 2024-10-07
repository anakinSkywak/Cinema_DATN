import React from 'react';
import { RouteObject } from 'react-router-dom';
import Dashboard from '../pages/admin/Dashboard';
// import Settings from '../pages/admin/Settings';

const adminRoutes: RouteObject[] = [
  {
    path: '/admin/dashboard',
    element: <Dashboard />,
  },
  {
    path: '/admin/settings',
    // element: <Settings />,
  },
];

export default adminRoutes;
