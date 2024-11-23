import { Button, Input, Table, notification, Modal, Card, Row, Col } from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import config from '../../../config';
import { useDeleteRoom, useGetRooms, useSeatAllRoom } from '../../../hooks/api/useRoomApi'; // Removed useEnableMaintenanceSeat and useDisableMaintenanceSeat
import ConfirmPrompt from '../../../layouts/Admin/components/ConfirmPrompt';
import { CloseCircleOutlined, EditOutlined, EyeOutlined, DeleteOutlined } from '@ant-design/icons';
import './RoomData.css';

const baseColumns = [
    {
        title: 'Tên phòng chiếu',
        dataIndex: 'ten_phong_chieu',
    },
    {
        title: 'Thao tác',
        dataIndex: 'action',
        render: (text) => <div style={{ display: 'flex', gap: '8px' }}>{text}</div>,
    },
];

function transformData(dt, navigate, setIsDisableOpen, showSeats) {
    return dt?.map((item) => {
        return {
            key: item.id,
            id: item.id,
            ten_phong_chieu: item.ten_phong_chieu,
            action: (
                <div className="action-btn flex gap-3">
                    <Button
                        type="primary"
                        icon={<EditOutlined />}
                        onClick={() => navigate(`${config.routes.admin.room}/update/${item.id}`)}
                    >
                        Sửa
                    </Button>
                    <Button
                        type="default"
                        icon={<EyeOutlined />}
                        onClick={() => showSeats(item.id)}
                    >
                        Xem ghế
                    </Button>
                    <Button
                        type="danger"
                        icon={<DeleteOutlined />}
                        onClick={() => setIsDisableOpen({ id: item.id, isOpen: true })}
                    >
                        Xóa
                    </Button>
                </div>
            ),
        };
    });
}

