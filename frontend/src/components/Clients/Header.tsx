import React, { useState } from 'react';
import NavBar from './NavBar';
import ModalSignUp from './ModalSignUp';
import ModalLogin from './ModalLogin';

const Header: React.FC = () => {
  const [showSignUp, setShowSignUp] = useState(false);
  const [showLogin, setShowLogin] = useState(false);

  const openSignUpModal = () => setShowSignUp(true);
  const closeSignUpModal = () => setShowSignUp(false);

  const openLoginModal = () => setShowLogin(true);
  const closeLoginModal = () => setShowLogin(false);

  const switchToLogin = () => {
    closeSignUpModal();
    openLoginModal();
  };

  const switchToSignUp = () => {
    closeLoginModal();
    openSignUpModal();
  };

  return (
    <>
      <NavBar onOpenSignUp={openSignUpModal} onOpenLogin={openLoginModal} />

      {showSignUp && <ModalSignUp onClose={closeSignUpModal} switchToLogin={switchToLogin} />}
      {showLogin && <ModalLogin onClose={closeLoginModal} switchToSignUp={switchToSignUp} />}
    </>
  );
};

export default Header;
