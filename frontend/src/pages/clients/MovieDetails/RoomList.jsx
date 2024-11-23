import React, { useState } from 'react';

const RoomList = ({ rooms, renderSeat, handleRoomChange }) => {
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 1; 
    const totalPages = Math.ceil(rooms.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const currentRooms = rooms.slice(startIndex, startIndex + itemsPerPage);

    const handlePreviousRoom = () => {
        if (currentPage > 1) {
            const newPage = currentPage - 1;
            setCurrentPage(newPage);
            handleRoomChange(); 
        }
    };

    const handleNextRoom = () => {
        if (currentPage < totalPages) {
            const newPage = currentPage + 1;
            setCurrentPage(newPage);
            handleRoomChange(); 
        }
    };

    return (
        <div>
            {currentRooms.length > 0 ? (
                currentRooms.map((room) => {
                    // Calculate seats and assign rows
                    const seatsWithRows = room.seats.map((seat, index) => {
                        return {
                            ...seat,
                            row: Math.floor(index / 5) + 1 // Assuming 5 seats per row
                        };
                    });

                    // Group seats by row
                    const seatsByRow = seatsWithRows.reduce((acc, seat) => {
                        const row = seat.row;
                        if (!acc[row]) {
                            acc[row] = [];
                        }
                        acc[row].push(seat);
                        return acc;
                    }, {});

                    return (
                        <div key={room.id} className="room mb-8">
                            <h2 className="text-center text-2xl font-bold mb-8">{room.room?.ten_phong_chieu}</h2>
                            <div className="mb-12 bg-gray-800 p-8 rounded-lg shadow-xl overflow-x-auto">
                                {Object.keys(seatsByRow).map(row => (
                                    <div key={row} className="flex justify-center mb-2">
                                        {seatsByRow[row].map(seat => renderSeat(seat, room))}
                                    </div>
                                ))}
                            </div>
                        </div>
                    );
                })
            ) : (
                <div className="text-center">Không có phòng chiếu nào</div>
            )}

            {/* Pagination Controls */}
            <div className="flex justify-between mt-4">
                <button
                    onClick={handlePreviousRoom}
                    disabled={currentPage === 1}
                    className="bg-gray-700 text-white px-4 py-2 rounded disabled:opacity-50"
                >
                    Trước
                </button>
                <span>
                    Phòng {currentPage} / {totalPages}
                </span>
                <button
                    onClick={handleNextRoom}
                    disabled={currentPage === totalPages}
                    className="bg-gray-700 text-white px-4 py-2 rounded disabled:opacity-50"
                >
                    Sau
                </button>
            </div>
        </div>
    );
};

export default RoomList;