import React, { useState, useEffect } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';

interface SelectedSeat {
  seat: string;
  type: string;
}

interface MovieInfo {
  title: string;
  releaseDate: string;
  selectedDate: string;
  selectedTime: string;
  theater: string;
  format: string;
}

const CheckoutPage: React.FC = () => {
  const [paymentMethod, setPaymentMethod] = useState<string>('VietQR');
  const location = useLocation();
  const navigate = useNavigate();
  const [movieInfo, setMovieInfo] = useState<MovieInfo>({
    title: '',
    releaseDate: '',
    selectedDate: '',
    selectedTime: '',
    theater: '',
    format: '',
  });
  const [selectedSeats, setSelectedSeats] = useState<SelectedSeat[]>([]);
  const [totalPrice, setTotalPrice] = useState(0);

  useEffect(() => {
    if (location.state) {
      setMovieInfo({
        title: location.state.title || 'TRANSFORMERS MỘT-T13 (Phụ đề)',
        releaseDate: location.state.releaseDate || '27/09/2024',
        selectedDate: location.state.selectedDate || '',
        selectedTime: location.state.selectedTime || '',
        theater: location.state.theater || '12',
        format: location.state.format || '2D',
      });
      setSelectedSeats(location.state.selectedSeats || []);
      setTotalPrice(location.state.totalPrice || 0);
    }
  }, [location]);

  const getPriceForSeatType = (type: string): number => {
    switch (type) {
      case 'Ghế VIP':
        return 150000;
      case 'Ghế Đôi':
        return 120000;
      case 'Ghế Thường':
      default:
        return 100000;
    }
  };

  useEffect(() => {
    const newTotalPrice = selectedSeats.reduce((total, seat) => total + getPriceForSeatType(seat.type), 0);
    setTotalPrice(newTotalPrice);
  }, [selectedSeats]);

  const handlePaymentChange = (method: string) => {
    setPaymentMethod(method);
  };

  const handlePaymentSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    alert(`Thanh toán thành công với phương thức: ${paymentMethod}`);
  };

  const handleGoBack = () => {
    navigate(-1);
  };

  return (
    <main className="flex flex-col items-center py-8 mt-20">
      <div className="max-w-full mx-auto p-4 flex px-20">
        <div className="w-2/3">
          <div className="bg-black p-4 rounded-lg mb-4">
            <h2 className="text-lg font-bold mb-2">Thông tin phim</h2>
            <div className="grid grid-cols-2 gap-4">
              <div>
                <p>Phim</p>
                <p className="font-bold">{movieInfo.title}</p>
              </div>
              <div>
                <p>Ghế</p>
                <p className="font-bold">{selectedSeats.map(seat => seat.seat).join(', ')}</p>
              </div>
              <div>
                <p>Ngày giờ chiếu</p>
                <p className="text-red-600 font-bold">
                  {movieInfo.selectedDate && movieInfo.selectedTime
                    ? `${movieInfo.selectedDate} - ${movieInfo.selectedTime}`
                    : 'Chưa chọn'}
                </p>
              </div>
              <div>
                <p>Phòng chiếu</p>
                <p className="font-bold">{movieInfo.theater}</p>
              </div>
              <div>
                <p>Định dạng</p>
                <p className="font-bold">{movieInfo.format}</p>
              </div>
              <div>
                <p>Khởi chiếu</p>
                <p className="font-bold">{movieInfo.releaseDate}</p>
              </div>
            </div>
          </div>

          <div className="bg-black p-4 rounded-lg mb-4">
            <h2 className="text-lg font-bold mb-2">Thông tin thanh toán</h2>
            <table className="w-full text-left">
              <thead>
                <tr>
                  <th className="border-b border-gray-700 p-2">Tên Ghế</th>
                  <th className="border-b border-gray-700 p-2">Loại Ghế</th>
                  <th className="border-b border-gray-700 p-2">Giá Tiền</th>
                </tr>
              </thead>
              <tbody>
                {selectedSeats.map((seat, index) => (
                  <tr key={index}>
                    <td className="border-b border-gray-700 p-2">{seat.seat}</td>
                    <td className="border-b border-gray-700 p-2">{seat.type}</td>
                    <td className="border-b border-gray-700 p-2">
                      {getPriceForSeatType(seat.type).toLocaleString()}đ
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        <div className="w-1/3 ml-4">
          <div className="bg-black p-4 rounded-lg">
            <h2 className="text-lg font-bold mb-2">Phương thức thanh toán</h2>
            <div className="space-y-2">
              {['VietQR', 'VNPAY', 'Viettel Money', 'Payoo'].map((method) => (
                <label key={method} className="flex items-center border border-gray-700 p-2 rounded-full hover:bg-gray-800 cursor-pointer">
                  <input
                    type="radio"
                    name="payment"
                    className="form-radio text-red-600"
                    checked={paymentMethod === method}
                    onChange={() => handlePaymentChange(method)}
                  />
                  <span className="ml-2">{method}</span>
                </label>
              ))}
            </div>
            <p className="text-yellow-500 text-sm mt-4">
              Lưu ý: Không mua vé cho trẻ em dưới 13 tuổi đối với các suất chiếu phim kết thúc sau 22h00 và không mua vé
              cho trẻ em dưới 16 tuổi đối với các suất chiếu phim kết thúc sau 23h00.
            </p>
            <div className="mt-4">
              <div className="flex justify-between mb-2">
                <span>Thanh toán</span>
                <span>{totalPrice.toLocaleString()}đ</span>
              </div>
              <div className="flex justify-between font-bold">
                <span>Tổng cộng</span>
                <span className='text-red-600 font-bold'>{totalPrice.toLocaleString()}đ</span>
              </div>
            </div>

            <div className="mt-4 flex flex-col space-y-2">
              <button onClick={handlePaymentSubmit} className="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700 transition duration-300">
                Thanh toán
              </button>
              <button onClick={handleGoBack} className="bg-gray-700 text-white px-4 py-2 rounded-full hover:bg-gray-600 transition duration-300">
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