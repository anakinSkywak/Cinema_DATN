import React from 'react';
import { jsPDF } from 'jspdf';
import './modal.css';

const Modal = ({ isOpen, onClose, ticket }) => {
    if (!isOpen) return null;

    const handlePrintBill = () => {
        const doc = new jsPDF();
        doc.text('Hóa Đơn Vé', 20, 10);
        doc.text(`Ngày mua: ${ticket.ngay_mua}`, 20, 20);
        doc.text(`Phim: Phim ABC`, 20, 30);
        doc.text(`Số lượng: ${ticket.so_luong}`, 20, 40);
        doc.text(`Ghế ngồi: ${ticket.ghe_ngoi}`, 20, 50);
        doc.text(`Tổng tiền: ${Number(ticket.tong_tien).toLocaleString()} VNĐ`, 20, 60);
        doc.save('bill.pdf');
    };

    return (
        <div className="modal-overlay">
            <div className="modal-content">
                <h2>Chi tiết vé</h2>
                <p><strong>Ngày mua:</strong> {ticket.ngay_mua}</p>
                <p><strong>Phim:</strong> Phim ABC</p>
                <p><strong>Số lượng:</strong> {ticket.so_luong}</p>
                <p><strong>Ghế ngồi:</strong> {ticket.ghe_ngoi}</p>
                <p><strong>Tổng tiền:</strong> {Number(ticket.tong_tien).toLocaleString()} VNĐ</p>
                <button onClick={handlePrintBill}>In hóa đơn PDF</button>
                <button onClick={onClose}>Đóng</button>
            </div>
        </div>
    );
};

export default Modal;