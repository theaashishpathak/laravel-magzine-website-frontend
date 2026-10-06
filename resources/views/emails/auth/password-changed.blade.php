@extends('emails.layouts.master', ['badge' => 'Security Notice', 'subject' => 'Security Alert: Your Password Was Changed'])

@section('content')
    <h2 style="margin: 0 0 16px; color: #0f172a; font-size: 22px; font-weight: 700;">
        Your Password Was Changed
    </h2>

    <p style="margin: 0 0 20px; color: #475569; font-size: 15px; line-height: 1.6;">
        Hello <strong>{{ $user->name }}</strong>, this is an automated confirmation that the password for your account (<strong>{{ $user->email }}</strong>) was recently modified.
    </p>

    <!-- Details Card -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 0 0 24px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
            <tr>
                <td style="color: #64748b; padding: 6px 0; width: 35%;"><strong>Account:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ $user->email }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Date & Time:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ now()->toDayDateTimeString() }}</td>
            </tr>
        </table>
    </div>

    <!-- Security Warning Alert -->
    <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px 20px; border-radius: 6px; margin: 0 0 24px;">
        <p style="margin: 0 0 8px; color: #991b1b; font-size: 14px; font-weight: 700;">
            Did not make this change?
        </p>
        <p style="margin: 0; color: #b91c1c; font-size: 13px; line-height: 1.5;">
            If you did not request or perform this password update, someone else may have gained unauthorized access to your account. Please reset your password immediately and contact support.
        </p>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 30px 0 20px;">
        <a href="{{ route('password.request') }}"
           style="display: inline-block; padding: 13px 28px; background: #dc2626; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; border-radius: 10px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);">
            Secure My Account (Reset Password) &rarr;
        </a>
    </div>

    <p style="margin: 0; color: #64748b; font-size: 13px; text-align: center;">
        If you made this change yourself, no further action is required.
    </p>
@endsection
