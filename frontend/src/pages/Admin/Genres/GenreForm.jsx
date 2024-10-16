import { Button, Col, Form, Input, Row, Select, Upload, notification } from 'antd';
import { PlusOutlined } from '@ant-design/icons';
import { useNavigate, useParams } from 'react-router-dom';
import { faChevronLeft } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useEffect, useRef, useState } from 'react';
import './category.scss';
import config from '../../../config';
import { useCreateGerne, useGetGerne, useUpdateGerne } from '../../../hooks/api/useGenreApi';


function  GenreFormPage() {
    const navigate = useNavigate();
    let { id } = useParams();
    const { isLoading: isLoadingCategory, data: category } = id
        ? useGetGerne(id)
        : { isLoading: null, data: null };


    useEffect(() => {
        if(!category) return;
        console.log("GERNE", category)
        form.setFieldsValue({
            name: category?.data?.ten_loai_phim,
        });
    }, [category]);
    const [form] = Form.useForm();
    const mutateAdd = useCreateGerne({
        success: () => {
            notification.success({
                message: 'Thêm mới thành công',
            });
            navigate(config.routes.admin.genres);
        },
        error: () => {
            notification.error({
                message: 'Thêm mới thất bại',
            });
        },
    });

    const mutateEdit = useUpdateGerne({
        success: () => {
            notification.success({
                message: 'Cập nhật thành công',
            });
            navigate(config.routes.admin.category);
        },
        error: () => {
            notification.error({
                message: 'Cập nhật thất bại',
            });
        },
    });

    const onFinish = async () => {
        if (id) {
            await mutateEdit.mutateAsync({
                id: id,
                body: {
                    ten_loai_phim: form.getFieldValue('name'),
                },
            });
        } else {
            await mutateAdd.mutateAsync({
                ten_loai_phim: form.getFieldValue('name'),
            });
        }
    };

    return (
        <div className="form-container">
            <div className="flex items-center gap-[1rem]">
                <FontAwesomeIcon
                    onClick={() => navigate(config.routes.admin.genres)}
                    className="text-[1.6rem] bg-[--primary-color] p-4 rounded-xl text-white cursor-pointer"
                    icon={faChevronLeft}
                />
                <h1 className="font-bold">
                    {id ? 'Cập nhật thông tin' : 'Thêm thể loại phim'}
                </h1>
            </div>
            <div className="flex items-center justify-start rounded-xl shadow text-[1.6rem] text-black gap-[1rem] bg-white p-7">
                <div className="flex flex-col gap-[1rem]">
                    <p>ID</p>
                    <code className="bg-blue-100 p-2">{category?.data?.id || '_'}</code>
                </div>
            </div>
            <div className="bg-white p-7 mt-5 rounded-xl shadow">
                <Form
                    name="employee-form"
                    layout="vertical"
                    form={form}
                    labelCol={{
                        span: 5,
                    }}
                    onFinish={onFinish}
                >
                    <Row gutter={16}>
                        <Col span={12}>
                            <Form.Item
                                label="Tên danh mục"
                                name="name"
                                rules={[
                                    {
                                        required: true,
                                        message: 'Nhập tên danh mục!',
                                    },
                                ]}
                            >
                                <Input />
                            </Form.Item>
                        </Col>
                    </Row>
                    <div className="flex justify-between items-center gap-[1rem]">
                        <Button htmlType="reset" className="min-w-[10%]">Đặt lại</Button>
                        <Button htmlType="submit" className="bg-blue-500 text-white min-w-[10%]">
                            {id ? 'Cập nhật' : 'Thêm mới'}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    );
}

export default  GenreFormPage;