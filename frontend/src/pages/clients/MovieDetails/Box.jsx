import Seat from "./Seat";
import Time from "./Time";
import { useState, useEffect } from "react";

const Box = ({detail}) => {
    const [selectedDate, setSelectedDate] = useState(null);
    const [selectedTime, setSelectedTime] = useState(null);
    const [selectedId, setSelectedId] = useState(null);
    const [dates, setDates] = useState([]);
    const [showTime, setShowTime] = useState(false);
    const [availableShowtimes, setAvailableShowtimes] = useState([]);

    useEffect(() => {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        setDates([today, tomorrow]);
    }, []);

    const formatDate = (date) => {
        const days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
        return {
            month: date.getMonth() + 1,
            day: date.getDate(),
            dayOfWeek: days[date.getDay()]
        };
    };

    const handleDateSelect = (date) => {
        setSelectedDate(date);
        setShowTime(true);
        setSelectedTime(null); 
        filterShowtimes(date);
    };

    const filterShowtimes = (date) => {
        const formattedDate = date.toISOString().split('T')[0];
        setAvailableShowtimes(formattedDate);
    };

    const handleTimeSelect = (id, time) => {
        setSelectedTime(time);
        setSelectedId(id);
    };

    return (
        <>
            <div>
                <div className="w-full mx-auto">
                    <div className="w-full flex justify-center items-center bg-[#1A1D23]">
                        {dates.map((date, index) => {
                            const formattedDate = formatDate(date);
                            
                            return (
                                <div 
                                    key={index}
                                    className={`text-center px-4 py-2 rounded mr-2 cursor-pointer ${
                                        selectedDate && date.toDateString() === selectedDate.toDateString() ? 'bg-red-600' : ''
                                    }`}
                                    onClick={() => handleDateSelect(date)}
                                >
                                    <div className="text-sm">Th. {formattedDate.month}</div>
                                    <div className="text-2xl font-bold">{formattedDate.day}</div>
                                    <div className="text-sm">{formattedDate.dayOfWeek}</div>
                                </div>
                            );
                        })}
                    </div>
                    <div className="text-center mt-4">
                        <p className="text-orange-500 font-semibold">Lưu ý: Khán giả dưới 13 tuổi chỉ chọn suất chiếu kết thúc trước 22h và Khán giả dưới 16 tuổi chỉ chọn suất chiếu kết thúc trước 23h.</p>
                    </div>
                    {showTime && (
                        <Time 
                            selectedDate={selectedDate} 
                            onTimeSelect={handleTimeSelect}
                            selectedTime={selectedTime}
                            availableShowtimes={availableShowtimes} // Pass the filtered showtimes
                        />
                    )}
                    {selectedTime && <Seat timeId={selectedId} availableShowtimes={availableShowtimes} selectedDate={selectedDate} selectedTime={selectedTime} detail={detail} />}
                </div>
            </div>
        </>
    );
}

export default Box;