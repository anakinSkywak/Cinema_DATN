<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé Vào Phòng Chiếu Phim</title>
    <style>
        body {

            font-family: 'DejaVu Sans', sans-serif;
            /* Font hỗ trợ tiếng Việt */
            background-color: white;
            width: 370px;
            height: 50px;
            padding: 0 auto;
            margin: 0 auto;
        }

        .ticket {

            max-width: 330px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .sub-header {
            text-align: center;
            font-size: 14px;

            margin-bottom: 20px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .row span {
            font-size: 12px;
        }

        .content {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
            margin-bottom: 10px;
        }

        .barcode {
            text-align: center;
            margin-top: 20px;
        }

        .barcode img {
            width: 100px;
            height: auto;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="header">CineBookingHub</div>
        <div class="sub-header" style="font-size: 20px">Vé vào phòng chiếu phim</div>

        <div class="row">
            <strong>Rạp:</strong>
            <strong>CineBookingHub</strong>
        </div>
        <div class="row">
            <strong>Địa chỉ:</strong>
            <span>Tầng 1, Tòa Nhà Thương Mại 5, Xuân Phương, Nam Từ Liêm, Hà Nội</span>
        </div>

        <div class="content">
            <div class="row">
                <span>Phim:</span>
                <span>{{ $ticket->ten_phim }}</span>
            </div>
            <div class="row">
                <span>Thời gian phim:</span>
                <span>{{ $ticket->thoi_luong_chieu }} phút</span>
            </div>
            <div class="row">
                <span>Ngày xem phim:</span>
                <span> {{ \Carbon\Carbon::parse($ticket->ngay_chieu)->format('d-m-Y') }} </span>
            </div>
            <div class="row">
                <span>Giờ xem phim:</span>
                <span> {{ \Carbon\Carbon::parse($ticket->gio_chieu)->format('H:i') }} </span>
            </div>
            <div class="row">
                <span>Phòng chiếu:</span>
                <span>{{ $ticket->ten_phong_chieu }} </span>
            </div>
            <div class="row">
                <span>Ghế:</span>
                <span>{{ $ticket->ghe_ngoi }}</span>
            </div>
            <div class="row">
                <span>Số lượng vé/người:</span>
                <span>{{ $ticket->so_luong }}</span>
            </div>
            <div class="row">
                <span>Đồ ăn kèm:</span>
                <span>{{ $ticket->do_an }}</span>
            </div>
            <div class="row">
                <span>Ngày mua:</span>
                <span> {{ \Carbon\Carbon::parse($ticket->ngay_mua)->format('d-m-Y') }}</span>
            </div>
            <div class="row">
                <span>Ghi chú:</span>
                <span>{{ $ticket->ghi_chu }}</span>
            </div>
        </div>

        <div class="row">
            <strong>Tổng thanh toán:</strong>
            <strong>{{ number_format($ticket->tong_tien_thanh_toan) }} VND</strong>
        </div>

        <div class="row" style="text-align: center">
            <span style="color: red ; font-size: 15px">ĐÃ CHECK IN</span>
        </div>

        <div class="footer">
            Lưu ý : Nhân viên soát vé nhận vé luôn của khách <br>
            Ticket No. | Cảm ơn bạn đã chọn CineBookingHub!
        </div>

    </div>


</body>

</html>
