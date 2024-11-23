import { Button, Col, Form, Input, Row, notification, Typography, Select, DatePicker } from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import { useEffect } from 'react';
import config from '../../../config';
import { useCreateVoucher, useShowVoucher, useUpdateVoucher } from '../../../hooks/api/useVoucherApi';
import moment from 'moment'; // Đảm bảo import moment

const { Title } = Typography;

function VoucherFormPage() {
    const navigate = useNavigate();
    let { id } = useParams();
    const { isLoading, data: voucher } = id ? useShowVoucher(id) : { isLoading: null, data: null };

    const [form] = Form.useForm();
    const mutateAdd = useCreateVoucher({
        success: () => {
            notification.success({ message: 'Thêm mới voucher thành công' });
            navigate(config.routes.admin.voucher);
        },
        error: () => {
            notification.error({ message: 'Thêm mới voucher thất bại' });
        },
    });

    const mutateEdit = useUpdateVoucher({
        id,
        success: () => {
            notification.success({ message: 'Cập nhật voucher thành công' });
            navigate(config.routes.admin.voucher);
        },
        error: () => {
            notification.error({ message: 'Cập nhật voucher thất bại' });
        },
    });

    useEffect(() => {
        if (!voucher) return;
        form.setFieldsValue({
            ma_giam_gia: voucher?.data?.ma_giam_gia,
            muc_giam_gia: voucher?.data?.muc_giam_gia,
            mota: voucher?.data?.mota,
            ngay_het_han: voucher?.data?.ngay_het_han ? moment(voucher.data.ngay_het_han) : null,
            so_luong: voucher?.data?.so_luong,
        });
    }, [voucher]);

    const onAddFinish = async (formData) => {
        await mutateAdd.mutateAsync(formData);
    };

    const onEditFinish = async (formData) => {
        await mutateEdit.mutateAsync({ id, body: formData });
    };

    const onFinish = async () => {
        const formData = {
            ma_giam_gia: form.getFieldValue('ma_giam_gia'),
            muc_giam_gia: form.getFieldValue('muc_giam_gia'),
            mota: form.getFieldValue('mota'),
            ngay_het_han: form.getFieldValue('ngay_het_han').format('YYYY-MM-DD'), // Định dạng ngày
            so_luong: form.getFieldValue('so_luong'),
        };

        if (id) {
            await onEditFinish(formData);
        } else {
            await onAddFinish(formData);
        }
    };

    return (
        <div className="form-container" style={{ padding: '20px', maxWidth: '600px', margin: 'auto', backgroundColor: 'white', borderRadius: '8px', boxShadow: '0 2px 10px rgba(0, 0, 0, 0.1)' }}>
            <Title level={2} style={{ textAlign: 'center', marginBottom: '20px' }}>
                {id ? 'Cập nhật thông tin voucher' : 'Thêm voucher mới'}
            </Title>
            <Form form={form} layout="vertical" onFinish={onFinish}>
                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item
                            label="Mã giảm giá"
                            name="ma_giam_gia"
                            rules={[{ required: true, message: 'Nhập mã giảm giá!' }]}
                        >
                            <Input placeholder="Nhập mã giảm giá" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Mức giảm giá (%)"
                            name="muc_giam_gia"
                            rules={[{ required: true, message: 'Nhập mức giảm giá!' }]}
                        >
                            <Input type="number" placeholder="Nhập mức giảm giá" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Mô tả"
                            name="mota"
                        >
                            <Input placeholder="Nhập mô tả" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Ngày hết hạn"
                            name="ngay_het_han"
                            rules={[{ required: true, message: 'Chọn ngày hết hạn!' }]}
                        >
                            <DatePicker style={{ width: '100%' }} />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Số lượng"
                            name="so_luong"
                            rules={[{ required: true, message: 'Nhập số lượng!' }]}
                        >
                            <Input type="number" placeholder="Nhập số lượng" />
                        </Form.Item>
                    </Col>
                </Row>
                <div className="flex justify-between items-center gap-[1rem]">
                    <Button htmlType="reset" style={{ width: '48%', background: 'red' }} onClick={() => navigate(-1)}>Hủy</Button>
                    <Button htmlType="submit" className="bg-blue-500 text-white" style={{ width: '48%' }}>
                        {id ? 'Cập nhật' : 'Thêm mới'}
                    </Button>
                </div>
            </Form>
        </div>
    );
}

export default VoucherFormPage;