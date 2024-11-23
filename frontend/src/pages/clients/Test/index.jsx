import { useParams } from "react-router-dom";
import { useGetMovieDetailById } from "../../../hooks/api/useMovieApi";
import { useState } from "react";

const Test = () => {
    const { id } = useParams();
    const { data, isLoading } = useGetMovieDetailById(id);
    const [selectedSeats, setSelectedSeats] = useState([]);
    const [totalPrice, setTotalPrice] = useState(0);
    const [notification, setNotification] = useState(''); // State để lưu thông báo

    const seatPrice = {
        "Thường": 100000, // Giá ghế thường
        "VIP": 200000, // Giá ghế VIP
        "Double": 300000 // Giá ghế đôi
    };

    if (isLoading) {
        return <div>Loading...</div>;
    }

    if (!data) {
        return <div>No data available</div>;
    }

    const room = data?.['movie-detail']?.showtimes[0].room;

    if (!room) {
        return <div>No room information available</div>;
    }

    const toggleSeatSelection = (seat) => {
        // Kiểm tra nếu ghế đã được đặt
        if (seat.trang_thai === 1) {
            setNotification(`Ghế ${seat.so_ghe_ngoi} đã được đặt!`);
            return; // Không cho phép chọn ghế đã đặt
        }

        setNotification(''); // Xóa thông báo nếu ghế chưa đặt
        setSelectedSeats((prev) => {
            const isSelected = prev.includes(seat.id);
            const updatedSeats = isSelected ? prev.filter(id => id !== seat.id) : [...prev, seat.id];

            // Cập nhật tổng tiền
            const priceChange = isSelected ? -seatPrice[seat.loai_ghe_ngoi] : seatPrice[seat.loai_ghe_ngoi];
            setTotalPrice(prevPrice => prevPrice + priceChange);

            return updatedSeats;
        });
    };

    const renderSeat = (seat) => {
        let seatClass = 'flex items-center justify-center text-white font-bold cursor-pointer';

        // Determine the seat status
        if (seat.trang_thai === 1) {
            seatClass += ' bg-gray-700'; // Ghế đã đặt
            return (
                <div key={seat.id} className={`w-10 h-10 m-1 text-xs font-bold rounded ${seatClass}`}>
                    X
                </div>
            );
        }

        // Ghế chưa đặt
        if (selectedSeats.includes(seat.id)) {
            seatClass += ' bg-blue-500'; // Ghế đã chọn
        } else {
            // Ghế còn lại
            if (seat.loai_ghe_ngoi === "Thường") {
                seatClass += ' bg-gray-600'; // Ghế thường
            } else if (seat.loai_ghe_ngoi === "VIP") {
                seatClass += ' bg-orange-400'; // Ghế VIP
            } else if (seat.loai_ghe_ngoi === "Double") {
                seatClass += ' bg-red-400'; // Ghế đôi
            }
        }

        return (
            <div key={seat.id} className={`w-10 h-10 m-1 text-xs font-bold rounded ${seatClass}`} onClick={() => toggleSeatSelection(seat)}>
                {seat.so_ghe_ngoi}
            </div>
        );
    };

    return (
        <>
            <div className="bg-gray-900 text-white p-6 relative">
                <div className="max-w-6xl mx-auto">
                    <h2 className="text-center text-2xl font-bold mt-20 mb-8">Phòng chiếu số {room.ten_phong_chieu}</h2>
                    <div className="mb-12 bg-gray-800 p-8 rounded-lg shadow-xl overflow-x-auto">
                        {room?.seat?.reduce((acc, seat, index) => {
                            if ((index + 1) % 10 === 0) {
                                acc.push(
                                    <div key={index} className="flex justify-center mb-2">
                                        {room?.seat?.slice(index - 9, index + 1).map(seat => renderSeat(seat))}
                                    </div>
                                );
                            }
                            return acc;
                        }, [])}
                    </div>
                    <div className="text-center text-lg font-bold">
                        Tổng tiền: {totalPrice.toLocaleString()} VNĐ
                    </div>
                    {/* Hiển thị danh sách ghế đã chọn */}
                    <div className="text-center text-lg font-bold mt-4">
                        Ghế đã chọn: {selectedSeats.map(seatId => (
                            <span key={seatId}>{room.seat.find(seat => seat.id === seatId).so_ghe_ngoi} </span>
                        ))}
                    </div>
                    {/* Hiển thị thông báo */}
                    {notification && (
                        <div className="text-center text-lg font-bold text-red-500 mt-4">
                            {notification}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
};

export default Test;