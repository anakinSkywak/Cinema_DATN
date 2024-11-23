import { Button, Input, Table, notification, Modal, Typography } from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { EyeOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons';
import config from '../../../config';
import { useDeleteSeat, useGetSeats, useGetAddSeat } from '../../../hooks/api/useSeatApi'; 
import ConfirmPrompt from '../../../layouts/Admin/components/ConfirmPrompt';

const { Title, Text } = Typography;

const baseColumns = [
    {
        title: 'Số ghế ngồi',
        dataIndex: 'so_ghe_ngoi',
    },
    {
        title: 'Loại ghế ngồi',
        dataIndex: 'loai_ghe_ngoi',
    },
    {
        title: 'Giá ghế',
        dataIndex: 'gia_ghe',
    },
    {
        title: 'Tên phòng chiếu',
        dataIndex: 'ten_phong_chieu',
    },
    {
        title: 'Thao tác',
        dataIndex: 'action',
    },
];

function transformData(dt, roomData, navigate, setIsDisableOpen, setViewData) {
    return dt?.map((item) => {
        const room = Array.isArray(roomData) ? roomData.find(r => r.id === item.room_id) : null;
        return {
            key: item.id,
            id: item.id,
            so_ghe_ngoi: item.so_ghe_ngoi,
            loai_ghe_ngoi: item.loai_ghe_ngoi,
            gia_ghe: item.gia_ghe,
            room_id: item.room_id,
            ten_phong_chieu: room ? room.ten_phong_chieu : 'Không xác định', 
            action: (
                <div className="action-btn flex gap-3">
                    <Button
                        icon={<EditOutlined />}
                        className="text-green-500 border border-green-500"
                        onClick={() => navigate(`${config.routes.admin.seat}/update/${item.id}`)}
                    >
                        Sửa
                    </Button>
                    <Button
                        icon={<DeleteOutlined />}
                        className={'text-red-500 border border-red-500'}
                        onClick={() => setIsDisableOpen({ id: item.id, isOpen: true })}
                    >
                        Xóa
                    </Button>
                </div>
            ),
        };
    });
}

function SeatData({ setParams, params }) {
    const [isDisableOpen, setIsDisableOpen] = useState({ id: 0, isOpen: false });
    const [viewData, setViewData] = useState(null); // State for view data
    const navigate = useNavigate();
    const { data: seatData, isLoading, refetch } = useGetSeats();
    const { data: roomDataResponse } = useGetAddSeat();
    const roomData = roomDataResponse?.data || [];
    const [tdata, setTData] = useState([]);

    useEffect(() => {
        if (isLoading || !seatData) return;
        let dt = transformData(seatData?.data, roomData, navigate, setIsDisableOpen, setViewData);
        setTData(dt);
    }, [isLoading, seatData, roomData]);

    const mutationDelete = useDeleteSeat({
        success: () => {
            setIsDisableOpen({ ...isDisableOpen, isOpen: false });
            notification.success({ message: 'Xóa thành công' });
            refetch();
        },
        error: () => {
            notification.error({ message: 'Xóa thất bại' });
        },
        obj: { id: isDisableOpen.id },
    });

    const onDelete = async (id) => {
        await mutationDelete.mutateAsync(id);
    };

    const onSearch = (value) => {
        const filteredData = seatData.data.filter((item) =>
            item.ten_phong_chieu.toLowerCase().includes(value.toLowerCase())
        );
        setTData(transformData(filteredData, roomData, navigate, setIsDisableOpen, setViewData));
    };

    const handleViewClose = () => {
        setViewData(null);
    };

    return (
        <div className="bg-white text-black p-4 rounded-lg shadow-lg">
            <div className="p-4 mb-3 flex items-center rounded-lg">
                <Input.Search
                    className="xl:w-1/4 md:w-1/2"
                    allowClear
                    enterButton
                    placeholder="Nhập từ khoá tìm kiếm"
                    onSearch={onSearch}
                />
            </div>
            <Table
                loading={isLoading}
                columns={baseColumns}
                dataSource={tdata}
                pagination={{ showSizeChanger: true }}
            />
            {isDisableOpen.isOpen && (
                <ConfirmPrompt
                    content="Bạn có muốn xóa ghế này?"
                    isDisableOpen={isDisableOpen}
                    setIsDisableOpen={setIsDisableOpen}
                    handleConfirm={onDelete}
                />
            )}
            <Modal
                title="Chi tiết ghế"
                visible={!!viewData}
                onCancel={handleViewClose}
                footer={null}
                width={600}
            >
                {viewData && (
                    <div style={{ padding: '20px' }}>
                        <Title level={4}>Thông tin ghế</Title>
                        <p><strong>Số ghế ngồi:</strong> <Text>{viewData.so_ghe_ngoi}</Text></p>
                        <p><strong>Loại ghế ngồi:</strong> <Text>{viewData.loai_ghe_ngoi}</Text></p>
                        <p><strong>Giá ghế:</strong> <Text>{viewData.gia_ghe}</Text></p>
                        <p><strong>Tên phòng chiếu:</strong> <Text>{viewData.ten_phong_chieu}</Text></p>
                    </div>
                )}
            </Modal>
        </div>
    );
}

export default SeatData;