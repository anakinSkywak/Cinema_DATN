import { Button, Col, Form, Input, Row, notification, Typography, Select } from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import { useEffect } from 'react';
import config from '../../../config';
import { useCreateSeat, useGetSeatById, useUpdateSeat, useGetAddSeat } from '../../../hooks/api/useSeatApi';

const { Title } = Typography;
const { Option } = Select;

function SeatFormPage() {
    const navigate = useNavigate();
    let { id } = useParams();
    const { isLoading: loadingSeat, data: seat } = id ? useGetSeatById(id) : { isLoading: null, data: null };
    const { data: roomDataResponse, isLoading: loadingRooms } = useGetAddSeat();
    const roomData = roomDataResponse?.data || [];

    const [form] = Form.useForm();
    const mutateAdd = useCreateSeat({
        success: () => {
            notification.success({ message: 'Thêm mới thành công' });
            navigate(config.routes.admin.seat);
        },
        error: () => {
            notification.error({ message: 'Thêm mới thất bại' });
        },
    });

    const mutateEdit = useUpdateSeat({
        id,
        success: () => {
            notification.success({ message: 'Cập nhật thành công' });
            navigate(config.routes.admin.seat);
        },
        error: () => {
            notification.error({ message: 'Cập nhật thất bại' });
        },
    });

    useEffect(() => {
        if (!seat) return;
        form.setFieldsValue({
            so_ghe_ngoi: seat?.data?.so_ghe_ngoi,
            loai_ghe_ngoi: seat?.data?.loai_ghe_ngoi,
            trang_thai: seat?.data?.trang_thai,
            gia_ghe: seat?.data?.gia_ghe,
            room_id: seat?.data?.room_id,
        });
    }, [seat]);

    const onFinish = async () => {
        const formData = {
            room_id: form.getFieldValue('room_id'),
            seats: [
                {
                    range: form.getFieldValue('so_ghe_ngoi'),
                    loai_ghe_ngoi: form.getFieldValue('loai_ghe_ngoi'),
                    gia_ghe: parseFloat(form.getFieldValue('gia_ghe')),
                }
            ]
        };

        if (id) {
            const editData = {
                so_ghe_ngoi: form.getFieldValue('so_ghe_ngoi'),
                loai_ghe_ngoi: form.getFieldValue('loai_ghe_ngoi'),
                gia_ghe: parseFloat(form.getFieldValue('gia_ghe')),
                room_id: form.getFieldValue('room_id'),
            };
            await mutateEdit.mutateAsync({ id, body: editData });
        } else {
            await mutateAdd.mutateAsync(formData);
        }
    };

    return (
        <div className="form-container" style={{ padding: '20px', maxWidth: '600px', margin: 'auto', backgroundColor: '#f0f2f5', borderRadius: '8px', boxShadow: '0 2px 10px rgba(0, 0, 0, 0.1)' }}>
            <Title level={2} style={{ textAlign: 'center', color: '#1890ff' }}>
                {id ? 'Cập nhật thông tin ghế' : 'Thêm ghế mới'}
            </Title>
            <Form form={form} layout="vertical" onFinish={onFinish}>
                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item
                            label="Chọn phòng"
                            name="room_id"
                            rules={[{ required: true, message: 'Chọn phòng!' }]}
                        >
                            <Select placeholder="Chọn phòng" loading={loadingRooms} style={{ borderRadius: '4px' }} disabled={!!id}>
                                {roomData.map(room => (
                                    <Option key={room.id} value={room.id}>
                                        {room.ten_phong_chieu}
                                    </Option>
                                ))}
                            </Select>
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Số ghế ngồi (Range)"
                            name="so_ghe_ngoi"
                            rules={[{ required: true, message: 'Nhập số ghế ngồi!' }]}
                        >
                            <Input placeholder="Nhập số ghế ngồi (e.g., D1-D15)" style={{ borderRadius: '4px' }} />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Loại ghế ngồi"
                            name="loai_ghe_ngoi"
                            rules={[{ required: true, message: 'Chọn loại ghế ngồi!' }]}
                        >
                            <Select placeholder="Chọn loại ghế ngồi" style={{ borderRadius: '4px' }}>
                                <Option value="Thường">Thường</Option>
                                <Option value="Đôi">Đôi</Option>
                                <Option value="Víp">Víp</Option>
                            </Select>
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Trạng thái"
                            name="trang_thai"
                            rules={[{ required: true, message: 'Nhập trạng thái!' }]}
                        >
                            <Input placeholder="Nhập trạng thái" style={{ borderRadius: '4px' }} />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Giá ghế"
                            name="gia_ghe"
                            rules={[{ required: true, message: 'Nhập giá ghế!' }]}
                        >
                            <Input placeholder="Nhập giá ghế" style={{ borderRadius: '4px' }} />
                        </Form.Item>
                    </Col>
                </Row>
                <div className="flex justify-between items-center gap-[1rem]">
                    <Button htmlType="reset" style={{ width: '48%' }}>Đặt lại</Button>
                    <Button htmlType="submit" className="bg-blue-500 text-white" style={{ width: '48%' }}>
                        {id ? 'Cập nhật' : 'Thêm mới'}
                    </Button>
                </div>
            </Form>
        </div>
    );
}

export default SeatFormPage;