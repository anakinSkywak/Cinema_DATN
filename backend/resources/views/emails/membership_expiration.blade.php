<!DOCTYPE html>
<html>
<head>
    <title>Thông báo hết hạn thẻ hội viên</title>
</head>
<body>
    <p>Chào bạn,</p>
    <p>Thẻ hội viên của bạn {{ $membership->so_the }} sẽ hết hạn vào {{ $membership->ngay_het_han }}.</p>
    <p>{{ $membership->renewal_message }}</p>
    <p>Vui lòng thực hiện gia hạn nếu muốn tiếp tục sử dụng dịch vụ!</p>
</body>
</html>
