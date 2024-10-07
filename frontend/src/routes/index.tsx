import adminRoutes from './adminRoutes';
import clientRoutes from './clientRoutes';
import { RouteObject } from 'react-router-dom';

const routes: RouteObject[] = [
  ...adminRoutes,
  ...clientRoutes,
];

export default routes;
