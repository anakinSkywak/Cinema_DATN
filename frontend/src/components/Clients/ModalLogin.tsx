import React from 'react';
import { Link } from 'react-router-dom';

interface ModalLoginProps {
  onClose: () => void;
  switchToSignUp: () => void;
}

const ModalLogin: React.FC<ModalLoginProps> = ({ onClose, switchToSignUp }) => {
  return (
    <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
      <div className="bg-gray-900 p-10 rounded-xl max-w-lg mx-auto text-white w-full">
        <div className="text-left">
          <button onClick={onClose} className="text-white text-2xl font-bold float-right text-red-500">
            &times;
          </button>
          <h3 className="text-xl font-bold mb-6">ĐĂNG NHẬP</h3>
        </div>
        <form>
          <label className="block mb-2 font-bold">Tên đăng nhập hoặc Email</label>
          <input
            type="text"
            placeholder="Tên đăng nhập hoặc Email"
            required
            className="w-full p-3 mb-6 rounded-xl bg-gray-800 text-white focus:outline-none"
          />

          <label className="block mb-2 font-bold">Mật khẩu</label>
          <input
            type="password"
            placeholder="Mật khẩu"
            required
            className="w-full p-3 mb-4 rounded-xl bg-gray-800 text-white focus:outline-none"
          />

          <div className="flex justify-between items-center mb-6">
            <Link to="/reset-password" className="text-red-500 hover:text-red-700">
              Quên mật khẩu?
            </Link>
          </div>

          <button
            type="submit"
            className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl focus:outline-none focus:shadow-outline"
          >
            Đăng nhập
          </button>
          <p className="text-center mt-4">
            Bạn chưa có tài khoản?{' '}
            <span onClick={switchToSignUp} className="cursor-pointer text-red-500 hover:underline">
              Đăng ký
            </span>
          </p>
        </form>
      </div>
    </div>
  );
};

export default ModalLogin;
