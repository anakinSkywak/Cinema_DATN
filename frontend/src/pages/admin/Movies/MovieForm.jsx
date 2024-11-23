import { Button, Col, Form, Input, Row, notification, Typography, Select } from 'antd';
import { useNavigate, useParams } from 'react-router-dom';
import { useEffect } from 'react';
import config from '../../../config';
import { useCreateMovie, useGetMovie, useUpdateMovie } from '../../../hooks/api/useMovieApi';

const { Title } = Typography;
const { Option } = Select;

function MovieFormPage() {
    const navigate = useNavigate();
    let { id } = useParams();
    const { isLoading, data: movie } = id ? useGetMovie(id) : { isLoading: null, data: null };

    const [form] = Form.useForm();
    const mutateAdd = useCreateMovie({
        success: () => {
            notification.success({ message: 'Thêm mới phim thành công' });
            navigate(config.routes.admin.movies);
        },
        error: () => {
            notification.error({ message: 'Thêm mới phim thất bại' });
        },
    });

    const mutateEdit = useUpdateMovie({
        id,
        success: () => {
            notification.success({ message: 'Cập nhật phim thành công' });
            navigate(config.routes.admin.movies);
        },
        error: () => {
            notification.error({ message: 'Cập nhật phim thất bại' });
        },
    });

    useEffect(() => {
        if (!movie) return;
        form.setFieldsValue({
            ten_phim: movie?.data?.ten_phim,
            anh_phim: movie?.data?.anh_phim,
            dao_dien: movie?.data?.dao_dien,
            dien_vien: movie?.data?.dien_vien,
            noi_dung: movie?.data?.noi_dung,
            trailer: movie?.data?.trailer,
            gia_ve: movie?.data?.gia_ve,
            danh_gia: movie?.data?.danh_gia,
            movie_genres: movie?.data?.movie_genres.map(genre => genre.ten_loai_phim) || [],
        });
    }, [movie]);

    const onFinish = async () => {
        const formData = {
            ten_phim: form.getFieldValue('ten_phim'),
            anh_phim: form.getFieldValue('anh_phim'),
            dao_dien: form.getFieldValue('dao_dien'),
            dien_vien: form.getFieldValue('dien_vien'),
            noi_dung: form.getFieldValue('noi_dung'),
            trailer: form.getFieldValue('trailer'),
            gia_ve: form.getFieldValue('gia_ve'),
            danh_gia: parseFloat(form.getFieldValue('danh_gia')),
            movie_genres: form.getFieldValue('movie_genres'),
        };

        if (id) {
            await mutateEdit.mutateAsync({ id, body: formData });
        } else {
            await mutateAdd.mutateAsync(formData);
        }
    };

    return (
        <div className="form-container" style={{ padding: '20px', maxWidth: '600px', margin: 'auto', backgroundColor: 'white', borderRadius: '8px', boxShadow: '0 2px 10px rgba(0, 0, 0, 0.1)' }}>
            <Title level={2} style={{ textAlign: 'center' }}>
                {id ? 'Cập nhật thông tin phim' : 'Thêm phim mới'}
            </Title>
            <Form form={form} layout="vertical" onFinish={onFinish}>
                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item
                            label="Tên Phim"
                            name="ten_phim"
                            rules={[{ required: true, message: 'Nhập tên phim!' }]}
                        >
                            <Input placeholder="Nhập tên phim" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Ảnh Phim"
                            name="anh_phim"
                            rules={[{ required: true, message: 'Nhập đường dẫn ảnh phim!' }]}
                        >
                            <Input placeholder="Nhập đường dẫn ảnh phim" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Đạo Diễn"
                            name="dao_dien"
                            rules={[{ required: true, message: 'Nhập tên đạo diễn!' }]}
                        >
                            <Input placeholder="Nhập tên đạo diễn" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Diễn Viên"
                            name="dien_vien"
                            rules={[{ required: true, message: 'Nhập tên diễn viên!' }]}
                        >
                            <Input placeholder="Nhập tên diễn viên" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Nội Dung"
                            name="noi_dung"
                            rules={[{ required: true, message: 'Nhập nội dung phim!' }]}
                        >
                            <Input.TextArea placeholder="Nhập nội dung phim" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Trailer"
                            name="trailer"
                            rules={[{ required: true, message: 'Nhập đường dẫn trailer!' }]}
                        >
                            <Input placeholder="Nhập đường dẫn trailer" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Giá Vé"
                            name="gia_ve"
                            rules={[{ required: true, message: 'Nhập giá vé!' }]}
                        >
                            <Input placeholder="Nhập giá vé" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Đánh Giá"
                            name="danh_gia"
                            rules={[{ required: true, message: 'Nhập đánh giá!' }]}
                        >
                            <Input type="number" placeholder="Nhập đánh giá" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item
                            label="Thể Loại Phim"
                            name="movie_genres"
                            rules={[{ required: true, message: 'Chọn thể loại phim!' }]}
                        >
                            <Select mode="multiple" placeholder="Chọn thể loại phim">
                                <Option value="Hành Động">Hành Động</Option>
                                <Option value="Hài">Hài</Option>
                                <Option value="Kinh Dị">Kinh Dị</Option>
                                <Option value="Tình Cảm">Tình Cảm</Option>
                                <Option value="Khoa Học Viễn Tưởng">Khoa Học Viễn Tưởng</Option>
                            </Select>
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

export default MovieFormPage;