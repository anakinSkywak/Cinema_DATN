import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

interface NavBarProps {
  onOpenSignUp: () => void;
  onOpenLogin: () => void;
}

const NavBar: React.FC<NavBarProps> = ({ onOpenSignUp, onOpenLogin }) => {
  const [userName, setUserName] = useState<string>('Dương Văn Hòa');
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false); // State to track scroll position

  // Toggle account dropdown menu
  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  // Handle scrolling to change navbar background
  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 50) {
        setIsScrolled(true); // If scrolled more than 50px, make background lighter
      } else {
        setIsScrolled(false); // If back to top, reset background opacity
      }
    };

    window.addEventListener('scroll', handleScroll);
    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  }, []);

  const handleMouseEnter = () => {
    setIsMenuOpen(true);
  };

  const handleMouseLeave = () => {
    setIsMenuOpen(false);
  };

  return (
    <nav className={`p-4 px-10 fixed top-0 w-full z-50 transition duration-300 ease-in-out ${isScrolled ? 'bg-gray-900/70' : 'bg-gray-800'}`}>
      <div className="container mx-auto flex justify-between items-center">
        <div className="flex items-center">
          <img src="../../../public/images/logo.png" alt="Logo" className="h-8 mr-6" />

          {/* Navigation links */}
          <Link to="/" className="text-red-600 hover:text-white px-5 py-2 rounded-md text-sm font-medium">
            Trang Chủ
          </Link>
          <Link to="/lich-chieu" className="text-gray-300 hover:text-white px-5 py-2 rounded-md text-sm font-medium">
            Lịch Chiếu
          </Link>
          <Link to="/tin-tuc" className="text-gray-300 hover:text-white px-5 py-2 rounded-md text-sm font-medium">
            Tin Tức
          </Link>
          <Link to="/khuyen-mai" className="text-gray-300 hover:text-white px-5 py-2 rounded-md text-sm font-medium">
            Khuyến Mãi
          </Link>
          <Link to="/gia-ve" className="text-gray-300 hover:text-white px-5 py-2 rounded-md text-sm font-medium">
            Giá Vé
          </Link>
          <Link to="/lienhoanphim" className="text-gray-300 hover:text-white px-5 py-2 rounded-md text-sm font-medium">
            Liên Hoan Phim
          </Link>
          <Link to="/gioi-thieu" className="text-gray-300 hover:text-white px-5 py-2 rounded-md text-sm font-medium">
            Giới Thiệu
          </Link>
          
        </div>

        <div className="flex items-center relative">
          {userName ? (
            <div
              className="relative"
              onMouseEnter={handleMouseEnter}
              onMouseLeave={handleMouseLeave}
            >
              <button className="text-white bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-xl text-sm font-medium">
                {userName}
              </button>
              {isMenuOpen && (
                <div className="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2 z-50">
                  <Link
                    to="/profile"
                    className="block px-4 py-2 text-gray-800 hover:bg-gray-100"
                  >
                    Thông Tin Tài Khoản
                  </Link>
                  <button
                    className="w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-100"
                    onClick={() => setUserName('')}
                  >
                    Đăng Xuất
                  </button>
                </div>
              )}
            </div>
          ) : (
            <>
              <button
                onClick={onOpenSignUp}
                className="text-white border border-white hover:bg-white hover:text-gray-800 px-3 py-2 rounded-xl text-sm font-medium mr-6"
              >
                Đăng Ký
              </button>

              <button
                onClick={onOpenLogin}
                className="text-white bg-red-500 hover:bg-white hover:text-gray-800 px-3 py-2 rounded-xl text-sm font-medium mr-6 transition duration-300 ease-in-out"
              >
                Đăng Nhập
              </button>
            </>
          )}
        </div>
      </div>
    </nav>
  );
};

export default NavBar;
