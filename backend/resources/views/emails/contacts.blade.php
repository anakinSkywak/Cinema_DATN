<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phản hồi từ hệ thống</title>
</head>
<body>
    <p>Chào {{ $ho_ten }},</p>
    <p>Cảm ơn bạn đã đóng góp với chúng tôi. </p>
    <p>Phản hồi: {{ $noidung }} của bạn đã được ghi nhận</p>
    
    <!-- Thêm phần phản hồi của admin -->
    <p>Dưới đây là phản hồi của chúng tôi:</p>
    <p><strong>Phản hồi của Admin:</strong> {{ $admin_reply }}</p>
    
    <p>Trân trọng</p>
    <p>Đội ngũ hỗ trợ khách hàng</p>
</body>
</html>