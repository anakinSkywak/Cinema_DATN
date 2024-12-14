<!DOCTYPE html>
<html>
<head>
    <title>Thông báo thẻ hội viên</title>
</head>
<body>
    <div class="container">
        <h1>Xin chào!</h1>
        <p>{{ $message }}</p>
        <p><strong>Ngày hết hạn:</strong> {{ $membership->ngay_het_han }}</p>
        <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
    </div>
</body>
</html>
