import { Button } from "antd";
import MyProfile from "./MyProfile";
import ScoreHistory from "./ScoreHistory";
import TicketHistory from "./TicketHistory";
import { useState } from "react";

const ProfilePage = () => {
    const [activeComponent, setActiveComponent] = useState('profile'); // Default component

    const handleButtonClick = (component) => {
        setActiveComponent(component);
    };
    return (
        <>
            <main className="flex flex-col items-center py-8 mt-16">
                <h1 className="text-2xl mb-6">Thông tin cá nhân</h1>
                <div className="flex space-x-4 mb-6">
                    <button
                        className={`py-2 px-4 rounded-full ${activeComponent === 'profile' ? 'bg-red-600 text-white' : 'bg-gray-800 text-white'}`}
                        onClick={() => handleButtonClick('profile')}
                    >
                        Tài khoản của tôi
                    </button>
                    <button
                        className={`py-2 px-4 rounded-full ${activeComponent === 'ticketHistory' ? 'bg-red-600 text-white' : 'bg-gray-800 text-white'}`}
                        onClick={() => handleButtonClick('ticketHistory')}
                    >
                        Lịch sử mua vé
                    </button>
                    <button
                        className={`py-2 px-4 rounded-full ${activeComponent === 'rewardHistory' ? 'bg-red-600 text-white' : 'bg-gray-800 text-white'}`}
                        onClick={() => handleButtonClick('rewardHistory')}
                    >
                        Lịch sử điểm thưởng
                    </button>
                </div>
                {activeComponent === 'profile' && <MyProfile />}
                {activeComponent === 'ticketHistory' && <TicketHistory />}
                {activeComponent === 'rewardHistory' && <ScoreHistory />}
            </main>
        </>
    );
}

export default ProfilePage;