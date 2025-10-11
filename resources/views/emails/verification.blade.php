<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email</title>
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
        .code-container {
            background: #f8f9fa;
            border: 2px dashed #6366f1;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .verification-code {
            font-size: 36px;
            font-weight: bold;
            color: #6366f1;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .message {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SokishiX Chat</div>
            <h1 class="title">Xác thực Email</h1>
        </div>

        <div class="message">
            <p>Xin chào!</p>
            <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>SokishiX Chat</strong>.</p>
            <p>Để hoàn tất quá trình đăng ký, vui lòng sử dụng mã xác thực 6 chữ số bên dưới:</p>
        </div>

        <div class="code-container">
            <div class="verification-code">{{ $code }}</div>
        </div>

        <div class="warning">
            <strong>Lưu ý quan trọng:</strong>
            <ul>
                <li>Mã xác thực này có hiệu lực trong <strong>10 phút</strong></li>
                <li>Không chia sẻ mã này với bất kỳ ai</li>
                <li>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này</li>
            </ul>
        </div>

        <div class="message">
            <p>Nhập mã này vào form xác thực để kích hoạt tài khoản của bạn.</p>
            <p>Nếu bạn gặp bất kỳ vấn đề nào, vui lòng liên hệ với chúng tôi.</p>
        </div>

        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống SokishiX Chat</p>
            <p>Vui lòng không trả lời email này</p>
        </div>
    </div>
</body>
</html>
