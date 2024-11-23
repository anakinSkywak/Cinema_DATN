import React, { useEffect, useState } from 'react';
import { FaCheckCircle } from 'react-icons/fa';

const TransactionSuccess = () => {
    const [countdown, setCountdown] = useState(10);

    useEffect(() => {
        const interval = setInterval(() => {
            setCountdown((prevCountdown) => {
                if (prevCountdown > 1) {
                    return prevCountdown - 1;
                } else {
                    clearInterval(interval);
                    window.location.href = "http://localhost:5173/"; // Replace with your target URL
                    return 0; // Set countdown to 0 to stop further updates
                }
            });
        }, 1000);

        // Cleanup interval on component unmount
        return () => clearInterval(interval);
    }, []);

    return (
        <div className="bg-gray-900 flex items-center justify-center min-h-screen">
            <div className="bg-gray-700 p-8 rounded-lg shadow-lg text-center w-96">
                <center className="text-6xl text-green-500 mb-4">
                    <FaCheckCircle />
                </center>
                <h1 className="text-2xl text-white font-bold mb-2">GIAO DỊCH THÀNH CÔNG!</h1>
                <p className="text-white mb-4">Nhấn phím <span className="font-bold">OK</span> để vào xem ngay</p>
                <p className="text-gray-400">Tự động vào xem sau <span>{countdown}</span> giây</p>
            </div>
        </div>
    );
};

export default TransactionSuccess;