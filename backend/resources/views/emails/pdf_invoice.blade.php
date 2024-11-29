<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé Xem Phim</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Font hỗ trợ tiếng Việt */
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .ticket {
            /* width: 100%; */
            max-width: 74mm; /* Chiều rộng A7 */
            height: 105mm; /* Chiều cao A7 */
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10mm; /* Padding thêm để tạo không gian cho các phần tử */
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .sub-header {
            text-align: center;
            font-size: 10px;
            margin-bottom: 10px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .row span {
            font-size: 9px;
        }

        .content {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            margin-bottom: 5px;
        }

        .barcode {
            text-align: center;
            margin-top: 10px;
        }

        .barcode img {
            width: 50mm; /* Đảm bảo barcode vừa vặn với kích thước vé */
            height: auto;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="header">CineBookingHub</div>
        <div class="sub-header">Vé Vào Phòng Chiếu Phim</div>

        <div class="row">
            <span>Rạp: CineBookingHub</span>
           
        </div>
        <div class="row">
            
            <span>Địa chỉ: Tầng 1, Tòa Nhà Thương Mại 5, Xuân Phương, Nam Từ Liêm, Hà Nội</span>
        </div>

        <div class="content">
            <div class="row">
                <span>Phim:</span>
                <span>{{ $booking->showtime->movie->ten_phim }}</span>
            </div>
            <div class="row">
                <span>Thời gian phim:</span>
                <span>{{ $booking->showtime->thoi_luong_chieu }} phút</span>
            </div>
            <div class="row">
                <span>Ngày xem phim:</span>
                <span>{{ $booking->showtime->ngay_chieu }}</span>
            </div>
            <div class="row">
                <span>Giờ xem phim:</span>
                <span>{{ $booking->showtime->gio_chieu }}</span>
            </div>
            <div class="row">
                <span>Phòng chiếu:</span>
                <span>{{ $room->ten_phong_chieu }}</span>
            </div>
            <div class="row">
                <span>Ghế:</span>
                <span>{{ $booking->ghe_ngoi }}</span>
            </div>
            <div class="row">
                <span>Số lượng vé/người:</span>
                <span>{{ $booking->so_luong }}</span>
            </div>
            <div class="row">
                <span>Đồ ăn kèm:</span>
                <span>{{ $booking->do_an }}</span>
            </div>
            <div class="row">
                <span>Ngày mua:</span>
                <span>{{ $booking->ngay_mua }}</span>
            </div>
            <div class="row">
                <span>Ghi chú:</span>
                <span>{{ $booking->ghi_chu }}</span>
            </div>
            <div class="row">
            <span>Tổng thanh toán:</span>
            <span>{{ number_format($booking->tong_tien_thanh_toan) }} VND</span>
        </div>
        </div>

        

       

        <div class="footer">
            Ticket No. | Cảm ơn bạn đã chọn CineBookingHub!
        </div>
    </div>
</body>

</html>
