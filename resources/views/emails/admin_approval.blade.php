<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Name</title>
</head>

<body bgcolor="#0f3462" style="margin-top:20px;margin-bottom:20px">
    <!-- Main table -->
    <table border="0" align="center" cellspacing="0" cellpadding="0" bgcolor="white" width="650">
        <tr>
            <td>
                <!-- Child table -->
                <table border="0" cellspacing="0" cellpadding="0" style="color:#0f3462; font-family: sans-serif;">
                    <tr>
                        <td>
                            <h2 style="text-align:center; margin: 0px; padding-bottom: 25px; margin-top: 25px;">
                                <i>SpeakMy</i><span style="color:lightcoral">Dialect</span>
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="https://speakmydialect.com.au/images/logo.jpeg" height="50px"
                                style="display:block; margin:auto;padding-bottom: 25px; ">
                        </td>
                    </tr>
                    <tr>
                        @if ($data['status'] == 'active')
                            <td style="text-align: center;">
                                <h1 style="margin: 0px;padding-bottom: 25px; text-transform: uppercase;">Your Profile is Approved</h1>
                                {{-- <h2 style="margin: 0px;padding-bottom: 25px;font-size:22px;"> Please renew your subscription</h2> --}}
                                <p style=" margin: 0px 40px;padding-bottom: 25px;line-height: 2; font-size: 15px;">
                                    Hello {{ $data['username'] }},

                                    Great news! Your profile has been approved, and you now have full access to your
                                    dashboard.

                                    Log in to your dashboard to start exploring all the features available to you.
                                    <a href="https://speakmydialect.com.au/login"
                                        style="background-color:#36b445; color:white; padding:15px 97px; outline: none; display: block; margin: auto; border-radius: 31px;
                                font-weight: bold; margin-top: 25px; margin-bottom: 25px; border: none; text-transform:uppercase;">
                                        Access Your Dashboard
                                    </a>

                                    If you have any questions or need assistance, our support team is here to help.
                                    Simply
                                    reach out to us at info@speakmydialect.com.au.

                                    Best regards,
                                    The Speak My Dialect Team

                                    Please do not reply to this email, as it is not monitored.
                                </p>

                                {{-- <h2 style="margin: 0px; padding-bottom: 25px;">Expire: 05 November</h2> --}}
                            </td>
                        @elseif($data['status'] == 'inactive')
                            <td style="text-align: center;">
                                <h1 style="margin: 0px;padding-bottom: 25px; text-transform: uppercase;">Your Profile is
                                    Inactive</h1>
                                <p style=" margin: 0px 40px;padding-bottom: 25px;line-height: 2; font-size: 15px;">
                                    Hello {{ $data['username'] }},

                                    We wanted to inform you that your account on Speak My Dialect has been marked as
                                    inactive. This status change occurred due to the following reason:

                                    {{ $data['message'] }}

                                    If you believe this was an error, or if you would like to resolve this issue, please
                                    don’t hesitate to reach out to our support team at info@speakmydialect.com.au. We’re
                                    here to assist you.

                                    Thank you for being part of Speak My Dialect, and we look forward to resolving this
                                    for
                                    you.

                                    Best regards,
                                    Speak My Dialect Team
                                </p>
                            </td>
                        @elseif($data['status'] == 'reject')
                            <td style="text-align: center;">
                                <h1 style="margin: 0px;padding-bottom: 25px; text-transform: uppercase;">Your Profile is
                                    Reject</h1>
                                <p style=" margin: 0px 40px;padding-bottom: 25px;line-height: 2; font-size: 15px;">
                                    Hello {{ $data['username'] }},

                                    We regret to inform you that your account registration with Speak My Dialect has
                                    been rejected. Unfortunately, we were unable to approve your account due to the
                                    following reason:

                                    {{ $data['message'] }}

                                    If you have any questions or believe there’s been a mistake, please don’t hesitate
                                    to contact us at info@speakmydialect.com.au. We’ll be happy to help clarify the
                                    situation.

                                    Thank you for your understanding,

                                    Best regards,
                                    Speak My Dialect Team
                                </p>
                            </td>
                        @endif
                    </tr>
                    {{-- <tr>
                        <td>
                            <button type="button"
                                style="background-color:#36b445; color:white; padding:15px 97px; outline: none; display: block; margin: auto; border-radius: 31px;
                                font-weight: bold; margin-top: 25px; margin-bottom: 25px; border: none; text-transform:uppercase; ">Renew</button>
                        </td>
                    </tr> --}}
                    <tr>
                        <td style="text-align:center;">
                            <h2 style="padding-top: 25px; line-height: 1; margin:0px;">Need Help?</h2>
                            <div style="margin-bottom: 25px; font-size: 15px;margin-top:7px;">info@speakmydialect.com.au
                            </div>
                        </td>
                    </tr>
                </table>
                <!-- /Child table -->
            </td>
        </tr>
    </table>
    <!-- / Main table -->
</body>

</html>
