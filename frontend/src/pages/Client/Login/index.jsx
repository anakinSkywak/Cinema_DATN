import { Button, Form, Input, notification } from 'antd';
import FormItem from 'antd/es/form/FormItem';
import { useLogin } from '../../../hooks/api/useAuthApi';
import config from '../../../config';
import { useState } from 'react';
import {saveToken, getRoles, isTokenStoraged} from '../../../utils/storage';
import { NavLink, Navigate, useNavigate } from 'react-router-dom';

const LoginModal = ({ closeModal, openRegisterModal }) => {
    const [processing, setProcessing] = useState(false);
    const [form] = Form.useForm();
    const navigate = useNavigate();

    const handleToken = (token) => {
        saveToken(token);
        let url = '/';
        notification.success({
            message: 'Đăng nhập thành công',
            description: 'Chào mừng bạn đến với hệ thống của chúng tôi',
        });
        navigate(url);
    };

    const mutationLogin = useLogin({
        success: (data) => {
            handleToken(data);
        },
        error: (err) => {
            console.log(err)
            let description = 'Không thể đăng nhập, vui lòng thử lại.';
            let detail = err?.response?.data?.message;
            if (detail) {
                description = detail;
            }
            notification.error({
                message: 'Đăng nhập thất bại',
                description,
            });
            
        },
        mutate: (data) => {
            setProcessing(true);
        },
        settled: (data) => {
            setProcessing(false);
        },
    });
    const onLogin = async () => {
        await mutationLogin.mutateAsync({
            email: form.getFieldValue('email'),
            password: form.getFieldValue('password'),
        });
    };

    
    if (isTokenStoraged()) {
        let url = config.routes.web.home;
        return <Navigate to={url} replace />;
    }
    return (
        <div className="fixed inset-0 bg-black bg-opacity-65 flex justify-center items-center z-50" id="loginModal">
            <div className="bg-black bg-opacity-90 p-8 rounded-lg shadow-lg w-96 relative">
                <button className="absolute top-2 right-2 text-white" onClick={closeModal}>
                    X
                </button>
                <h2 className="text-white text-2xl mb-6">
                    Đăng nhập
                </h2>
                <Form form={form} onFinish={onLogin}>
                    <div className="mb-24">
                        <FormItem
                            name={'email'}
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
                                className="w-full p-2 rounded-lg border border-gray-300"
                                id="email"
                                placeholder="Email"
                                type="email"
                            />
                        </FormItem>
                    </div>
                    <div className="mb-24">
                        <FormItem
                            name={'password'}
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
                                className="w-full p-2 rounded-lg border border-gray-300"
                                id="password"
                                placeholder="Mật khẩu"
                                type="password"
                            />
                        </FormItem>
                    </div>
                    <div className="flex justify-between items-center mb-6">
                        <a className="text-red-500" href="#">
                            Quên mật khẩu?
                        </a>
                    </div>
                    <button
                        className="w-full bg-red-500 text-white py-2 rounded-lg hover-zoom"
                        type="submit"
                    >
                        Đăng Nhập
                    </button>
                </Form>
                <p className="text-white mt-4 text-center">
                    Bạn chưa có tài khoản?
                    <a className="text-red-500" href="#" onClick={openRegisterModal}>
                        Đăng kí
                    </a>
                </p>
            </div>
        </div>
    )
}

export default LoginModal;