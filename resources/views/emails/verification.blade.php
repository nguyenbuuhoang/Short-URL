<div style="margin:auto;max-width:600px; border: 1px solid #000; border-radius: 5px;"
    class="m_1906987519014165992email-container">

    <table role="presentation" cellspacing="0" cellpadding="0" width="100%" align="center"
        id="m_1906987519014165992logoContainer" style="background:#252f3d;border-radius:3px 3px 0 0;max-width:600px">
        <tbody>
            <tr>
                <td
                    style="background:rgb(206, 73, 1);border-radius:3px 3px 0 0;padding:20px 0 10px 0;text-align:center">
                    <img src="https://media.licdn.com/dms/image/C5612AQEjeyt_SgzdmQ/article-cover_image-shrink_600_2000/0/1520109896777?e=2147483647&v=beta&t=3Q9Hb6JmtKWWnlvgIEYTrpW5_QMzFa6eUlXElDoDB60"
                        width="75" height="45" alt="AWS logo" border="0"
                        style="font-family:sans-serif;font-size:15px;line-height:140%;color:#555555" class="CToWUd"
                        data-bit="iit">
                </td>
            </tr>
        </tbody>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" width="100%" align="center"
        id="m_1906987519014165992emailBodyContainer" style="border:0px;border-bottom:1px solid #d6d6d6;max-width:600px">
        <tbody>
            <tr>
                <td
                    style="background-color:#fff;color:#444;font-family:'Amazon Ember','Helvetica Neue',Roboto,Arial,sans-serif;font-size:14px;line-height:140%;padding:25px 35px">
                    <h1 style="font-size:20px;font-weight:bold;line-height:1.3;margin:0 0 15px 0">Verify your identity
                    </h1>
                    <p style="margin:0 0 15px 0;padding:0 0 0 0">Hi {{ $user->name }},</p>
                    <p style="margin:0 0 15px 0;padding:0 0 0 0">Bạn đã đăng ký tài khoản thành công, hãy nhập mã xác
                        thực vào link dưới để xác nhận</p>
                </td>
            </tr>
            <tr>
                <td
                    style="background-color:#fff;color:#444;font-family:'Amazon Ember','Helvetica Neue',Roboto,Arial,sans-serif;font-size:14px;line-height:140%;padding:25px 35px;padding-top:0;text-align:center">
                    <div style="font-weight:bold;padding-bottom:15px">Verification code</div>
                    <div style="color:#000;font-size:36px;font-weight:bold;padding-bottom:15px">{{ $verificationCode }}
                    </div>
                    <div style="color:#444;font-size:10px">(Mã sẽ hết hạn sau 5 phút)</div>
                </td>
            </tr>
            <tr>
                <td
                    style="background-color:#fff;border-top:1px solid #e0e0e0;color:#777;font-family:'Amazon Ember','Helvetica Neue',Roboto,Arial,sans-serif;font-size:14px;line-height:140%;padding:25px 35px">
                    <p style="margin: 0; padding: 0; text-align: center;">
                        <a href="{{ $verificationLink }}" target="_blank"
                            data-saferedirecturl="https://www.google.com/url?q=https://aws.amazon.com/contact-us/&amp;source=gmail&amp;ust=1697968409975000&amp;usg=AOvVaw3D1QoIsRcfS8H8m70GLPpT">
                            <button
                                style="background-color: yellow; color: rgb(0, 4, 7); padding: 10px 20px; border: 2px solid black; border-radius: 5px; cursor: pointer;">Xác
                                nhận ngay</button>
                        </a>
                    </p>
            </tr>
        </tbody>
    </table>
</div>
</div>
