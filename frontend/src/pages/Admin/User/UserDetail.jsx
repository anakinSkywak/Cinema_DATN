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
        property: 'Tên',
        value: 'T',
    },
    {
        key: '5',
        property: 'Email',
        value: 't@t.com',
    },
    {
        key: '6',
        property: 'Số điện thoại',
        value: '1243143434',
    },
    {
        key: '7',
        property: 'Zalo',
        value: 'zalo',
    },
    {
        key: '8',
        property: 'Facebook',
        value: 'fb',
    },
    {
        key: '9',
        property: 'Địa chỉ',
        value: 'TPHCM',
    },
    {
        key: '10',
        property: 'Ảnh',
        value: (
            <img
                className="w-20 h-20 rounded-xl"
                src="https://dummyimage.com/138x100.png/dddddd/000000"
            />
        ),
    },
    {
        key: '11',
        property: 'Trạng thái',
        value: (
            <Tag className="w-fit uppercase" color="red">
                Chưa duyệt
            </Tag>
        ),
    },
    {
        key: '12',
        property: 'Xác nhận',
        value: (
            <Tag className="w-fit uppercase" color="green">
                Đã xác nhận
            </Tag>
        ),
    },
];
function UserDetail({ isDetailOpen, setIsDetailOpen }) {
    return (
        <Detail isDetailOpen={isDetailOpen} setIsDetailOpen={setIsDetailOpen} rawData={rawData} />
    );
}

export default UserDetail;