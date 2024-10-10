import React from 'react';
import { useRoutes } from 'react-router-dom';
import routes from './src/routes';
import './index.css'; 
import './App.css';
import ScrollToTop from './src/components/ScrollToTop';

const App: React.FC = () => {
  const routing = useRoutes(routes);

  return (
    <>
      <ScrollToTop />
      <div>
        {routing}
      </div>
    </>
  );
};

export default App;
