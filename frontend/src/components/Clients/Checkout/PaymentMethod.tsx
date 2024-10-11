import React from 'react';

interface PaymentMethodProps {
  paymentMethod: string;
  totalPrice: number;
  onPaymentChange: (method: string) => void;
  onPaymentSubmit: (e: React.FormEvent) => void;
  onGoBack: () => void;
}

const PaymentMethod: React.FC<PaymentMethodProps> = ({
  paymentMethod,
  totalPrice,
  onPaymentChange,
  onPaymentSubmit,
  onGoBack,
}) => (
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
            onChange={() => onPaymentChange(method)}
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
      <button onClick={onPaymentSubmit} className="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700 transition duration-300">
        Thanh toán
      </button>
      <button onClick={onGoBack} className="bg-gray-700 text-white px-4 py-2 rounded-full hover:bg-gray-600 transition duration-300">
        Quay lại
      </button>
    </div>
  </div>
);

export default PaymentMethod;