import React, { useState, useRef, useEffect } from 'react';
import { Link } from 'react-router-dom';
import {
  IconFilter,
  IconSearch,
  IconBell,
  IconChevronDown
} from '@tabler/icons-react';
import './Header.scss';

function Header() {
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);
  const [isLanguageMenuOpen, setIsLanguageMenuOpen] = useState(false);
  const userMenuRef = useRef(null);
  const languageMenuRef = useRef(null);

  const toggleUserMenu = () => setIsUserMenuOpen(!isUserMenuOpen);
  const toggleLanguageMenu = () => setIsLanguageMenuOpen(!isLanguageMenuOpen);

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (userMenuRef.current && !userMenuRef.current.contains(event.target)) {
        setIsUserMenuOpen(false);
      }
      if (languageMenuRef.current && !languageMenuRef.current.contains(event.target)) {
        setIsLanguageMenuOpen(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  return (
    <header className="main-header fixed">
      <div className="header-content">
        <Link to="/" className="logo">
          <img src="https://chieuphimquocgia.com.vn/_next/image?url=%2Fimages%2Flogo.png&w=96&q=75" alt="NCC Logo" />
        </Link>
        <div className="header-controls">
          <button className="filter-button">
            <IconFilter size={20} />
            <span>Filter</span>
          </button>
          <div className="search-bar">
            <IconSearch size={20} className="search-icon" />
            <input type="text" placeholder="Search" />
          </div>
          <div className="header-actions">
            <div className="notification-badge">
              <IconBell size={20} />
              <span className="badge">3</span>
            </div>
            <div className="dropdown" ref={languageMenuRef}>
              <button className="language-selector" onClick={toggleLanguageMenu}>
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/21/Flag_of_Vietnam.svg" alt="Vietnamese flag" />
                <span>Vietnamese</span>
                <IconChevronDown size={16} />
              </button>
              {isLanguageMenuOpen && (
                <div className="dropdown-menu">
                  <a href="#" className="dropdown-item">Tiếng Việt</a>
                  <a href="#" className="dropdown-item">English</a>
                </div>
              )}
            </div>
            <div className="dropdown" ref={userMenuRef}>
              <button className="user-menu" onClick={toggleUserMenu}>
                <img src="https://i.pravatar.cc/300" alt="User avatar" className="user-avatar" />
                <span>Bằng</span>
                <IconChevronDown size={16} />
              </button>
              {isUserMenuOpen && (
                <div className="dropdown-menu">
                  <a href="#" className="dropdown-item">Profile</a>
                  <a href="#" className="dropdown-item">Settings</a>
                  <div className="dropdown-divider"></div>
                  <a href="#" className="dropdown-item">Logout</a>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}

export default Header;