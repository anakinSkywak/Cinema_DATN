import React from 'react';
import { Link } from 'react-router-dom';

const Footer: React.FC = () => {
  return (
    <footer className="bg-black text-white py-5">
      <div className="container mx-auto px-10 text-center">

        <div className="mb-8">
          {/* Navigation links */}
          <div className="flex justify-center space-x-6 mb-10 text-lg">
            <Link to="/chinh-sach" className="hover:text-gray-300">Chính sách</Link>
            <Link to="/lich-chieu" className="hover:text-gray-300">Lịch chiếu</Link>
            <Link to="/tin-tuc" className="hover:text-gray-300">Tin tức</Link>
            <Link to="/gia-ve" className="hover:text-gray-300">Giá vé</Link>
            <Link to="/hoi-dap" className="hover:text-gray-300">Hỏi đáp</Link>
            <Link to="/lien-he" className="hover:text-gray-300">Liên hệ</Link>
          </div>

          {/* Social media and app store links */}
          <div className="flex justify-center space-x-6">
            <Link to="https://www.facebook.com" className="hover:opacity-75">
              <img src="../../../public/images/facebook.png" alt="Facebook" className="h-6 w-6" />
            </Link>
            <Link to="https://www.zalo.me" className="hover:opacity-75">
              <img src="../../../public/images/zalo.png" alt="Zalo" className="h-6 w-6" />
            </Link>
            <Link to="https://www.youtube.com" className="hover:opacity-75">
              <img src="../../../public/images/youtube.png" alt="YouTube" className="h-6 w-6" />
            </Link>
            <Link to="https://play.google.com/store" className="hover:opacity-75">
              <img src="../../../public/images/googleplay.png" alt="Google Play" className="h-8 w-20" />
            </Link>
            <Link to="https://www.apple.com/app-store/" className="hover:opacity-75">
              <img src="../../../public/images/appstore.png" alt="App Store" className="h-8 w-20" />
            </Link>
            <Link to="#" className="hover:opacity-75">
              <img src="../../../public/images/chungnhan.png" alt="Certification" className="h-8 w-20" />
            </Link>
          </div>
        </div>

        <div className="text-base mb-4 leading-relaxed">
          <p>Cơ quan chủ quản: BỘ VĂN HÓA, THỂ THAO VÀ DU LỊCH</p>
          <p className="mt-2">Bản quyền thuộc Trung tâm Chiếu phim Quốc gia.</p>
          <p className="mt-2">Giấy phép số: 224/GP- TTĐT ngày 31/8/2010 - Chịu trách nhiệm: Vũ Đức Tùng – Giám đốc.</p>
          <p className="mt-2">Địa chỉ: 87 Láng Hạ, Quận Ba Đình, Tp. Hà Nội - Điện thoại: 024.35141791</p>
          <p className="mt-2">Copyright 2023. NCC All Rights Reserved. Dev by Anvuivn.</p>
        </div>

      </div>
    </footer>
  );
};

export default Footer;
