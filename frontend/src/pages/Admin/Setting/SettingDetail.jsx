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
        value: 'Trang web',
    },
    {
        key: '5',
        property: 'Nội dung',
        value: 'Thông tin trang web',
    },
    {
        key: '6',
        property: 'Ảnh',
        value: (
            <img
                className="w-20 h-20 rounded-xl"
                src="https://dummyimage.com/138x100.png/dddddd/000000"
            />
        ),
    },
    {
        key: '7',
        property: 'Trạng thái',
        value: (
            <Tag className="w-fit uppercase" color="red">
                Chưa duyệt
            </Tag>
        ),
    },
];
function SettingDetail({ isDetailOpen, setIsDetailOpen }) {
    return (
        <Detail isDetailOpen={isDetailOpen} setIsDetailOpen={setIsDetailOpen} rawData={rawData} />
    );
}

export default SettingDetail;