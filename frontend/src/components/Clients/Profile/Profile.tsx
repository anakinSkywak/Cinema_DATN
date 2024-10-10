import React, { useState } from 'react';
import { Link } from 'react-router-dom';

const Profile: React.FC = () => {
  const [profileData, setProfileData] = useState({
    lastName: 'Dương',
    firstName: 'Văn Hòa',
    phone: '0123456789',
    address: 'Dương',
    username: 'abcdef@gmail.com',
    email: 'abcdef@gmail.com',
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setProfileData({
      ...profileData,
      [e.target.id]: e.target.value,
    });
  };

  const handleSave = () => {
    alert('Thông tin đã được lưu.');
    // Add logic to save data here.
  };

  return (
    <main className="flex flex-col items-center py-8">
      <h1 className="text-2xl mb-6">Thông tin cá nhân</h1>
      <div className="flex space-x-4 mb-6">
        <button className="bg-red-600 text-white py-2 px-4 rounded-full">
          <Link to="/profile">Tài khoản của tôi</Link>
        </button>
        <button className="bg-gray-800 text-white py-2 px-4 rounded-full">
          <Link to="/history-ticket">Lịch sử mua vé</Link>
        </button>
        <button className="bg-gray-800 text-white py-2 px-4 rounded-full">
          <Link to="/history-point">Lịch sử điểm thưởng</Link>
        </button>
      </div>
      <form className="grid grid-cols-2 gap-4 w-1/2">
        <div>
          <label className="block mb-2" htmlFor="lastName">
            Họ <span className="text-red-500">*</span>
          </label>
          <input
            className="w-full p-2 bg-black text-white rounded-full"
            id="lastName"
            type="text"
            value={profileData.lastName}
            onChange={handleChange}
          />
        </div>
        <div>
          <label className="block mb-2" htmlFor="firstName">
            Tên <span className="text-red-500">*</span>
          </label>
          <input
            className="w-full p-2 bg-black text-white rounded-full"
            id="firstName"
            type="text"
            value={profileData.firstName}
            onChange={handleChange}
          />
        </div>
        <div>
          <label className="block mb-2" htmlFor="phone">
            Số điện thoại <span className="text-red-500">*</span>
          </label>
          <input
            className="w-full p-2 bg-black text-white rounded-full"
            id="phone"
            type="text"
            value={profileData.phone}
            onChange={handleChange}
          />
        </div>
        <div>
          <label className="block mb-2" htmlFor="address">
            Địa chỉ
          </label>
          <input
            className="w-full p-2 bg-black text-white rounded-full"
            id="address"
            type="text"
            value={profileData.address}
            onChange={handleChange}
          />
        </div>
        <div>
          <label className="block mb-2" htmlFor="username">
            Tên đăng nhập
          </label>
          <input
            className="w-full p-2 bg-black text-white rounded-full"
            id="username"
            type="text"
            disabled
            value={profileData.username}
          />
        </div>
        <div>
          <label className="block mb-2" htmlFor="email">
            Email
          </label>
          <input
            className="w-full p-2 bg-black text-white rounded-full"
            id="email"
            type="email"
            disabled
            value={profileData.email}
          />
        </div>
      </form>
      <div className="flex justify-end space-x-4 mt-6 w-1/2">
        <button className="bg-gray-800 text-white py-2 px-4 rounded">
          Đổi mật khẩu
        </button>
        <button onClick={handleSave} className="bg-red-600 text-white py-2 px-4 rounded">
          Lưu thông tin
        </button>
      </div>
    </main>
  );
};

export default Profile;