function RoomData({ setParams, params }) {
    const [isDisableOpen, setIsDisableOpen] = useState({ id: 0, isOpen: false });
    const [isModalVisible, setIsModalVisible] = useState(false);
    const [selectedRoomId, setSelectedRoomId] = useState(null);
    const navigate = useNavigate();
    const { data, isLoading, refetch } = useGetRooms();
    const [tdata, setTData] = useState([]);

    useEffect(() => {
        if (isLoading || !data) return;
        const dt = transformData(data.data, navigate, setIsDisableOpen, showSeats);
        setTData(dt);
    }, [isLoading, data]);

    const mutationDelete = useDeleteRoom({
        success: () => {
            setIsDisableOpen({ ...isDisableOpen, isOpen: false });
            notification.success({ message: 'Xóa phòng thành công' });
            refetch();
        },
        error: () => {
            notification.error({ message: 'Xóa phòng thất bại' });
        },
        obj: { id: isDisableOpen.id },
    });

    const onDelete = async () => {
        await mutationDelete.mutateAsync(isDisableOpen.id);
    };

    const onSearch = (value) => {
        const filteredData = data.data.filter((item) =>
            item.ten_phong_chieu.toLowerCase().includes(value.toLowerCase())
        );
        setTData(transformData(filteredData, navigate, setIsDisableOpen, showSeats));
    };

    const showSeats = (id) => {
        setSelectedRoomId(id);
        setIsModalVisible(true);
    };

    const handleModalClose = () => {
        setIsModalVisible(false);
        setSelectedRoomId(null);
    };
    
    const handleMaintenance = async (seat) => {
        const action = seat.trang_thai === 2 ? 'tắt bảo trì' : 'bảo trì';
        Modal.confirm({
            title: `Xác nhận ${action} ghế`,
            content: `Bạn có muốn ${action} ghế ${seat.so_ghe_ngoi}?`,
            onOk: async () => {
                try {
                    // Xây dựng endpoint dựa trên trạng thái hiện tại của ghế
                    const endpoint = seat.trang_thai === 2 
                        ? `http://127.0.0.1:8000/api/tatbaoTriSeat/${seat.id}` // Tắt bảo trì
                        : `http://127.0.0.1:8000/api/baoTriSeat/${seat.id}`; // Bật bảo trì

                    const response = await fetch(endpoint, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Phản hồi mạng không hợp lệ');
                    }

                    const responseData = await response.json();

                    // Cập nhật trạng thái ghế
                    const newStatus = seat.trang_thai === 2 ? 0 : 2; // 0: có thể thuê, 2: đang bảo trì
                    notification.success({ message: responseData.message });
                    // Cập nhật dữ liệu ghế
                    setTData(prevData => prevData.map(item => 
                        item.id === seat.id ? { ...item, trang_thai: newStatus } : item
                    ));
                    handleModalClose(); // Đóng modal sau khi thực hiện hành động
                } catch (error) {
                    notification.error({ message: `Có lỗi xảy ra: ${error.message}` });
                }
            },
            onCancel() {
                // Xử lý hành động hủy nếu cần
            },
        });
    };

    return (
        <Card className="bg-white text-black p-4 rounded-lg shadow-lg">
            <Row gutter={16} className="mb-3">
                <Col span={24}>
                    <Input.Search
                        allowClear
                        enterButton
                        placeholder="Nhập từ khoá tìm kiếm"
                        onSearch={onSearch}
                    />
                </Col>
            </Row>
            <Table
                loading={isLoading}
                columns={baseColumns}
                dataSource={tdata}
                rowKey="key"
                pagination={{ pageSize: 5 }}
            />
            {isDisableOpen.isOpen && (
                <ConfirmPrompt
                    content="Bạn có muốn xóa phòng này?"
                    isDisableOpen={isDisableOpen}
                    setIsDisableOpen={setIsDisableOpen}
                    handleConfirm={onDelete}
                />
            )}
            <Modal
                open={isModalVisible}
                onCancel={handleModalClose}
                footer={null}
                width={900}
                style={{ 
                    top: 90,
                    display: 'flex', 
                    justifyContent: 'center', 
                    alignItems: 'center', 
                    maxHeight: '80vh', 
                    overflowY: 'auto', 
                    padding: '20px', 
                }}
            >
                <div style={{ width: '100%', textAlign: 'center', padding: '20px' }}>
                    <SeatLayout roomId={selectedRoomId} onClose={handleModalClose} handleMaintenance={handleMaintenance} />
                    <div className="legend">
                        <div className="legend-item">
                            <div className="seat selected"></div>
                            <span>Ghế đang bảo trì</span>
                        </div>
                        <div className="legend-item">
                            <div className="seat regular"></div>
                            <span>Ghế thường</span>
                        </div>
                        <div className="legend-item">
                            <div className="seat vip"></div>
                            <span>Ghế VIP</span>
                        </div>
                        <div className="legend-item">
                            <div className="seat double"></div>
                            <span>Ghế đôi</span>
                        </div>
                    </div>
                </div>
            </Modal>
        </Card>
    );
}

const SeatLayout = ({ roomId, onClose, handleMaintenance }) => {
    const { data, isLoading, error } = useSeatAllRoom(roomId);

    useEffect(() => {
        if (error) {
            notification.error({ message: 'Failed to load seats' });
        }
    }, [error]);

    if (isLoading) return <div>Loading...</div>;

    const seats = data?.data || [];

    const getSeatClass = (loai_ghe_ngoi, trang_thai) => {
        if (trang_thai === 2) return 'selected'; // 2: Đang bảo trì (màu xanh)
        if (loai_ghe_ngoi === 'VIP') return 'vip'; // Ghế VIP
        if (loai_ghe_ngoi === 'Đôi') return 'double'; // Ghế đôi
        return 'regular'; // Ghế thường
    };

    const rows = {};
    seats.forEach(seat => {
        const row = seat.so_ghe_ngoi.charAt(0); 
        if (!rows[row]) rows[row] = [];
        rows[row].push(seat);
    });

    return (
        <div className="seat-layout">
            {Object.keys(rows).map(row => (
                <div key={row} className="seat-row">
                    {rows[row].map(seat => (
                        <div 
                            key={seat.id} 
                            className={`seat ${getSeatClass(seat.loai_ghe_ngoi, seat.trang_thai)}`} 
                            onClick={() => handleMaintenance(seat)}
                        >
                            {seat.trang_thai === 2 ? (
                                <CloseCircleOutlined style={{ color: 'blue', fontSize: '24px' }} />
                            ) : (
                                seat.so_ghe_ngoi
                            )}
                        </div>
                    ))}
                </div>
            ))}
        </div>
    );
};

export default RoomData;