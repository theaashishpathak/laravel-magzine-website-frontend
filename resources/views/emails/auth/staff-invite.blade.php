@extends('emails.layouts.master', ['badge' => 'Staff Invitation', 'subject' => 'You have been invited to ' . config('app.name', 'Blogger4U')])

@section('content')
    <h2 style="margin: 0 0 16px; color: #0f172a; font-size: 22px; font-weight: 700;">
        You've Been Invited to {{ config('app.name', 'Blogger4U') }}
    </h2>

    <p style="margin: 0 0 20px; color: #475569; font-size: 15px; line-height: 1.6;">
        Hello <strong>{{ $user->name }}</strong>, an administrator has created an account for you on <strong>{{ config('app.name', 'Blogger4U') }}</strong>.
    </p>

    <!-- Credentials Card -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; margin: 0 0 24px;">
        <h3 style="margin: 0 0 14px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">
            Your Login Credentials
        </h3>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
            <tr>
                <td style="color: #64748b; padding: 6px 0; width: 35%;"><strong>Login Email:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;"><strong>{{ $user->email }}</strong></td>
            </tr>
            @if(!empty($temporaryPassword))
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Temporary Password:</strong></td>
                <td style="padding: 6px 0;">
                    <code style="background-color: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-family: monospace; font-size: 15px; font-weight: 700; color: #4f46e5; border: 1px dashed #cbd5e1;">
                        {{ $temporaryPassword }}
                    </code>
                </td>
            </tr>
            @endif
            @if($user->job_title)
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Job Title:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ $user->job_title }}</td>
            </tr>
            @endif
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Roles:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">
                    {{ $user->getRoleNames()->join(', ') ?: 'Staff' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Security Note -->
    <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 14px 16px; border-radius: 6px; margin: 0 0 24px;">
        <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.5;">
            <strong>Important:</strong> For security purposes, please log in and change your temporary password immediately in your account settings.
        </p>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 30px 0 24px;">
        <a href="{{ route('login') }}"
           style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; border-radius: 10px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            Log In to Your Account &rarr;
        </a>
    </div>
@endsection
