<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name', 'Blogger4U') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-font-smoothing: antialiased; line-height: 1.6;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 36px 16px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 32px 40px; text-align: center; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%);">
                            <a href="{{ config('app.url') }}" style="text-decoration: none;">
                                <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 800; letter-spacing: -0.5px;">
                                    {{ config('app.name', 'Blogger4U') }}
                                </h1>
                            </a>
                            @if(isset($badge))
                                <div style="margin-top: 8px;">
                                    <span style="display: inline-block; padding: 4px 12px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px); border-radius: 9999px; color: #ffffff; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        {{ $badge }}
                                    </span>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 32px;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 40px 32px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0 0 8px; color: #64748b; font-size: 13px;">
                                &copy; {{ date('Y') }} <strong>{{ config('app.name', 'Blogger4U') }}</strong>. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #94a3b8; font-size: 12px; line-height: 1.5;">
                                This is an automated notification. If you did not expect this email, you can safely ignore it or contact our support team.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
