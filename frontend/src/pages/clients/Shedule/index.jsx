import { useState } from "react";
import { useGetShowtimes } from "../../../hooks/api/useShowtimeApi";

const ShedulePage = () => {
    const { data, isLoading } = useGetShowtimes();
    const [selectedDate, setSelectedDate] = useState(new Date()); // Bắt đầu với ngày hôm nay

    // Hàm để lấy ngày hiện tại và hai ngày tiếp theo
    const getNextThreeDays = () => {
        const today = new Date();
        return Array.from({ length: 3 }, (_, index) => {
            const nextDate = new Date(today);
            nextDate.setDate(today.getDate() + index);
            return nextDate.toLocaleDateString('en-GB'); // Định dạng là DD-MM-YYYY
        });
    };

    const dateButtons = getNextThreeDays();

    if (isLoading) {
        return <div className="text-center py-10">Loading...</div>;
    }

    return (
        <>
            <div className="flex justify-center py-10 mt-16 px-32">
                <div className="w-full max-w-5xl">
                    <div className="text-center mb-6">
                        <h1 className="text-xl font-bold">
                            Phim đang chiếu
                        </h1>
                        <div className="flex justify-center space-x-4 mt-4">
                            {dateButtons.map(date => (
                                <button
                                    key={date}
                                    className={`py-2 px-4 rounded-lg ${selectedDate.toLocaleDateString('en-GB') === date ? 'bg-red-600 date-button' : 'date-button-inactive border'}`}
                                    onClick={() => setSelectedDate(new Date(date.split('-').reverse().join('-')))} // Chuyển đổi thành đối tượng Date
                                >
                                    {date}
                                </button>
                            ))}
                        </div>
                        <p className="text-yellow-600 note mt-4">
                            Lưu ý: Khán giả dưới 13 tuổi chỉ chọn suất chiếu kết thúc trước 22h và khán giả dưới 16 tuổi chỉ chọn suất chiếu kết thúc trước 23h.
                        </p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {data.data.map(item => (
                            <div key={item.id} className="movie-card p-4 flex space-x-4 border rounded-3xl">
                                <div className="movie-poster hover-zoom">
                                    <img
                                        alt={`Movie poster of ${item.movie.ten_phim}`}
                                        className="w-[300px] h-[250px] rounded-lg"
                                        src={`http://localhost:8000${item.movie.anh_phim}`}
                                        width="100"
                                    />
                                </div>
                                <div>
                                    <p className="text-sm">
                                        {item.movie.dien_vien} | {item.thoi_luong_chieu} Phút
                                    </p>
                                    <h2 className="title text-lg font-bold">
                                        {item.movie.ten_phim} (Phụ đề)
                                    </h2>
                                    <p className="text-sm">
                                        Xuất xứ: Nước Ngoài
                                    </p>
                                    <p className="text-sm">
                                       Trạng thái: {item.movie.hinh_thuc_phim}
                                    </p>
                                    <p className="text-red-600 warning text-sm">
                                        Kiểm duyệt: T18 - Phim được phổ biến đến người xem từ đủ 18 tuổi trở lên 20+
                                    </p>
                                    <p className="text-sm mt-2 font-bold">
                                        Lịch chiếu
                                    </p>
                                    <div className="flex space-x-2 mt-1">
                                        <button className="bg-[#fff0] text-white border py-1 px-2 rounded-lg">
                                            {item.gio_chieu}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </>
    );
};

export default ShedulePage;