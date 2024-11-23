import { Button, Table, notification, Modal, Typography } from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { EyeOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons';
import config from '../../../config';
import { useDeleteShowtime, useShowtimes } from '../../../hooks/api/useShowtimeApi';
import ConfirmPrompt from '../../../layouts/Admin/components/ConfirmPrompt';

const { Title, Text } = Typography;

const baseColumns = [
    {
        title: 'Tên phim',
        dataIndex: 'ten_phim',
        sorter: true,
    },
    {
        title: 'Ngày chiếu',
        dataIndex: 'ngay_chieu',
    },
    {
        title: 'Giờ chiếu',
        dataIndex: 'gio_chieu',
    },
    {
        title: 'Phòng chiếu',
        dataIndex: 'ten_phong_chieu',
    },
    {
        title: 'Thao tác',
        dataIndex: 'action',
    },
];

function transformData(dt, navigate, setIsDisableOpen, setViewData) {
    return dt?.map((item) => {
        return {
            key: item.id,
            ten_phim: item.movie.ten_phim,
            ngay_chieu: item.ngay_chieu,
            gio_chieu: item.gio_chieu,
            ten_phong_chieu: item.room.ten_phong_chieu,
            action: (
                <div className="action-btn flex gap-3">
                    <Button
                        icon={<EyeOutlined />}
                        className="text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white transition"
                        onClick={() => setViewData(item)}
                    >
                        Xem
                    </Button>
                    <Button
                        icon={<EditOutlined />}
                        className="text-green-500 border border-green-500 hover:bg-green-500 hover:text-white transition"
                        onClick={() => navigate(`${config.routes.admin.showTime}/update/${item.id}`)}
                    >
                        Sửa
                    </Button>
                    <Button
                        icon={<DeleteOutlined />}
                        className="text-red-500 border border-red-500 hover:bg-red-500 hover:text-white transition"
                        onClick={() => setIsDisableOpen({ id: item.id, isOpen: true })}
                    >
                        Xóa
                    </Button>
                </div>
            ),
        };
    });
}

function ShowTimeData({ setParams, params }) {
    const [isDisableOpen, setIsDisableOpen] = useState({ id: 0, isOpen: false });
    const [viewData, setViewData] = useState(null);
    const navigate = useNavigate();
    const { data, isLoading, refetch } = useShowtimes();
    const [tdata, setTData] = useState([]);

    useEffect(() => {
        if (isLoading || !data) return;
        let dt = transformData(data?.data, navigate, setIsDisableOpen, setViewData);
        setTData(dt);
    }, [isLoading, data]);

    const mutationDelete = useDeleteShowtime({
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

    const handleViewClose = () => {
        setViewData(null);
    };

    return (
        <div className="bg-white text-black p-4 rounded-lg shadow-lg">
            <Table
                loading={isLoading}
                columns={baseColumns}
                dataSource={tdata}
                pagination={{ showSizeChanger: true }}
                rowClassName={(record, index) => (index % 2 === 0 ? 'bg-gray-100 hover:bg-gray-200' : 'bg-white hover:bg-gray-200')}
                bordered
                size="middle"
            />
            {isDisableOpen.id !== 0 && (
                <ConfirmPrompt
                    content="Bạn có muốn xóa thời gian chiếu này?"
                    isDisableOpen={isDisableOpen}
                    setIsDisableOpen={setIsDisableOpen}
                    handleConfirm={onDelete}
                />
            )}
            <Modal
                title="Chi tiết thời gian chiếu"
                visible={!!viewData}
                onCancel={handleViewClose}
                footer={null}
                width={600}
            >
                {viewData && (
                    <div style={{ padding: '20px' }}>
                        <Title level={4}>Thông tin thời gian chiếu</Title>
                        <p><strong>Tên phim:</strong> <Text>{viewData.movie.ten_phim}</Text></p>
                        <p><strong>Ngày chiếu:</strong> <Text>{viewData.ngay_chieu}</Text></p>
                        <p><strong>Giờ chiếu:</strong> <Text>{viewData.gio_chieu}</Text></p>
                        <p><strong>Phòng chiếu:</strong> <Text>{viewData.room.ten_phong_chieu}</Text></p>
                    </div>
                )}
            </Modal>
        </div>
    );
}

export default ShowTimeData;