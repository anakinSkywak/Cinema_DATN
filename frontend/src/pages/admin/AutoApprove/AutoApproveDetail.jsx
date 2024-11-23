import { Button, Modal, Table, Tag } from 'antd';
import Detail from '../../../layouts/Admin/components/Detail';
const rawData = [
    {
        key: '1',
        property: 'ID',
        value: '1',
    },
    {
        key: '2',
        property: 'Ngày tạo',
        value: new Date().toLocaleString(),
    },
    {
        key: '3',
        property: 'Ngày cập nhật',
        value: new Date().toLocaleString(),
    },
    {
        key: '4',
        property: 'Tiêu đề',
        value: 'Bán đồ gia dụng',
    },
    {
        key: '5',
        property: 'Nội dung',
        value: 'Cần bán',
    },
    {
        key: '5',
        property: 'Phân loại',
        value: 'Đồ gia dụng',
    },
    {
        key: '7',
        property: 'Ảnh',
        value: (
            <img
                className="w-20 h-20 rounded-xl"
                src="https://dummyimage.com/138x100.png/dddddd/000000"
            />
        ),
    },
    {
        key: '8',
        property: 'Trạng thái',
        value: (
            <Tag className="w-fit uppercase" color="red">
                Chưa duyệt
            </Tag>
        ),
    },
    {
        key: '9',
        property: 'Hoàn thành',
        value: (
            <Tag className="w-fit uppercase" color="green">
                Hoàn thành
            </Tag>
        ),
    },
];
function AutoApproveDetail({ isDetailOpen, setIsDetailOpen }) {
    return (
        <Detail isDetailOpen={isDetailOpen} setIsDetailOpen={setIsDetailOpen} rawData={rawData} />
    );
}

export default AutoApproveDetail;