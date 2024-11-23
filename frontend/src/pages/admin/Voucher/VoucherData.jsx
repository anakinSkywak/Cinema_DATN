import { Button, Table, notification, Modal, Typography } from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useDeleteVoucher, useGetVouchers } from '../../../hooks/api/useVoucherApi';
import ConfirmPrompt from '../../../layouts/Admin/components/ConfirmPrompt';

const { Title, Text } = Typography;

const baseColumns = [
    {
        title: 'Mã Giảm Giá',
        dataIndex: 'ma_giam_gia',
        sorter: true,
    },
    {
        title: 'Giá Trị',
        dataIndex: 'muc_giam_gia',
    },
    {
        title: 'Mô Tả',
        dataIndex: 'mota',
    },
    {
        title: 'Hạn Sử Dụng',
        dataIndex: 'ngay_het_han',
    },
    {
        title: 'Số Lượng',
        dataIndex: 'so_luong',
    },
    {
        title: 'Số Lượng Đã Sử Dụng',
        dataIndex: 'so_luong_da_su_dung',
    },
    {
        title: 'Thao Tác',
        dataIndex: 'action',
    },
];

function transformData(dt, navigate, setIsDisableOpen) {
    return dt?.map((item) => {
        return {
            key: item.id,
            ma_giam_gia: item.ma_giam_gia,
            muc_giam_gia: item.muc_giam_gia,
            mota: item.mota,
            ngay_het_han: item.ngay_het_han,
            so_luong: item.so_luong,
            so_luong_da_su_dung: item.so_luong_da_su_dung,
            trang_thai: item.trang_thai,
            action: (
                <div className="action-btn flex gap-3">
                    <Button
                        onClick={() => navigate(`/admin/voucher/update/${item.id}`)}
                    >
                        Sửa
                    </Button>
                    <Button
                        onClick={() => setIsDisableOpen({ id: item.id, isOpen: true })}
                    >
                        Xóa
                    </Button>
                </div>
            ),
        };
    });
}

function VoucherData({ setParams, params }) {
    const [isDisableOpen, setIsDisableOpen] = useState({ id: 0, isOpen: false });
    const navigate = useNavigate();
    const { data, isLoading, refetch } = useGetVouchers();
    const [tdata, setTData] = useState([]);

    useEffect(() => {
        if (isLoading || !data) return;
        setTData(transformData(data?.data, navigate, setIsDisableOpen));
    }, [isLoading, data]);

    const mutationDelete = useDeleteVoucher({
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

    return (
        <div>
            <Table
                loading={isLoading}
                columns={baseColumns}
                dataSource={tdata}
                pagination={{ showSizeChanger: true }}
            />
            {isDisableOpen.id !== 0 && (
                <ConfirmPrompt
                    content="Bạn có muốn xóa voucher này?"
                    isDisableOpen={isDisableOpen}
                    setIsDisableOpen={setIsDisableOpen}
                    handleConfirm={onDelete}
                />
            )}
        </div>
    );
}

export default VoucherData;