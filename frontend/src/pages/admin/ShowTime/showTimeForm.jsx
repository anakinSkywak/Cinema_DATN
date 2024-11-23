import { Button, Col, Form, Row, notification, Typography, Select, DatePicker, Input } from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import { useEffect } from 'react';
import config from '../../../config';
import { usestoreShowtime, useAddShowtime, useshowShowtime, useUpdateShowtime } from '../../../hooks/api/useShowtimeApi';
import moment from 'moment';
import axios from 'axios'; // Import axios for making API calls
const { Title } = Typography;

function ShowtimeFormPage() {
    const navigate = useNavigate();
    let { id } = useParams();
    const { isLoading: loadingShowtime, data: showtime } = id ? useshowShowtime(id) : { isLoading: null, data: null };
    const { data: showtimeData } = useAddShowtime();

    useEffect(() => {
        console.log('Showtime data:', showtime);
    }, [showtime]);

    const [form] = Form.useForm();
    const mutateAdd = usestoreShowtime({
        success: () => {
            notification.success({ message: 'Thêm mới thành công' });
            navigate(config.routes.admin.showTime);
        },
        error: () => {
            notification.error({ message: 'Thêm mới thất bại' });
        },
    });

    const mutateEdit = useUpdateShowtime({
        id,
        success: () => {
            notification.success({ message: 'Cập nhật thành công' });
            navigate(config.routes.admin.showTime);
        },
        error: () => {
            notification.error({ message: 'Cập nhật thất bại' });
        },
    });

    useEffect(() => {
        if (!showtime) return;
        const movieId = showtimeData?.data?.movies?.find(movie => movie.ten_phim === showtime.movie)?.id || '';
        const roomIds = showtimeData?.data?.rooms?.filter(room => showtime.room.includes(room.ten_phong_chieu)).map(room => room.id) || [];
        
        // Check if gio_chieu is a string and format it
        const gioChieuArray = typeof showtime.gio_chieu === 'string' ? 
            [moment(showtime.gio_chieu, 'HH:mm:ss').format('HH:mm')] : 
            []; // Fallback to an empty array if not

        form.setFieldsValue({
            phim_id: movieId,
            room_ids: roomIds,
            ngay_chieu: moment(showtime.ngay_chieu),
            gio_chieu: gioChieuArray, // Use the formatted array
        });
    }, [showtime, showtimeData]);
    const onAddFinish = async (formData) => {
        await mutateAdd.mutateAsync(formData);
    };

    const onEditFinish = async (formData) => {
        // Ensure the formData includes the correct structure for editing
        await mutateEdit.mutateAsync({ id, body: formData });
    };

    const onFinish = async () => {
        const formData = {
            phim_id: form.getFieldValue('phim_id'),
            room_ids: form.getFieldValue('room_ids'),
            ngay_chieu: form.getFieldValue('ngay_chieu').format('YYYY-MM-DD'),
            gio_chieu: form.getFieldValue('gio_chieu'),
        };
        try {
            if (id) {
                // Ensure the formData is passed correctly for editing
                await onEditFinish(formData);
            } else {
                await onAddFinish(formData);
            }
        } catch (error) {
            if (error.response && error.response.data) {
                // Hiển thị thông báo lỗi cho từng trường hợp
                const errorMessage = error.response.data.error || 'Đã xảy ra lỗi không xác định';
                notification.error({
                    message: 'Lỗi',
                    description: errorMessage,
                });
            } else {
                notification.error({ message: 'Đã xảy ra lỗi không xác định' });
            }
        }
    };

    const handleAddShowtime = (value) => {
        const currentValues = form.getFieldValue('gio_chieu') || [];
        if (value && !currentValues.includes(value)) {
            form.setFieldsValue({ gio_chieu: [...currentValues, value] });
        }
    };

    const handleRemoveShowtime = (value) => {
        const currentValues = form.getFieldValue('gio_chieu') || [];
        const updatedValues = currentValues.filter(time => time !== value);
        form.setFieldsValue({ gio_chieu: updatedValues });
    };

    return (
        <div className="form-container" style={{ padding: '20px', maxWidth: '600px', margin: 'auto', backgroundColor: 'white', borderRadius: '8px', boxShadow: '0 2px 10px rgba(0, 0, 0, 0.1)' }}>
            <Title level={2} style={{ textAlign: 'center', marginBottom: '20px' }}>
                {id ? 'Cập nhật thông tin suất chiếu' : 'Thêm suất chiếu mới'}
            </Title>
            <Form form={form} layout="vertical" onFinish={onFinish}>
                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item
                            label="Phim ID"
                            name="phim_id"
                            rules={[{ required: true, message: 'Chọn ID phim!' }]}>
                            <Select placeholder="Chọn ID phim">
                                {showtimeData?.data?.movies?.map(movie => (
                                    <Select.Option key={movie.id} value={movie.id}>{movie.ten_phim}</Select.Option>
                                ))}
                            </Select>
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Room ID"
                            name="room_ids"
                            rules={[{ required: true, message: 'Chọn ID phòng!' }]}>
                            <Select mode="multiple" placeholder="Chọn ID phòng">
                                {showtimeData?.data?.rooms?.map(room => (
                                    <Select.Option key={room.id} value={room.id}>{room.ten_phong_chieu}</Select.Option>
                                ))}
                            </Select>
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Ngày chiếu"
                            name="ngay_chieu"
                            rules={[{ required: true, message: 'Chọn ngày chiếu!' }]}>
                            <DatePicker placeholder="Chọn ngày chiếu" format="YYYY-MM-DD" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Giờ chiếu"
                            name="gio_chieu"
                            rules={[{ required: true, message: 'Nhập giờ chiếu!' }]}>
                            <Input
                                placeholder="Nhập giờ chiếu (HH:mm)"
                                onPressEnter={(e) => {
                                    const value = e.target.value;
                                    if (value) {
                                        handleAddShowtime(value);
                                        e.target.value = ''; // Clear input after adding
                                    }
                                }}
                            />
                            <div>
                                {form.getFieldValue('gio_chieu')?.map((time) => (
                                    <div key={time} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                        <span>{time}</span>
                                        <Button type="link" onClick={() => handleRemoveShowtime(time)}>Xóa</Button>
                                    </div>
                                ))}
                            </div>
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

export default ShowtimeFormPage;