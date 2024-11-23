import { NavLink, Navigate, useNavigate } from 'react-router-dom';
import config from '../../../config';
import { Button, Form, Input, Select, notification } from 'antd';
import FormItem from 'antd/es/form/FormItem';
import { useRegister } from '../../../hooks/api/useAuthApi';
import { useState } from 'react';
import { isTokenStoraged } from '../../../utils/storage';

const RegisterModal = ({ closeModal, openLoginModal }) => {
    const navigate = useNavigate();
    const [processing, setProcessing] = useState(false);
    const [form] = Form.useForm();

    const mutationRegister = useRegister({
        success: (data) => {
            notification.success({
                message: 'Đăng ký thành công'
            });
            openLoginModal();
        },
        error: (err) => {
            let description = 'Có lỗi xảy ra khi đăng ký, vui lòng thử lại sau';
            let detail = err?.response?.data?.message;
            if (detail) {
                description = err?.response?.data?.message;
            }
            notification.error({
                message: 'Đăng ký thất bại',
                description: err?.response?.data?.message,
            });
        },
        mutate: () => {
            setProcessing(true);
        },
        settled: () => {
            setProcessing(false);
        },
    });

    const onRegister = async () => {
        await mutationRegister.mutateAsync({
            ho_ten: form.getFieldValue('fullName'),
            gioi_tinh: form.getFieldValue('gender'),
            so_dien_thoai: form.getFieldValue('phone'),
            email: form.getFieldValue('email'),
            password: form.getFieldValue('password'),
            password_confirmation: form.getFieldValue('confirmPassword'),
            vai_tro: 'user',
        });
    };

    if (isTokenStoraged()) {
        let url = config.routes.web.home;
        return <Navigate to={url} replace />;
    }

    return (
        <div className="fixed inset-0 bg-black bg-opacity-65 flex justify-center items-center z-50 mt-12" id="registerModal">
            <div className="bg-black bg-opacity-90 p-8 rounded-lg shadow-lg w-120 relative">
                <button className="absolute top-2 right-2 text-white" onClick={closeModal}>
                    X
                </button>
                <h2 className="text-white text-2xl mb-2">
                    Đăng kí
                </h2>

                <Form form={form} onFinish={onRegister}>
                    <div className="mb-14">
                        <FormItem
                            name="fullName"
                            layout="vertical"
                            label={<label style={{ color: "white" }}>Họ và tên</label>}
                            rules={[
                                {
                                    required: true,
                                    message: "Vui lòng nhập họ và tên."
                                },
                            ]}
                        >
                            <Input
                                className="p-2 rounded-lg border border-gray-300"
                                placeholder="Họ và tên"
                                type="text"
                            />
                        </FormItem>
                    </div>

                    <div className="mb-20">
                        <FormItem
                            name="email"
                            layout="vertical"
                            label={<label style={{ color: "white" }}>Email</label>}
                            rules={[
                                {
                                    type: 'email',
                                    message: 'Email không hợp lý!',
                                },
                                {
                                    required: true,
                                    message: "Vui lòng nhập email."
                                },
                            ]}
                        >
                            <Input
                                className="p-2 w-full rounded border border-gray-300"
                                placeholder="abc-example@gmail.com"
                                type="email"
                            />
                        </FormItem>
                    </div>

                    <div className="grid grid-cols-2 gap-4 mb-16">
                        <FormItem
                            name="phone"
                            layout="vertical"
                            label={<label style={{ color: "white" }}>Số điện thoại</label>}
                            rules={[
                                {
                                    required: true,
                                    message: "Vui lòng nhập số điện thoại."
                                },
                            ]}
                        >
                            <Input
                                className="p-2 rounded-lg border border-gray-300"
                                placeholder="Số điện thoại"
                                type="text"
                            />
                        </FormItem>
                        <FormItem
                            name="gender"
                            layout="vertical"
                            label={<label style={{ color: "white" }}>Giới tính</label>}
                            rules={[
                                {
                                    required: true,
                                    message: "Vui lòng chọn giới tính."
                                },
                            ]}
                        >
                            <Select
                                className="w-full"
                                placeholder="Chọn giới tính"
                            >
                                <Select.Option value="nam">Nam</Select.Option>
                                <Select.Option value="nu">Nữ</Select.Option>
                            </Select>
                        </FormItem>
                    </div>
                    <div className="grid grid-cols-2 gap-4 mb-16">
                        <FormItem
                            name="password"
                            layout="vertical"
                            label={<label style={{ color: "white" }}>Mật khẩu</label>}
                            rules={[
                                {
                                    required: true,
                                    message: "Vui lòng nhập mật khẩu."
                                },
                            ]}
                        >
                            <Input.Password
                                className="p-2 rounded-lg border border-gray-300"
                                placeholder="Mật khẩu"
                                type="password"
                            />
                        </FormItem>

                        <FormItem
                            name="confirmPassword"
                            layout="vertical"
                            label={<label style={{ color: "white" }}>Xác nhận mật khẩu</label>}
                            rules={[
                                {
                                    required: true,
                                    message: "Vui lòng nhập xác nhận mật khẩu."
                                },
                                ({ getFieldValue }) => ({
                                    validator(_, value) {
                                        if (!value || getFieldValue('password') === value) {
                                            return Promise.resolve();
                                        }
                                        return Promise.reject(new Error('Mật khẩu xác nhận chưa đúng'));
                                    },
                                }),
                            ]}
                        >
                            <Input.Password
                                className="p-2 rounded-lg border border-gray-300"
                                placeholder="Xác minh mật khẩu"
                                type="password"
                            />
                        </FormItem>
                    </div>
                    <button
                        className="w-full bg-red-600 text-white p-2 rounded-lg hover-zoom"
                        type="primary"
                        htmlType="submit"
                    >
                        Đăng ký
                    </button>
                </Form>
                <p className="text-white mt-4 text-center">
                    Bạn đã có tài khoản?
                    <a className="text-red-500" href="#" onClick={openLoginModal}>
                        Đăng nhập
                    </a>
                </p>
            </div>
        </div>
    );
}

export default RegisterModal;