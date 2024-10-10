import React, { useState } from 'react';

const CheckoutPage: React.FC = () => {
  const [paymentMethod, setPaymentMethod] = useState<string>('VietQR');

  const handlePaymentChange = (method: string) => {
    setPaymentMethod(method);
  };

  const handlePaymentSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    alert(`Thanh toán thành công với phương thức: ${paymentMethod}`);
  };

  return (
    <main className="flex flex-col items-center py-8 mt-20">
      <div className="max-w-full mx-auto p-4 flex px-20">
        {/* Movie and Seat Information */}
        <div className="w-2/3">
          <div className="bg-black p-4 rounded-lg mb-4">
            <h2 className="text-lg font-bold mb-2">Thông tin phim</h2>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <p>Phim</p>
                <p className="font-bold">TRANSFORMERS MỘT-T13 (Phụ đề)</p>
              </div>
              <div>
                <p>Ghế</p>
                <p className="font-bold">I7</p>
              </div>
              <div>
                <p>Ngày giờ chiếu</p>
                <p className="text-red-600 font-bold">10:20 - 28/09/2024</p>
              </div>
              <div>
                <p>Phòng chiếu</p>
                <p className="font-bold">12</p>
              </div>
              <div>
                <p>Định dạng</p>
                <p className="font-bold">2D</p>
              </div>
            </div>
          </div>

          {/* Payment Information */}
          <div className="bg-black p-4 rounded-lg mb-4">
            <h2 className="text-lg font-bold mb-2">Thông tin thanh toán</h2>
            <table className="w-full text-left">
              <thead>
                <tr>
                  <th className="border-b border-gray-700 p-2">Danh mục</th>
                  <th className="border-b border-gray-700 p-2">Số lượng</th>
                  <th className="border-b border-gray-700 p-2">Tổng tiền</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td className="border-b border-gray-700 p-2">Ghế (I7)</td>
                  <td className="border-b border-gray-700 p-2">1</td>
                  <td className="border-b border-gray-700 p-2">80.000đ</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        {/* Payment Method */}
        <div className="w-1/3 ml-4">
          <div className="bg-black p-4 rounded-lg">
            <h2 className="text-lg font-bold mb-2">Phương thức thanh toán</h2>
            <div className="space-y-2">
              <label className="flex items-center border border-gray-700 p-2 rounded-full hover:bg-gray-800 cursor-pointer">
                <input
                  type="radio"
                  name="payment"
                  className="form-radio text-red-600"
                  checked={paymentMethod === 'VietQR'}
                  onChange={() => handlePaymentChange('VietQR')}
                />
                <span className="ml-2">VietQR</span>
              </label>
              <label className="flex items-center border border-gray-700 p-2 rounded-full hover:bg-gray-800 cursor-pointer">
                <input
                  type="radio"
                  name="payment"
                  className="form-radio text-blue-600"
                  checked={paymentMethod === 'VNPAY'}
                  onChange={() => handlePaymentChange('VNPAY')}
                />
                <span className="ml-2">VNPAY</span>
              </label>
              <label className="flex items-center border border-gray-700 p-2 rounded-full hover:bg-gray-800 cursor-pointer">
                <input
                  type="radio"
                  name="payment"
                  className="form-radio text-pink-600"
                  checked={paymentMethod === 'Viettel Money'}
                  onChange={() => handlePaymentChange('Viettel Money')}
                />
                <span className="ml-2">Viettel Money</span>
              </label>
              <label className="flex items-center border border-gray-700 p-2 rounded-full hover:bg-gray-800 cursor-pointer">
                <input
                  type="radio"
                  name="payment"
                  className="form-radio text-blue-600"
                  checked={paymentMethod === 'Payoo'}
                  onChange={() => handlePaymentChange('Payoo')}
                />
                <span className="ml-2">Payoo</span>
              </label>
            </div>
            <p className="text-yellow-500 text-sm mt-4">
              Lưu ý: Không mua vé cho trẻ em dưới 13 tuổi đối với các suất chiếu phim kết thúc sau 22h00 và không mua vé
              cho trẻ em dưới 16 tuổi đối với các suất chiếu phim kết thúc sau 23h00.
            </p>
            <div className="mt-4">
              <div className="flex justify-between mb-2">
                <span>Thanh toán</span>
                <span>80.000đ</span>
              </div>
              <div className="flex justify-between font-bold">
                <span>Tổng cộng</span>
                <span className='text-red-600 font-bold'>80.000đ</span>
              </div>
            </div>

            <div className="mt-4 flex flex-col space-y-2">
              <button onClick={handlePaymentSubmit} className="bg-red-600 text-white px-4 py-2 rounded-full hover-zoom">
                Thanh toán
              </button>
              <button type="button" className="bg-gray-700 text-white px-4 py-2 rounded-full hover-bg-gray-600">
                Quay lại
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  );
};

export default CheckoutPage;
