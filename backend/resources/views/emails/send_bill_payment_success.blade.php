<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công</title>
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <div style="text-align: center; padding: 20px;">
        <h1 style="margin-bottom: 20px;">CineBookingHub</h1>
        <h5 style="left: 100px;">Địa chỉ : Tầng 1 Tòa Nhà Thương Mại 55 , Xuân Phương , Nam Từ Liêm , Hà Nội </h5>
        <h3 style="margin-bottom: 20px;">Thông Tin Booking & Payment</h3>
        <table border="1" cellpadding="10" cellspacing="0"
            style="border-collapse: collapse; width: 60%; margin: 0 auto; background-color: #ffffff; text-align: left;">
            <thead>
                <tr>
                    <th style="background-color: #f2f2f2; text-align: left; padding: 10px; width: 40%;">Thông Tin</th>
                    <th style="background-color: #f2f2f2; text-align: left; padding: 10px; width: 60%;">Chi Tiết</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px;">Tên khách hàng</td>
                    <td style="padding: 10px;">{{ $booking->user->ho_ten }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Số điện thoại</td>
                    <td style="padding: 10px;">{{ $booking->user->so_dien_thoai }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Email</td>
                    <td style="padding: 10px;">{{ $booking->user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Ngày mua</td>
                    <td style="padding: 10px;">{{ $booking->ngay_mua }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Tên phim</td>
                    <td style="padding: 10px;">{{ $booking->showtime->movie->ten_phim }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Số lượng vé</td>
                    <td style="padding: 10px;">{{ $booking->so_luong }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Ngày xem phim</td>
                    <td style="padding: 10px;">{{ $booking->showtime->ngay_chieu }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Phòng chiếu</td>
                    <td style="padding: 10px;">{{ $room->ten_phong_chieu }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Giờ chiếu</td>
                    <td style="padding: 10px;">{{ $booking->showtime->gio_chieu }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Ghế ngồi</td>
                    <td style="padding: 10px;">{{ $booking->ghe_ngoi }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Đồ ăn kèm</td>
                    <td style="padding: 10px;">{{ $booking->do_an }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Tổng tiền</td>
                    <td style="padding: 10px;">{{ number_format($booking->tong_tien_thanh_toan) }} VND</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Phương thức thanh toán</td>
                    <td style="padding: 10px;">{{ $payment->phuong_thuc_thanh_toan }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Trạng thái thanh toán</td>
                    <td style="padding: 10px;">{{ $payment->trang_thai }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Ngày thanh toán</td>
                    <td style="padding: 10px;">{{ $payment->ngay_thanh_toan }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px;">Ghi Chú</td>
                    <td style="padding: 10px;">{{ $booking->ghi_chu }}</td>
                </tr>

            </tbody>
        </table>


        <p style="margin-top: 20px; color: #888888; font-size: 14px;">
            Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!
            <br> <br>
            © Developer Bùi Văn Ánh
        </p>
    </div>


    <h3>Chi tiết thanh toán đã được đính kèm dưới dạng PDF ⬇️</h3>


</body>

</html>
