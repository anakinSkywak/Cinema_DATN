import React from 'react';
import { Link } from 'react-router-dom';

interface NavBarProps {
  onOpenSignUp: () => void;
  onOpenLogin: () => void;
}

const NavBar: React.FC<NavBarProps> = ({ onOpenSignUp, onOpenLogin }) => {
  return (
    <nav className="bg-gray-800 p-4 px-10 fixed top-0 w-full z-50">
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

        <div className="flex items-center">
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
        </div>
      </div>
    </nav>
  );
};

export default NavBar;
