import React from 'react';

interface DateSelectionProps {
  setSelectedTime: (time: string) => void;
  isTimeSelected: boolean;
}

const DateSelection: React.FC<DateSelectionProps> = ({ setSelectedTime, isTimeSelected }) => {
  const currentDate = new Date();
  const nextDate = new Date(currentDate);
  nextDate.setDate(currentDate.getDate() + 1);

  const formatDate = (date: Date) => {
    const daysOfWeek = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
    const months = ['Th. 1', 'Th. 2', 'Th. 3', 'Th. 4', 'Th. 5', 'Th. 6', 'Th. 7', 'Th. 8', 'Th. 9', 'Th. 10', 'Th. 11', 'Th. 12'];
    return {
      day: date.getDate(),
      month: months[date.getMonth()],
      dayOfWeek: daysOfWeek[date.getDay()],
    };
  };

  const current = formatDate(currentDate);
  const next = formatDate(nextDate);

  const [selectedDate, setSelectedDate] = React.useState<'current' | 'next'>('current');

  return (
    <>
      <div className="w-full flex justify-center items-center bg-[#1A1D23]">
        <div
          className={`text-center px-4 py-2 rounded mr-2 cursor-pointer ${
            selectedDate === 'current' ? 'bg-red-600' : ''
          }`}
          onClick={() => !isTimeSelected && setSelectedDate('current')} // Only allow selecting if time is not chosen
        >
          <div className="text-sm">{current.month}</div>
          <div className="text-4xl font-bold">{current.day}</div>
          <div className="text-sm">{current.dayOfWeek}</div>
        </div>
        <div
          className={`text-center px-4 py-2 rounded mr-2 cursor-pointer ${
            selectedDate === 'next' ? 'bg-red-600' : ''
          }`}
          onClick={() => !isTimeSelected && setSelectedDate('next')} // Only allow selecting if time is not chosen
        >
          <div className="text-sm">{next.month}</div>
          <div className="text-4xl font-bold">{next.day}</div>
          <div className="text-sm">{next.dayOfWeek}</div>
        </div>
      </div>

      {/* Warning Message */}
      <div className="text-center mt-4">
        <p className="text-orange-500 font-semibold">
          Lưu ý: Khán giả dưới 13 tuổi chỉ chọn suất chiếu kết thúc trước 22h và khán giả dưới 16 tuổi chỉ chọn suất chiếu kết thúc trước 23h.
        </p>
      </div>

      {/* Time options */}
      {!isTimeSelected && (
        <div className="flex justify-center space-x-4 mt-4">
          {['10:00', '12:00', '14:00', '16:00'].map((time) => (
            <button
              key={time}
              className="px-4 py-2 rounded-full bg-gray-600 text-white"
              onClick={() => setSelectedTime(time)}
            >
              {time}
            </button>
          ))}
        </div>
      )}
    </>
  );
};

export default DateSelection;
