import React from 'react';
import { useRoutes } from 'react-router-dom';
import routes from './src/routes';
import './index.css'; 
import './App.css'

const App: React.FC = () => {
  const routing = useRoutes(routes);

  return (
    <div>
      {routing}
    </div>
  );
};

export default App;
