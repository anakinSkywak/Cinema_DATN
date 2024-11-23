import { Button, Input, Table, notification, Modal, Typography } from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { EyeOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons';
import config from '../../../config';
import { useDeleteFood, useGetFoods } from '../../../hooks/api/useFoodApi';
import ConfirmPrompt from '../../../layouts/Admin/components/ConfirmPrompt';

const { Title, Text } = Typography;

const baseColumns = [
    {
        title: 'Tên món ăn',
        dataIndex: 'ten_do_an',
        sorter: true,
    },
    {
        title: 'Giá',
        dataIndex: 'gia',
    },
    {
        title: 'Ghi chú',
        dataIndex: 'ghi_chu',
    },
    {
        title: 'Trạng thái',
        dataIndex: 'trang_thai',
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
            ten_do_an: item.ten_do_an,
            gia: item.gia,
            ghi_chu: item.ghi_chu,
            trang_thai: (
                <span className={item.trang_thai === 0 ? 'text-green-500' : 'text-red-500'}>
                    {item.trang_thai === 0 ? 'Còn hàng' : 'Hết hàng'}
                </span>
            ),
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
                        onClick={() => navigate(`${config.routes.admin.food}/update/${item.id}`)}
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

function FoodData({ setParams, params }) {
    const [isDisableOpen, setIsDisableOpen] = useState({ id: 0, isOpen: false });
    const [searchValue, setSearchValue] = useState('');
    const [viewData, setViewData] = useState(null);
    const navigate = useNavigate();
    const { data, isLoading, refetch } = useGetFoods();
    const [tdata, setTData] = useState([]);

    useEffect(() => {
        if (isLoading || !data) return;
        let dt = transformData(data?.data, navigate, setIsDisableOpen, setViewData);
        setTData(dt);
    }, [isLoading, data]);

    const mutationDelete = useDeleteFood({
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
        setSearchValue(value);
        const filteredData = data?.data.filter(item =>
            item.ten_do_an.toLowerCase().includes(value.toLowerCase())
        );
        setTData(transformData(filteredData, navigate, setIsDisableOpen, setViewData));
    };

    const handleViewClose = () => {
        setViewData(null);
    };

    return (
        <div className="bg-white text-black p-4 rounded-lg shadow-lg">
            <div className="mb-3 flex items-center">
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
                rowClassName={(record, index) => (index % 2 === 0 ? 'bg-gray-100 hover:bg-gray-200' : 'bg-white hover:bg-gray-200')}
                bordered
                size="middle"
            />
            {isDisableOpen.id !== 0 && (
                <ConfirmPrompt
                    content="Bạn có muốn xóa món ăn này?"
                    isDisableOpen={isDisableOpen}
                    setIsDisableOpen={setIsDisableOpen}
                    handleConfirm={onDelete}
                />
            )}
            <Modal
                title="Chi tiết món ăn"
                visible={!!viewData}
                onCancel={handleViewClose}
                footer={null}
                width={600}
            >
                {viewData && (
                    <div style={{ padding: '20px' }}>
                        <Title level={4}>Thông tin món ăn</Title>
                        <p><strong>Tên món ăn:</strong> <Text>{viewData.ten_do_an}</Text></p>
                        <p><strong>Giá:</strong> <Text>{viewData.gia}</Text></p>
                        <p><strong>Ghi chú:</strong> <Text>{viewData.ghi_chu}</Text></p>
                        <p><strong>Trạng thái:</strong> <Text>{viewData.trang_thai === 0 ? 'Còn hàng' : 'Hết hàng'}</Text></p>
                    </div>
                )}
            </Modal>
        </div>
    );
}

export default FoodData;