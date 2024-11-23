import { Tag } from 'antd';

export const baseColumns = [
    {
        title: 'Ảnh đại diện',
        dataIndex: 'avatar',
        key: 'avatar',
        render: (avatar) => <img src={avatar} alt="Avatar" className="w-10 h-10 rounded-full" />,
    },
    {
        title: 'Tên',
        dataIndex: 'name',
        key: 'name',
        sorter: (a, b) => a.name.localeCompare(b.name),
    },
    {
        title: 'Chức vụ',
        dataIndex: 'role',
        key: 'role',
    },
    {
        title: 'Email',
        dataIndex: 'email',
        key: 'email',
    },
    {
        title: 'Trạng thái',
        dataIndex: 'status',
        key: 'status',
        render: (status) => (
            <Tag color={status === 'Hoạt động' ? 'green' : 'red'}>
                {status}
            </Tag>
        ),
    },
];

export function generateUserData() {
    return [
        {
            id: '1',
            avatar: "https://randomuser.me/api/portraits/men/1.jpg",
            name: 'Nguyễn Văn A',
            role: 'Admin',
            email: 'nguyenvana@example.com',
            status: 'Hoạt động',
        },
        {
            id: '2',
            avatar: "https://randomuser.me/api/portraits/women/2.jpg",
            name: 'Trần Thị B',
            role: 'Nhân viên',
            email: 'tranthib@example.com',
            status: 'Không hoạt động',
        },
        {
            id: '3',
            avatar: "https://randomuser.me/api/portraits/men/3.jpg",
            name: 'Lê Văn C',
            role: 'Quản lý',
            email: 'levanc@example.com',
            status: 'Hoạt động',
        },
        {
            id: '4',
            avatar: "https://randomuser.me/api/portraits/women/4.jpg",
            name: 'Phạm Thị D',
            role: 'Nhân viên',
            email: 'phamthid@example.com',
            status: 'Hoạt động',
        },
        {
            id: '5',
            avatar: "https://randomuser.me/api/portraits/men/5.jpg",
            name: 'Hoàng Văn E',
            role: 'Nhân viên',
            email: 'hoangvane@example.com',
            status: 'Không hoạt động',
        },
        {
            id: '6',
            avatar: "https://randomuser.me/api/portraits/women/6.jpg",
            name: 'Ngô Thị F',
            role: 'Quản lý',
            email: 'ngothif@example.com',
            status: 'Hoạt động',
        },
        {
            id: '7',
            avatar: "https://randomuser.me/api/portraits/men/7.jpg",
            name: 'Đặng Văn G',
            role: 'Nhân viên',
            email: 'dangvang@example.com',
            status: 'Hoạt động',
        },
        {
            id: '8',
            avatar: "https://randomuser.me/api/portraits/women/8.jpg",
            name: 'Vũ Thị H',
            role: 'Nhân viên',
            email: 'vuthih@example.com',
            status: 'Không hoạt động',
        },
        {
            id: '9',
            avatar: "https://randomuser.me/api/portraits/men/9.jpg",
            name: 'Bùi Văn I',
            role: 'Admin',
            email: 'buivani@example.com',
            status: 'Hoạt động',
        },
        {
            id: '10',
            avatar: "https://randomuser.me/api/portraits/women/10.jpg",
            name: 'Lý Thị K',
            role: 'Nhân viên',
            email: 'lythik@example.com',
            status: 'Hoạt động',
        },
        {
            id: '11',
            avatar: "https://randomuser.me/api/portraits/women/10.jpg",
            name: 'Lý Thị K',
            role: 'Nhân viên',
            email: 'lythik@example.com',
            status: 'Hoạt động',
        },
    ];
}