import { Button, Input, Table, notification, Modal, Typography } from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { EyeOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons';
import config from '../../../config';
import { useDeleteMovie, useGetMovies } from '../../../hooks/api/useMovieApi';
import ConfirmPrompt from '../../../layouts/Admin/components/ConfirmPrompt';

const { Title, Text } = Typography;

const baseColumns = [
    {
        title: 'Ảnh', 
        dataIndex: 'anh_phim',
        render: (text) => <img src={`http://localhost:8000${text}`} alt="Movie Poster" style={{ width: '100px', borderRadius: '8px' }} />, // Render the image
    },
    {
        title: 'Tên Phim',
        dataIndex: 'ten_phim',
    },
    {
        title: 'Đạo Diễn',
        dataIndex: 'dao_dien',
    },
    {
        title: 'Diễn Viên',
        dataIndex: 'dien_vien',
    },
    {
        title: 'Giá Vé',
        dataIndex: 'gia_ve',
    },
    {
        title: 'Thao Tác',
        dataIndex: 'action',
        render: (text) => <div style={{ display: 'flex', gap: '8px' }}>{text}</div>,
    },
];

function transformData(dt, navigate, setIsDisableOpen, setViewData) {
    return dt?.map((item) => {
        return {
            ten_phim: item.ten_phim,
            dao_dien: item.dao_dien,
            dien_vien: item.dien_vien,
            gia_ve: item.gia_ve,
            anh_phim: item.anh_phim,
            action: (
                <div className="action-btn flex gap-3">
                    <Button
                        icon={<EyeOutlined />}
                        className="text-blue-500 border border-blue-500 hover:bg-blue-500 hover:text-white transition"
                        onClick={() => setViewData(item)}
                    />
                    <Button
                        icon={<EditOutlined />}
                        className="text-green-500 border border-green-500 hover:bg-green-500 hover:text-white transition"
                        onClick={() => navigate(`${config.routes.admin.movies}/update/${item.id}`)}
                    />
                    <Button
                        icon={<DeleteOutlined />}
                        className="text-red-500 border border-red-500 hover:bg-red-500 hover:text-white transition"
                        onClick={() => setIsDisableOpen({ id: item.id, isOpen: true })}
                    />
                </div>
            ),
        };
    });
}

function MovieData({ setParams, params }) {
    const [isDisableOpen, setIsDisableOpen] = useState({ id: 0, isOpen: false });
    const [viewData, setViewData] = useState(null);
    const navigate = useNavigate();
    const { data, isLoading, refetch } = useGetMovies();
    const [tdata, setTData] = useState([]);

    useEffect(() => {
        if (isLoading || !data) return;
        const dt = transformData(data.data, navigate, setIsDisableOpen, setViewData);
        setTData(dt);
    }, [isLoading, data]);

    const mutationDelete = useDeleteMovie({
        success: () => {
            setIsDisableOpen({ ...isDisableOpen, isOpen: false });
            notification.success({ message: 'Xóa phim thành công' });
            refetch();
        },
        error: () => {
            notification.error({ message: 'Xóa phim thất bại' });
        },
        obj: { id: isDisableOpen.id },
    });

    const onDelete = async () => {
        await mutationDelete.mutateAsync(isDisableOpen.id);
    };

    const onSearch = (value) => {
        const filteredData = data.data.filter((item) =>
            item.ten_phim.toLowerCase().includes(value.toLowerCase())
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
                rowKey="key"
                pagination={{ showSizeChanger: true }}
                rowClassName={(record, index) => (index % 2 === 0 ? 'bg-gray-100 hover:bg-gray-200' : 'bg-white hover:bg-gray-200')}
                bordered
                size="middle"
            />
            {isDisableOpen.isOpen && (
                <ConfirmPrompt
                    content="Bạn có muốn xóa phim này?"
                    isDisableOpen={isDisableOpen}
                    setIsDisableOpen={setIsDisableOpen}
                    handleConfirm={onDelete}
                />
            )}
            <Modal
    title="Chi tiết phim"
    visible={!!viewData}
    onCancel={handleViewClose}
    footer={null}
    width={600}
>
    {viewData && (
        <div style={{ padding: '20px', textAlign: 'center' }}> 
            {viewData.anh_phim && ( // Check if the image URL exists
                <img src={`http://localhost:8000${viewData.anh_phim}`} alt="Movie Poster" style={{ width: '100%', borderRadius: '8px', marginBottom: '20px' }} />
            )}
            <Title level={4}>Thông tin phim</Title>
            <p><strong>Tên phim:</strong> <Text>{viewData.ten_phim}</Text></p>
            <p><strong>Đạo diễn:</strong> <Text>{viewData.dao_dien}</Text></p>
            <p><strong>Diễn viên:</strong> <Text>{viewData.dien_vien}</Text></p>
            <p><strong>Giá vé:</strong> <Text>{viewData.gia_ve}</Text></p>
        </div>
    )}
</Modal>
        </div>
    );
}

export default MovieData;