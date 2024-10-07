import React from 'react';

interface ModalSignUpProps {
  onClose: () => void;
  switchToLogin: () => void;
}

const ModalSignUp: React.FC<ModalSignUpProps> = ({ onClose, switchToLogin }) => {
  return (
    <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
      <div className="bg-gray-900 p-10 rounded-xl max-w-lg mx-auto text-white w-full">
        <div className="text-left">
          <button onClick={onClose} className="text-white text-2xl font-bold float-right text-red-500">
            &times;
          </button>
          <h3 className="text-xl font-bold mb-6">ĐĂNG KÝ</h3>
        </div>
        <form>
          <div className="flex gap-6 mb-6">
            <div className="flex-1">
              <label className="block mb-2 font-bold">Họ</label>
              <input
                type="text"
                placeholder="Họ"
                required
                className="w-full p-3 rounded-xl bg-gray-800 text-white focus:outline-none"
              />
            </div>
            <div className="flex-1">
              <label className="block mb-2 font-bold">Tên</label>
              <input
                type="text"
                placeholder="Tên"
                required
                className="w-full p-3 rounded-xl bg-gray-800 text-white focus:outline-none"
              />
            </div>
          </div>

          <label className="block mb-2 font-bold">Tên tài khoản</label>
          <input
            type="text"
            placeholder="Tên tài khoản"
            required
            className="w-full p-3 mb-6 rounded-xl bg-gray-800 text-white focus:outline-none"
          />

          <div className="flex gap-6 mb-6">
            <div className="flex-1">
              <label className="block mb-2 font-bold">Số điện thoại</label>
              <input
                type="text"
                placeholder="Số điện thoại"
                className="w-full p-3 rounded-xl bg-gray-800 text-white focus:outline-none"
              />
            </div>
            <div className="flex-1">
              <label className="block mb-2 font-bold">Email</label>
              <input
                type="email"
                placeholder="Email"
                required
                className="w-full p-3 rounded-xl bg-gray-800 text-white focus:outline-none"
              />
            </div>
          </div>

          <div className="flex gap-6 mb-6">
            <div className="flex-1">
              <label className="block mb-2 font-bold">Mật khẩu</label>
              <input
                type="password"
                placeholder="Mật khẩu"
                required
                className="w-full p-3 rounded-xl bg-gray-800 text-white focus:outline-none"
              />
            </div>
            <div className="flex-1">
              <label className="block mb-2 font-bold">Xác nhận mật khẩu</label>
              <input
                type="password"
                placeholder="Xác nhận mật khẩu"
                required
                className="w-full p-3 rounded-xl bg-gray-800 text-white focus:outline-none"
              />
            </div>
          </div>

          <button
            type="submit"
            className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl focus:outline-none focus:shadow-outline"
          >
            Đăng ký
          </button>
          <p className="text-center mt-4">
            Bạn đã có tài khoản?{' '}
            <span onClick={switchToLogin} className="cursor-pointer text-red-500 hover:underline">
              Đăng nhập
            </span>
          </p>
        </form>
      </div>
    </div>
  );
};

export default ModalSignUp;
