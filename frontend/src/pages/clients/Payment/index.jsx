import { useState, useEffect } from "react";
import { useParams, useLocation, useNavigate } from "react-router-dom";
import { Spin } from "antd";
import axios from "axios";
import { getTokenOfUser } from "../../../utils/storage";

const Payment = () => {
  const [selectedSeats, setSelectedSeats] = useState([]);
  const [totalAmount, setTotalAmount] = useState(0);
  const [selectedTime, setSelectedTime] = useState("");
  const [selectedSeatIds, setSelectedSeatIds] = useState([]);
  const [movieDetail, setMovieDetail] = useState();
  const [selectedItems, setSelectedItems] = useState([]);
  const [ticketPrice, setTicketPrice] = useState();
  const [showtime, setShowtime] = useState();
  const location = useLocation();
  const navigate = useNavigate();
  const [promoCode, setPromoCode] = useState('');
  const [discount, setDiscount] = useState(0);
  const accessToken = getTokenOfUser();
  const [isModalOpen, setIsModalOpen] = useState(false);
  const promoCodes = ['vou5', 'vou10', 'vou15'];


  if (!accessToken) {
    window.location.href = '/';
  }

  const handleApplyPromoCode = () => {
    if (promoCode === "vou10") {
      setDiscount(totalAmount * 0.1);
    } else if (promoCode === "vou15") {
      setDiscount(totalAmount * 0.15);
    } else if (promoCode === "vou5") {
      setDiscount(totalAmount * 0.05);
    } else {
      alert("Mã khuyến mãi không hợp lệ");
    }
  };

  const finalAmount = totalAmount - discount;

  useEffect(() => {

    if (location.state) {
      setSelectedSeats(location.state.selectedSeats);
      setTotalAmount(location.state.totalAmount);
      setTicketPrice(location.state.totalAmount)
      setSelectedTime(location.state.selectedTime);
      setSelectedSeatIds(location.state.selectedSeatIds)
      setMovieDetail(location.state.movieDetail),
        setShowtime(location.state.showtimeState),
        setSelectedItems(location.state.items),
        setTicketPrice(location.state.ticketPrice)
    }
  }, [location.state]);


  const processDoan = () => {
    const doanArray = Object.keys(selectedItems).map((key) => ({
      doan_id: selectedItems[key].id,
      so_luong_do_an: selectedItems[key].quantity,
    }));
    return doanArray;
  };




  const data = {
    thongtinchieu_id: location.state.timeId,
    ghe_ngoi: selectedSeats,
    doan: processDoan(),
    ma_giam_gia: promoCode,
    ghi_chu: "Chỗ ngồi yêu cầu ở giữa"
  };


  const handlePayment = async () => {
    const sendData = async () => {
      try {
        const result = await axios.post('http://127.0.0.1:8000/api/booking', data, {
          headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Content-Type': 'application/json',
          },
        });
        callPaymentMethod(result?.data);
        // usePaymentMethod(result?.data?.id, "ncb")
        console.log(result?.data)
      } catch (error) {
        console.error('Error:', error);
      }
    };

    await sendData();
  };

  const handleSelectPromoCode = (code) => {
    setPromoCode(code);
    setIsModalOpen(false);
  };

  const callPaymentMethod = async (data) => {
    try {

      const result = await axios.post(`http://127.0.0.1:8000/api/payment/${data?.data.id}/ncb`, data, {
        headers: {
          'Authorization': `Bearer ${accessToken}`, // Add the bearer token here
          'Content-Type': 'application/json',
        },
      });
      window.location.href = result?.data.url
    } catch (error) {
      console.error('Error:', error);
    }
  };

  if (!movieDetail) {
    return <Spin size="large" className='flex items-center justify-center mt-20'></Spin>;
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800 text-white  mt-10">
      <div className="container mx-auto px-4 py-12">
        <h1 className="text-4xl font-bold mb-8 text-center">Thanh toán</h1>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
          <div className="space-y-8">
            <div className="bg-gray-800 p-6 rounded-lg shadow-lg transition duration-300 hover:shadow-xl">
              <h2 className="text-2xl font-bold mb-4 text-red-500">Thông tin phim</h2>
              <div className="space-y-2">
                <p><span className="font-semibold">Phim:</span> {movieDetail?.ten_phim}</p>
                <p><span className="font-semibold">Giờ chiếu:</span> {selectedTime}</p>
                <p><span className="font-semibold">Ngày chiếu:</span> {location.state.date}</p>
                <p><span className="font-semibold">Phòng chiếu:</span> {showtime?.room?.ten_phong_chieu}</p>
              </div>
            </div>

            <div className="bg-gray-800 p-6 rounded-lg shadow-lg transition duration-300 hover:shadow-xl mt-6">
              <h2 className="text-2xl font-bold mb-4 text-red-500">Thông tin thanh toán</h2>
              <table className="w-full">
                <thead>
                  <tr className="border-b border-gray-700">
                    <th className="text-left py-2">Danh mục</th>
                    <th className="text-left py-2">Số lượng</th>
                    <th className="text-right py-2">Tổng tiền</th>
                  </tr>
                </thead>
                <tbody>
                  <td className="py-2">Ghế ({selectedSeatIds.join(', ')})</td>
                  <td className="py-2">{selectedSeats.length}</td>
                  <td className="text-right py-2">{ticketPrice}đ</td>
                  {Object.keys(selectedItems).map((key, index) => {
                    const item = selectedItems[key]; // Lấy món ăn từ selectedItems
                    return (
                      <tr key={index}>
                        <td className="py-2">{item.ten_do_an}</td>
                        <td className="py-2">{item.quantity}</td> {/* Hiển thị số lượng */}
                        <td className="text-right py-2">{item.quantity * item.gia}đ</td>
                      </tr>
                    );
                  })}
                  <tr>
                    <td className="py-2 font-bold">Tổng cộng</td>
                    <td className="py-2"></td>
                    <td className="text-right py-2 font-bold">{totalAmount.toLocaleString()}đ</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div className="bg-gray-800 p-6 rounded-lg shadow-lg transition duration-300 hover:shadow-xl">
            <h2 className="text-2xl font-bold mb-6 text-red-500">Phương thức thanh toán</h2>
            <div className="space-y-4 mb-6">
              {["Visa", "NCB", "Master card", "Momo"].map((method) => (
                <label key={method} className="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg cursor-pointer transition duration-300 hover:bg-gray-600">
                  <input type="radio" name="paymentMethod" className="form-radio text-red-500" />
                  <span className="text-lg">{method}</span>
                </label>
              ))}
            </div>

            <div className="space-y-2 mb-6">
              <p className="flex justify-between"><span>Chi phí</span><span>{totalAmount.toLocaleString()}đ</span></p>
              <p className="flex justify-between"><span>Phí</span><span>0đ</span></p>
              <p className="flex justify-between"><span>Giảm giá</span><span>-{discount.toLocaleString()}đ</span></p> {/* Hiển thị giảm giá */}
              <p className="flex justify-between font-bold text-xl"><span>Tổng cộng</span><span>{finalAmount.toLocaleString()}đ</span></p> {/* Hiển thị tổng cộng sau khi giảm giá */}
            </div>

            {/* Input mã khuyến mãi */}
            <div className="mb-4">
              <input
                type="text"
                value={promoCode}
                onChange={(e) => setPromoCode(e.target.value)}
                onClick={() => setIsModalOpen(true)}
                placeholder="Nhập mã khuyến mãi"
                className="w-full p-3 bg-gray-700 rounded-lg text-white placeholder-gray-400"
              />
              <button
                className="w-full bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 mt-2 mb-10"
                onClick={handleApplyPromoCode}
              >
                Áp dụng
              </button>
            </div>

            <button
              className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-full transition duration-300 mb-4"
              onClick={handlePayment}
            >
              Thanh toán
            </button>
            <button
              className="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-full transition duration-300"
              onClick={() => navigate(-1)}
            >
              Quay lại
            </button>

            <p className="text-yellow-400 text-sm mt-6">
              Lưu ý: Không mua vé cho trẻ em dưới 13 tuổi đối với các suất chiếu phim kết thúc sau 22h00 và không mua vé cho trẻ em dưới 16 tuổi đối với các suất chiếu phim kết thúc sau 23h00.
            </p>
          </div>

        </div>
      </div>
      <PromoCodeModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        promoCodes={promoCodes}
        onSelectPromoCode={handleSelectPromoCode}
      />
    </div>


  );
};

export default Payment;


const PromoCodeModal = ({ isOpen, onClose, promoCodes, onSelectPromoCode }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
      <div className="bg-white rounded-lg p-6 w-11/12 md:w-1/3">
        <h2 className="text-lg font-bold mb-4">Các mã khuyến mãi</h2>
        <ul>
          {promoCodes.map((code, index) => (
            <li key={index} className="mb-2 " style={{ cursor: 'pointer', color: 'black' }} onClick={() => onSelectPromoCode(code)}>
              {code}
            </li>
          ))}
        </ul>
        <button
          className="mt-4 bg-red-500 text-white py-2 px-4 rounded"
          onClick={onClose}
        >
          Đóng
        </button>
      </div>
    </div>
  );
};