import { Modal, Form, Input, Select, Button } from 'antd';
import { useState, useEffect } from 'react';
import { generateUserData } from './UserData';

function UserEdit({ isEditOpen, setIsEditOpen }) {
    const [form] = Form.useForm();

    useEffect(() => {
        if (isEditOpen.isOpen) {
            const allUsers = generateUserData();
            const selectedUser = allUsers.find(user => user.id === isEditOpen.id);
            if (selectedUser) {
                form.setFieldsValue(selectedUser);
            }
        }
    }, [isEditOpen, form]);

    const handleOk = () => {
        form.validateFields().then((values) => {
            console.log('Updated values:', values);
            // Here you would typically send the updated data to your backend
            setIsEditOpen({ id: 0, isOpen: false });
        });
    };

    const handleCancel = () => {
        setIsEditOpen({ id: 0, isOpen: false });
    };

    return (
        <Modal  
            title={<h2 className="text-2xl font-bold text-gray-800">Chỉnh sửa người dùng</h2>}
            open={isEditOpen.isOpen}
            onOk={handleOk}
            onCancel={handleCancel}
            footer={[
                <Button key="back" onClick={handleCancel}>
                    Hủy
                </Button>,
                <Button key="submit" type="primary" onClick={handleOk}>
                    Lưu
                </Button>,
            ]}
            className="user-edit-modal bg-white"
        >
            <Form form={form} layout="vertical" className="user-edit-form">
                <Form.Item name="id" hidden>
                    <Input />
                </Form.Item>
                <Form.Item name="name" label="Tên" rules={[{ required: true, message: 'Vui lòng nhập tên' }]}>
                    <Input />
                </Form.Item>
                <Form.Item name="email" label="Email" rules={[{ required: true, type: 'email', message: 'Vui lòng nhập email hợp lệ' }]}>
                    <Input />
                </Form.Item>
                <Form.Item name="role" label="Chức vụ" rules={[{ required: true, message: 'Vui lòng chọn chức vụ' }]}>
                    <Select>
                        <Select.Option value="Admin">Admin</Select.Option>
                        <Select.Option value="Quản lý">Quản lý</Select.Option>
                        <Select.Option value="Nhân viên">Nhân viên</Select.Option>
                    </Select>
                </Form.Item>
                <Form.Item name="status" label="Trạng thái" rules={[{ required: true, message: 'Vui lòng chọn trạng thái' }]}>
                    <Select>
                        <Select.Option value="Hoạt động">Hoạt động</Select.Option>
                        <Select.Option value="Không hoạt động">Không hoạt động</Select.Option>
                    </Select>
                </Form.Item>
            </Form>
        </Modal>
    );
}

export default UserEdit;