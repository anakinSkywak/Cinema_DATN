import { Button, Col, Form, Input, Row, notification, Typography, Select } from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import { useEffect } from 'react';
import config from '../../../config';
import { useCreateFood, useShowFood, useUpdateFood } from '../../../hooks/api/useFoodApi';

const { Title } = Typography;

function FoodFormPage() {
    const navigate = useNavigate();
    let { id } = useParams();
    const { isLoading, data: food } = id ? useShowFood(id) : { isLoading: null, data: null };

    const [form] = Form.useForm();
    const mutateAdd = useCreateFood({
        success: () => {
            notification.success({ message: 'Thêm mới thành công' });
            navigate(config.routes.admin.food);
        },
        error: () => {
            notification.error({ message: 'Thêm mới thất bại' });
        },
    });

    const mutateEdit = useUpdateFood({
        id,
        success: () => {
            notification.success({ message: 'Cập nhật thành công' });
            navigate(config.routes.admin.food);
        },
        error: () => {
            notification.error({ message: 'Cập nhật thất bại' });
        },
    });

    useEffect(() => {
        if (!food) return;
        form.setFieldsValue({
            ten_do_an: food?.data?.ten_do_an,
            gia: food?.data?.gia,
            ghi_chu: food?.data?.ghi_chu,
            trang_thai: food?.data?.trang_thai,
        });
    }, [food]);

    const onAddFinish = async (formData) => {
        await mutateAdd.mutateAsync(formData);
    };

    const onEditFinish = async (formData) => {
        await mutateEdit.mutateAsync({ id, body: formData });
    };

    const onFinish = async () => {
        const formData = {
            ten_do_an: form.getFieldValue('ten_do_an'),
            gia: form.getFieldValue('gia'),
            ghi_chu: form.getFieldValue('ghi_chu'),
            trang_thai: form.getFieldValue('trang_thai'),
        };

        if (id) {
            await onEditFinish(formData);
        } else {
            await onAddFinish(formData);
        }
    };

    return (
        <div className="form-container" style={{ padding: '20px', maxWidth: '600px', margin: 'auto', backgroundColor: 'white', borderRadius: '8px', boxShadow: '0 2px 10px rgba(0, 0, 0, 0.1)' }}> {/* Improved styling */}
            <Title level={2} style={{ textAlign: 'center', marginBottom: '20px' }}>
                {id ? 'Cập nhật thông tin món ăn' : 'Thêm món ăn mới'}
            </Title>
            <Form form={form} layout="vertical" onFinish={onFinish}>
                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item
                            label="Tên món ăn"
                            name="ten_do_an"
                            rules={[{ required: true, message: 'Nhập tên món ăn!' }]}
                        >
                            <Input placeholder="Nhập tên món ăn" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Giá"
                            name="gia"
                            rules={[{ required: true, message: 'Nhập giá món ăn!' }]}
                        >
                            <Input placeholder="Nhập giá món ăn" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Ghi chú"
                            name="ghi_chu"
                        >
                            <Input placeholder="Nhập ghi chú" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Trạng thái"
                            name="trang_thai"
                            rules={[{ required: true, message: 'Nhập trạng thái!' }]}
                        >
                            <Select placeholder="Chọn trạng thái">
                                <Select.Option value={0}>Còn hàng</Select.Option>
                                <Select.Option value={1}>Hết hàng</Select.Option>
                            </Select>
                        </Form.Item>
                    </Col>
                </Row>
                <div className="flex justify-between items-center gap-[1rem]">
                    <Button htmlType="reset" style={{ width: '48%' , background:'red' }} onClick={() => navigate(-1)}>Hủy</Button> {/* Added navigation to previous page */}
                    <Button htmlType="submit" className="bg-blue-500 text-white" style={{ width: '48%' }}>
                        {id ? 'Cập nhật' : 'Thêm mới'}
                    </Button>
                </div>
            </Form>
        </div>
    );
}

export default FoodFormPage;