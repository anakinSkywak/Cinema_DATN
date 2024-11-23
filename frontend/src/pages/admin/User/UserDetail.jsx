import { Modal, Table, Tag } from 'antd';
import { useState, useEffect } from 'react';
import { generateUserData } from './UserData';

function UserDetail({ isDetailOpen, setIsDetailOpen }) {
    const [userData, setUserData] = useState({});

    useEffect(() => {
        if (isDetailOpen.isOpen) {
            const allUsers = generateUserData();
            const selectedUser = allUsers.find(user => user.id === isDetailOpen.id);
            setUserData(selectedUser || {});
        }
    }, [isDetailOpen]);

    const columns = [
        { title: 'Thuộc tính', dataIndex: 'property', key: 'property' },
        { title: 'Giá trị', dataIndex: 'value', key: 'value' },
    ];

    const data = Object.entries(userData).map(([key, value], index) => ({
        key: index,
        property: key.charAt(0).toUpperCase() + key.slice(1),
        value: key === 'avatar' ? <img src={value} alt="Avatar" className="w-20 h-20 rounded-full" /> :
               key === 'status' ? <Tag color={value === 'Hoạt động' ? 'green' : 'red'}>{value}</Tag> :
               value,
    }));

    return (
        <Modal
            title={<h2 className="text-2xl font-bold text-gray-800">Chi tiết người dùng</h2>}
            open={isDetailOpen.isOpen}
            onCancel={() => setIsDetailOpen({ id: 0, isOpen: false })}
            footer={null}
            width={600}
            className="user-detail-modal bg-white"
        >
            <Table 
                columns={columns} 
                dataSource={data} 
                pagination={false}
                className="user-detail-table"
            />
        </Modal>
    );
}

export default UserDetail;