import { Button, Form, Input, notification, Typography } from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import { useEffect } from 'react';
import config from '../../../config';
import { useUpdateRoom, useGetRoom, useCreateRoom } from '../../../hooks/api/useRoomApi';

const { Title } = Typography;

function RoomFormPage() {
    const navigate = useNavigate();
    let { id } = useParams();
    const { data: room } = id ? useGetRoom(id) : { data: null };

    const [form] = Form.useForm();
    const mutateAdd = useCreateRoom({
        success: () => {
            notification.success({ message: 'Thêm mới phòng thành công' });
            navigate(config.routes.admin.seat.create);
        },
        error: () => {
            notification.error({ message: 'Thêm mới phòng thất bại' });
        },
    });

    const mutateEdit = useUpdateRoom({
        id,
        success: () => {
            notification.success({ message: 'Cập nhật phòng thành công' });
            navigate(config.routes.admin.room);
        },
        error: () => {
            notification.error({ message: 'Cập nhật phòng thất bại' });
        },
    });

    useEffect(() => {
        if (!room) return;
        form.setFieldsValue({
            room_name: room?.data?.ten_phong_chieu,
        });
    }, [room]);

    const onFinish = async () => {
        const formData = {
            ten_phong_chieu: form.getFieldValue('room_name'),
        };

        if (id) {
            await mutateEdit.mutateAsync({ id, body: formData });
        } else {
            await mutateAdd.mutateAsync(formData);
        }
    };

    return (
        <div className="form-container" style={{ padding: '80px', maxWidth: '1000px', margin: 'auto', backgroundColor: '#f9f9f9', borderRadius: '10px', boxShadow: '0 4px 20px rgba(0, 0, 0, 0.1)' }}>
            <Title level={2} style={{ textAlign: 'center', marginBottom: '20px' }}>
                {id ? 'Cập nhật thông tin phòng' : 'Thêm mới thông tin phòng'}
            </Title>
            <Form form={form} layout="vertical" onFinish={onFinish}>
                <Form.Item
                    label="Tên Phòng"
                    name="room_name"
                    rules={[{ required: true, message: 'Nhập tên phòng!' }]}
                >
                    <Input placeholder="Nhập tên phòng" style={{ borderRadius: '5px' }} />
                </Form.Item>
                <div className="flex justify-between items-center gap-[1rem]">
                    <Button htmlType="reset" style={{ width: '48%', borderRadius: '5px' }}>Đặt lại</Button>
                    <Button htmlType="submit" className="bg-blue-500 text-white" style={{ width: '48%', borderRadius: '5px' }}>
                        {id ? 'Cập nhật' : 'Thêm mới'}
                    </Button>
                </div>
            </Form>
        </div>
    );
}

export default RoomFormPage;