<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn</title>
</head>
<body>
    <h1>Thông tin hóa đơn</h1>
    <p>Thời gian chiếu: {{ $data->showtime->ngay_chieu }} lúc {{ $data->gio_chieu }}</p>
    <p>Thời lượng: {{ $data->showtime->thoi_luong_chieu }} phút</p>
    <p>Phim ID: {{ $data->showtime->phim_id }}</p>
    <p>Phòng chiếu: {{ $data->showtime->room_id }}</p>
    <p>Tổng tiền: {{ number_format($data->tong_tien, 0, ',', '.') }} VND</p>
</body>
</html>
