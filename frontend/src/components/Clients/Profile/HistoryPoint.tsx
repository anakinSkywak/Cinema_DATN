import React from 'react';
import { Link } from 'react-router-dom';

const HistoryPoints: React.FC = () => {
  return (
    <main className="flex flex-col items-center py-8">
      <h1 className="text-2xl mb-6">Thông tin cá nhân</h1>
      <div className="flex space-x-4 mb-6">
        <button className="bg-gray-800 text-white py-2 px-4 rounded-full">
          <Link to="/profile">Tài khoản của tôi</Link>
        </button>
        <button className="bg-gray-800 text-white py-2 px-4 rounded-full">
          <Link to="/history-ticket">Lịch sử mua vé</Link>
        </button>
        <button className="bg-red-600 text-white py-2 px-4 rounded-full">
          <Link to="/history-point">Lịch sử điểm thưởng</Link>
        </button>
      </div>
      <div className="bg-black p-4 rounded w-3/4">
        <table className="w-full text-left">
          <thead className="border-b border-gray-700">
            <tr>
              <th className="py-2">Ngày giao dịch</th>
              <th className="py-2">Tên phim</th>
              <th className="py-2">Số vé</th>
              <th className="py-2">Số điểm</th>
            </tr>
          </thead>
          <tbody style={{ height: '200px' }}>
            <tr>
              <td className="py-4 text-center" colSpan={4}>
                Không có dữ liệu
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  );
};

export default HistoryPoints;
