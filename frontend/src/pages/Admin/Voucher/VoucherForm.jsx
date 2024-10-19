import { Button, Col, DatePicker, Form, Input, InputNumber, Row, Select, Upload, notification } from 'antd';
import { PlusOutlined } from '@ant-design/icons';
import { useNavigate, useParams } from 'react-router-dom';
import { faChevronLeft } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { useEffect, useRef, useState } from 'react';
import './category.scss';
import config from '../../../config';
import { useCreateVoucher, useGetVoucher, useUpdateVoucher } from '../../../hooks/api/useVoucherApi';
import dayjs from 'dayjs';


function VoucherFormPage() {
    const navigate = useNavigate();
    let { id } = useParams();
    const { isLoading: isLoadingCategory, data: category } = id
        ? useGetVoucher(id)
        : { isLoading: null, data: null };


    useEffect(() => {
        if (!category) return;
        console.log("GERNE", category)
        form.setFieldsValue({
            name: category?.data?.ten_loai_phim,
            ma_giam_gia: category?.data?.ma_giam_gia, 
            muc_giam_gia: category?.data?.muc_giam_gia,
            mota: category?.data?.mota,
            ngay_het_han: category?.data?.ngay_het_han,
            so_luong: category?.data?.so_luong,
            so_luong_da_su_dung: category?.data?.so_luong_da_su_dung,
        });
    }, [category]);
    const [form] = Form.useForm();
    const mutateAdd = useCreateVoucher({
        success: () => {
            notification.success({
                message: 'Thêm mới thành công',
            });
            navigate(config.routes.admin.voucher);
        },
        error: () => {
            notification.error({
                message: 'Thêm mới thất bại',
            });
        },
    });

    const mutateEdit = useUpdateVoucher({
        success: () => {
            notification.success({
                message: 'Cập nhật thành công',
            });
            navigate(config.routes.admin.voucher);
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
                    ma_giam_gia: form.getFieldValue('ma_giam_gia'),
                    muc_giam_gia: form.getFieldValue('muc_giam_gia'),
                    mota: form.getFieldValue('mota'),
                    ngay_het_han: form.getFieldValue('ngay_het_han'),
                    so_luong: form.getFieldValue('so_luong'),
                    so_luong_da_su_dung: form.getFieldValue('so_luong_da_su_dung'),
                },
            });
        } else {
            await mutateAdd.mutateAsync({
                ma_giam_gia: form.getFieldValue('ma_giam_gia'),
                muc_giam_gia: form.getFieldValue('muc_giam_gia'),
                mota: form.getFieldValue('mota'),
                ngay_het_han: form.getFieldValue('ngay_het_han'),
                so_luong: form.getFieldValue('so_luong'),
                so_luong_da_su_dung: form.getFieldValue('so_luong_da_su_dung'),
            });
        }
    };

    return (
        <div className="form-container">
            <div className="flex items-center gap-[1rem]">
                <FontAwesomeIcon
                    onClick={() => navigate(config.routes.admin.voucher)}
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
                    name="discount-form"
                    layout="vertical"
                    form={form}
                    labelCol={{
                        span: 3,
                    }}
                    onFinish={onFinish}
                >
                    <Row gutter={10}>
                        <Col span={12}>
                            <Form.Item
                                label="Mã giảm giá"
                                name="ma_giam_gia"
                                rules={[
                                    {
                                        required: true,
                                        message: 'Nhập mã giảm giá!',
                                    },
                                ]}
                            >
                                <Input />
                            </Form.Item>
                        </Col>
                    </Row>
                    <Row gutter={16}>
                        <Col span={12}>
                            <Form.Item
                                label="Mức giảm giá"
                                name="muc_giam_gia"
                                rules={[
                                    {
                                        required: true,
                                        message: 'Nhập mức giảm giá!',
                                    },
                                ]}
                            >
                                <InputNumber />
                            </Form.Item>
                        </Col>
                    </Row>
                    <Row gutter={16}>
                        <Col span={12}>
                            <Form.Item
                                label="Mô tả"
                                name="mota"
                                rules={[
                                    {
                                        required: true,
                                        message: 'Nhập mô tả!',
                                    },
                                ]}
                            >
                                <Input.TextArea />
                            </Form.Item>
                        </Col>
                    </Row>
                    <Row gutter={16}>
                        <Col span={12}>
                            <Form.Item
                                label="Ngày hết hạn"
                                name="ngay_het_han"
                                rules={[
                                    {
                                        required: true,
                                        message: 'Nhập ngày hết hạn!',
                                    },
                                ]}
                                getValueFromEvent={(e) => e?.format("YYYY-MM-DD")}
                                getValueProps={(e) => ({
                                    value: e ? dayjs(e) : "",
                                })}
                            >
                                <DatePicker
                                    format="YYYY-MM-DD"
                                    disabledDate={(current) => {
                                        let customDate = dayjs().format("YYYY-MM-DD");
                                        return current && current < dayjs(customDate, "YYYY-MM-DD");
                                    }}
                                />
                            </Form.Item>
                        </Col>
                    </Row>
                    <Row gutter={16}>
                        <Col span={12}>
                            <Form.Item
                                label="Số lượng"
                                name="so_luong"
                                rules={[
                                    {
                                        required: true,
                                        message: 'Nhập số lượng!',
                                    },
                                ]}
                            >
                                <InputNumber />
                            </Form.Item>
                        </Col>
                    </Row>
                    <Row gutter={16}>
                        <Col span={12}>
                            <Form.Item
                                label="Số lượng đã sử dụng"
                                name="so_luong_da_su_dung"
                                rules={[
                                    {
                                        required: true,
                                        message: 'Nhập số lượng đã sử dụng!',
                                    },
                                ]}
                            >
                                <InputNumber />
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

export default VoucherFormPage;