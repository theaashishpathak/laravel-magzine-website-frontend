@extends('emails.layouts.master', ['badge' => 'Admin Alert', 'subject' => '[Alert] New User Registered: ' . $user->name])

@section('content')
    <h2 style="margin: 0 0 16px; color: #0f172a; font-size: 22px; font-weight: 700;">
        New User Account Created
    </h2>

    <p style="margin: 0 0 20px; color: #475569; font-size: 15px; line-height: 1.6;">
        A new user account has been registered on <strong>{{ config('app.name', 'Blogger4U') }}</strong>.
    </p>

    <!-- Details Card -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 0 0 24px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
            <tr>
                <td style="color: #64748b; padding: 6px 0; width: 35%;"><strong>User Name:</strong></td>
                <td style="color: #0f172a; padding: 6px 0; font-weight: 600;">{{ $user->name }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Email Address:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ $user->email }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Role:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">
                    <span style="display: inline-block; padding: 2px 8px; background: #e0e7ff; color: #4338ca; border-radius: 6px; font-weight: 600; font-size: 12px;">
                        {{ $user->getRoleNames()->join(', ') ?: 'Author' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Portal Type:</strong></td>
                <td style="color: #0f172a; padding: 6px 0; text-transform: capitalize;">{{ $user->portal_type ?? 'author' }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Registered At:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ now()->toDayDateTimeString() }}</td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 30px 0 20px;">
        <a href="{{ route('admin.staff.index') }}"
           style="display: inline-block; padding: 13px 28px; background: #0f172a; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; border-radius: 10px;">
            Manage Users in Admin Panel &rarr;
        </a>
    </div>
@endsection
