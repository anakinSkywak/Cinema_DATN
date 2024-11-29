<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineBookingHub Ticket</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .ticket {
            width: 320px;
            background-color: white;
            /* Màu hồng */
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
            font-size: 12px;
            color: #000;
            position: relative;
        }

        .ticket .header {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .ticket .content {
            line-height: 1.6;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
        }

        .ticket .content .row {
            display: flex;
            justify-content: space-between;
        }

        .ticket .content .row span {
            display: inline-block;
        }

        .ticket .barcode {
            text-align: center;
            margin-top: 15px;
        }

        .ticket .barcode img {
            width: 150px;
            height: 150px;
        }

        .ticket .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="header" >CineBookingHub</div>
        <div class="header" style="font-family: 'Courier New', Courier, monospace">Vé Vào Phòng Chiếu Phim</div>
        <br>
        <div class="row">
            <strong>Rạp: CineBookingHub </strong>
            <strong><p>Địa chỉ: Tầng 1 Tòa Nhà Thương Mại 5, Xuân Phương , Nam Từ Liêm , Hà Nội </p></strong>
        </div>
        <br>

        <div class="content">

            <div class="row">
                <span>Phim:</span>
                <span>Batman hồi kết tử chiến</span>
            </div>
            <div class="row">
                <span>Ngày xem phim:</span>
                <span>29/11/2024</span>
            </div>
            <div class="row">
                <span>Giờ xem phim:</span>
                <span>22:00</span>
            </div>
            <div class="row">
                <span>Phòng chiếu:</span>
                <span>Phòng số 1</span>
            </div>
            <div class="row">
                <span>Ghế:</span>
                <span>A1 , A2</span>
            </div>
            <div class="row">
                <span>Số lượng vé/người:</span>
                <span>2</span>
            </div>
            <div class="row">
                <span>Giá vé:</span>
                <span>195,000 VND</span>
            </div>
            <div class="row">
                <span>Đồ ăn kèm:</span>
                <span>Bỏng (x1) , Coca (x2)</span>
            </div>
            <div class="row">
                <span>Ngày mua:</span>
                <span>26/11/2024</span>
            </div>
            <div class="row">
                <span>Ghi chú:</span>
                <span></span>
            </div>
            <br>
            <div class="content">
                <div class="row">
                    <span>Tiền thanh toán:</span>
                    <span>150.000 VND</span>
                </div>

            </div>
            <div class="barcode">
                <img src="https://i.pinimg.com/originals/24/ab/ef/24abeff2113b322d7c2df86c24ddd797.jpg" alt="Barcode">
            </div>
            <div class="footer">
                Ticket No. | Thank you for choosing CineBookingHub!
            </div>
        </div>
</body>

</html>
