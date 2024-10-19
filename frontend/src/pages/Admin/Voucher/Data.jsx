import { faEdit, faEye, faEyeSlash } from '@fortawesome/free-regular-svg-icons';
import { faSearch, faTrash } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Button, Input, Table, Tag, notification } from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import config from '../../../config';
import ConfirmPrompt from '../../../layouts/Admin/components/ConfirmPrompt';
import { useDeleteVoucher, useGetVouchers } from '../../../hooks/api/useVoucherApi';


const baseColumns = [
    {
        title: 'Id',
        dataIndex: 'id',
        sorter: true,
        width: 50,
    },
    {
        title: 'Code giảm giá',
        dataIndex: 'name',
    },
    {
        title: 'Mức giảm giá',
        dataIndex: 'countLevel',
    },
    {
        title: 'Mô tả',
        dataIndex: 'description',
    },
    {
        title: 'Ngày hết hạn',
        dataIndex: 'dateReturn',
    },
    {
        title: 'Số lượng',
        dataIndex: 'qantity',
    },
    {
        title: 'Sử dụng',
        dataIndex: 'used',
    },
    {
        title: 'Thao tác',
        dataIndex: 'action',
    },
];

function transformData(dt, navigate, setIsDetailOpen, setIsDisableOpen) {
    let id = 1; // Initialize the id variable
    return dt?.map((item) => {
        id++; 
        return {
            key: id - 1, // Use the current id as the key
            id: id - 1, //  Use the current id as the id
            name: item.ma_giam_gia,
            countLevel: item.muc_giam_gia,
            description: item.mota,
            dateReturn: item.ngay_het_han,
            qantity: item.so_luong,
            used: item.so_luong_da_su_dung,
            action: (
                <div className="action-btn flex gap-3">
                    <Button
                        className="text-green-500 border border-green-500"
                        onClick={() =>
                            navigate(`${config.routes.admin.voucher}/update/${item.id}`)
                        }
                    >
                        <FontAwesomeIcon icon={faEdit} />
                    </Button>
                    <Button
                        className={'text-red-500 border border-red-500'}
                        onClick={() => setIsDisableOpen({ id: item.id, isOpen: true })}
                    >
                        <FontAwesomeIcon icon={faTrash} />
                    </Button>
                </div>
            ),
        };
        // Increment the id for the next item
    });
}

function Data({ setProductCategoryIds, params, setParams }) {
    const [isDetailOpen, setIsDetailOpen] = useState({
        id: 0,
        isOpen: false,
    });
    const [isDisableOpen, setIsDisableOpen] = useState({
        id: 0,
        isOpen: false,
    });
    const navigate = useNavigate();
    const { data, isLoading, refetch } = useGetVouchers();
    const [tdata, setTData] = useState([]);
    const [tableParams, setTableParams] = useState({
        pagination: {
            current: 1,
            pageSize: 10,
            total: data?.totalElements,
        },
    });

    useEffect(() => {
        if (isLoading || !data) return;
        let dt = transformData(data?.data, navigate, setIsDetailOpen, setIsDisableOpen);
        setTData(dt);
        setTableParams({
            ...tableParams,
            pagination: {
                ...tableParams.pagination,
                total: data?.totalElements,
            },
        });
    }, [isLoading, data]);

    const onSearch = (value) => {
        const dt = transformData(data?.data, navigate, setIsDetailOpen, setIsDisableOpen);
        if (!value) return;
        const filterTable = dt.filter((o) =>
            Object.keys(o).some((k) => String(o[k]).toLowerCase().includes(value.toLowerCase())),
        );

        setTData(filterTable);
    };

    const handleTableChange = (pagination, filters, sorter) => {
        setTableParams({
            ...tableParams,
            pagination,
            ...sorter,
        });
        setParams({
            ...params,
            pageNo: pagination.current,
            pageSize: pagination.pageSize,
        });
    };

    const mutationDelete = useDeleteVoucher({
        success: () => {
            setIsDisableOpen({ ...isDisableOpen, isOpen: false });
            notification.success({
                message: 'Thành công',
            });
            refetch();
        },
        error: (err) => {
            notification.error({
                message: 'Thất bại',
            });
        },
        obj: {
            id: isDisableOpen.id,
            params: params,
        },
    });

    const onDelete = async (id) => {
        await mutationDelete.mutateAsync(id);
    };

    return (
        <div>
            <div className="p-4 bg-white mb-3 flex items-center rounded-lg">
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
                pagination={{ ...tableParams.pagination, showSizeChanger: true }}
                onChange={handleTableChange}
            />

            {/* {isDetailOpen.id !== 0 && (
                <CategoryDetail isDetailOpen={isDetailOpen} setIsDetailOpen={setIsDetailOpen} />
            )} */}

            {isDisableOpen.id !== 0 && (
                <ConfirmPrompt
                    content="Bạn có muốn ẩn khuyễn mãi này ?"
                    isDisableOpen={isDisableOpen}
                    setIsDisableOpen={setIsDisableOpen}
                    handleConfirm={onDelete}
                />
            )}
        </div>
    );
}

export default Data;
