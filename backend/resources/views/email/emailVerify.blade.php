<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Xác thực email</title>
  <style>
    @font-face {
      font-family: 'Source Sans Pro';
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src: local('Source Sans Pro Regular'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe03otPbuUS0.woff) format('woff');
    }
    @font-face {
      font-family: 'Source Sans Pro';
      font-style: normal; 
      font-weight: 700;
      font-display: swap;
      src: local('Source Sans Pro Bold'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/toadOcfmlt9b38dHJxOBGFkQc6VGVFSmCnC_l7QZG60.woff) format('woff');
    }

    body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      background-color: #e9ecef;
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    table {
      border-collapse: collapse;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      border: 0;
      height: auto;
      line-height: 100%;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }

    .main-table {
      max-width: 600px;
      margin: 0 auto;
      background: #ffffff;
    }

    .header {
      padding: 36px 24px;
      text-align: center;
    }
    
    .content {
      padding: 24px;
      font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif;
      font-size: 16px;
      line-height: 24px;
    }

    .button {
      display: inline-block;
      padding: 16px 36px;
      background: #1a82e2;
      color: #ffffff;
      text-decoration: none;
      border-radius: 6px;
      font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif;
    }

    .footer {
      padding: 24px;
      text-align: center;
      color: #666;
      font-size: 14px;
      line-height: 20px;
    }

    @media only screen and (max-width: 600px) {
      .main-table {
        width: 100% !important;
      }
      .content {
        padding: 12px !important;
      }
    }
  </style>
</head>

<body>
  <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
    Mã xác thực OTP của bạn
  </div>

  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" bgcolor="#e9ecef">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="main-table">
          <tr>
            <td class="header">
              <img src="https://www.blogdesire.com/wp-content/uploads/2019/07/blogdesire-1.png" alt="Logo" width="48" style="display: block; margin: 0 auto;">
            </td>
          </tr>

          <tr>
            <td class="content" style="border-top: 3px solid #d4dadf;">
              <h1 style="margin: 0 0 24px; font-size: 32px; font-weight: 700;">Mã xác thực OTP của bạn</h1>
              
              <p>Đây là mã OTP để xác thực tài khoản của bạn. Mã này sẽ hết hạn sau 5 phút:</p>

              <p style="text-align: center; margin: 24px 0; font-size: 32px; font-weight: bold; letter-spacing: 5px;">
                {{ $otp }}
              </p>

              <p>Vui lòng không chia sẻ mã này với bất kỳ ai. Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>

              <p style="margin-top: 24px; border-top: 1px solid #eee; padding-top: 24px;">
                Trân trọng,<br>
                {{ config('app.name') }}
              </p>
            </td>
          </tr>

          <tr>
            <td class="footer">
              <p>Email này đã được gửi đến. Nếu bạn không yêu cầu mã OTP này, vui lòng bỏ qua email này.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>