<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }

        .container {
            width: 80mm; /* Khổ giấy 80mm */
            margin: 0 auto;
            padding: 10px;
        }

        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
            color: #000;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table th, .info-table td {
            padding: 8px;
            text-align: left;
        }

        .info-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .info-table td {
            border-bottom: 1px solid #ddd;
        }

        .total {
            font-size: 16px;
            font-weight: bold;
            color: #d9534f;
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thông Tin Hóa Đơn</h1>

        <table class="info-table">
            <tr>
                <th>Thời Gian Chiếu</th>
                <td>{{ $data->showtime->ngay_chieu }} lúc {{ $data->showtime->gio_chieu }}</td>
            </tr>
            <tr>
                <th>Thời Lượng Phim</th>
                <td>{{ $data->showtime->thoi_luong_chieu }} phút</td>
            </tr>
            <tr>
                <th>Tên Phim</th>
                <td>{{ $tenPhim->ten_phim }}</td>
            </tr>
            <tr>
                <th>Phòng Chiếu</th>
                <td>{{ $data->showtime->room_id }}</td>
            </tr>
        </table>

        <p class="total">Tổng Tiền: {{ number_format($data->tong_tien, 0, ',', '.') }} VND</p>

        <div class="footer">
            <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
            <p>Hãy quay lại lần sau!</p>
        </div>
    </div>
</body>
</html>

