import axios from "axios";
import { getTokenOfUser } from "../../../utils/storage";
import { useEffect, useState } from "react";
import Modal from "./Modal";

const TicketHistory = () => {
    const accessToken = getTokenOfUser ();
    const [ticketData, setTicketData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [currentPage, setCurrentPage] = useState(1);
    const [itemsPerPage] = useState(5);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedTicket, setSelectedTicket] = useState(null);


    const handleOpenModal = (ticket) => {
        setSelectedTicket(ticket);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedTicket(null);
    };

    const callPaymentMethod = async () => {
        try {
            const result = await axios.get(`http://127.0.0.1:8000/api/booking-detail`, {
                headers: {
                    'Authorization': `Bearer ${accessToken}`,
                    'Content-Type': 'application/json',
                },
            });
            setTicketData(result.data.data); // Assuming result.data contains the ticket history
        } catch (error) {
            console.error('Error:', error);
            setError('Failed to fetch ticket history');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        callPaymentMethod();
    }, []);

    if (loading) {
        return <div>Loading...</div>;
    }

    if (error) {
        return <div>{error}</div>;
    }

    const totalTickets = ticketData.length;
    const totalPages = Math.ceil(totalTickets / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const currentTickets = ticketData.slice(startIndex, startIndex + itemsPerPage);

    function convertDateString(dateString) {
        const [year, month, day] = dateString.split('-').map(Number);
        return day + "-" + month + "-" + year;
    }
    return (
        <>
            <div className="bg-black p-4 rounded w-3/4">
                <table className="w-full text-left">
                    <thead className="border-b border-gray-700">
                        <tr>
                            <th className="py-2 text-center">Ngày giao dịch</th>
                            <th className="py-2 text-center">Tên phim</th>
                            <th className="py-2 text-center">Số vé</th>
                            <th className="py-2 text-center">Ghế</th>
                            <th className="py-2 text-center">Số tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        {currentTickets.length > 0 ? (
                            currentTickets?.map((ticket, index) => (
                                <tr key={index}>
                                    <td className="py-4 text-center">{convertDateString(ticket.ngay_mua)}</td>
                                    <td className="py-4 text-center cursor-pointer text-red-600" onClick={() => handleOpenModal(ticket)}>Phim ABC</td>
                                    <td className="py-4 text-center">{ticket.so_luong}</td>
                                    <td className="py-4 text-center">{ticket.ghe_ngoi}</td>
                                    <td className="py-4 text-center">{Number(ticket.tong_tien).toLocaleString()} VNĐ</td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td className="py-4 text-center" colSpan="4">Không có dữ liệu</td>
                            </tr>
                        )}
                    </tbody>
                </table>

                <div className="flex justify-between mt-4">
                    <button
                        onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
                        disabled={currentPage === 1}
                        className="bg-gray-700 text-white px-4 py-2 rounded disabled:opacity-50"
                    >
                        Trước
                    </button>
                    <span>
                        Trang {currentPage} / {totalPages}
                    </span>
                    <button
                        onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
                        disabled={currentPage === totalPages}
                        className="bg-gray-700 text-white px-4 py-2 rounded disabled:opacity-50"
                    >
                        Sau
                    </button>
                </div>
                <Modal isOpen={isModalOpen} onClose={handleCloseModal} ticket={selectedTicket} />
            </div>
        </>
    );
};

export default TicketHistory;