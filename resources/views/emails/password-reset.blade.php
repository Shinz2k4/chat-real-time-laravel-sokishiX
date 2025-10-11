<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #6366f1;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        .reset-button:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 14px;
        }
        .code-container {
            background: #f8f9fa;
            border: 2px dashed #6366f1;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .reset-url {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #6366f1;
            word-break: break-all;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SokishiX Chat</div>
            <h1 class="title">Đặt lại mật khẩu</h1>
        </div>

        <div class="message">
            <p>Xin chào!</p>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản <strong>{{ $email }}</strong>.</p>
            <p>Để đặt lại mật khẩu, vui lòng click vào nút bên dưới:</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="reset-button">
                <i class="fas fa-key" style="margin-right: 8px;"></i>
                Đặt lại mật khẩu
            </a>
        </div>

        <div class="code-container">
            <p><strong>Hoặc copy link này vào trình duyệt:</strong></p>
            <div class="reset-url">{{ $resetUrl }}</div>
        </div>

        <div class="warning">
            <strong>Lưu ý quan trọng:</strong>
            <ul>
                <li>Link này có hiệu lực trong <strong>1 giờ</strong> (đến {{ $expiresAt }})</li>
                <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này</li>
                <li>Mật khẩu của bạn sẽ không thay đổi cho đến khi bạn tạo mật khẩu mới</li>
                <li>Không chia sẻ link này với bất kỳ ai</li>
            </ul>
        </div>

        <div class="message">
            <p>Nếu bạn gặp bất kỳ vấn đề nào, vui lòng liên hệ với chúng tôi.</p>
            <p>Chúc bạn có trải nghiệm tốt với SokishiX Chat!</p>
        </div>

        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống SokishiX Chat</p>
            <p>Vui lòng không trả lời email này</p>
        </div>
    </div>
</body>
</html>
