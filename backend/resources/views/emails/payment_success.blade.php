<!DOCTYPE html>
<html>
<head>
    <title>Thông báo thanh toán thành công</title>
</head>
<body>
    <h1>Chúc mừng bạn đã thanh toán thành công và đăng kí thẻ hội viên!</h1>
    <p>Mã thẻ hội viên của bạn là: <strong>{{ $membership->so_the }}</strong></p>
    <p>Ngày bắt đầu: {{ $membership->ngay_dang_ky }}</p>
    <p>Ngày hết hạn: {{ $membership->ngay_het_han }}</p>
    <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi.</p>
</body>
</html>
