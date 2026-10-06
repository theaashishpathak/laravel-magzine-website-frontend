@extends('emails.layouts.master', ['badge' => 'Welcome', 'subject' => 'Welcome to ' . config('app.name', 'Blogger4U')])

@section('content')
    <h2 style="margin: 0 0 16px; color: #0f172a; font-size: 22px; font-weight: 700;">
        Welcome to the team, {{ $user->name }}! 🎉
    </h2>

    <p style="margin: 0 0 20px; color: #475569; font-size: 15px; line-height: 1.6;">
        Your account on <strong>{{ config('app.name', 'Blogger4U') }}</strong> has been successfully created. You can now start drafting articles, collaborating with editors, and publishing your stories.
    </p>

    <!-- Account Details Card -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 0 0 24px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
            <tr>
                <td style="color: #64748b; padding: 6px 0; width: 35%;"><strong>Name:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ $user->name }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Email Address:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ $user->email }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Role:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">
                    <span style="display: inline-block; padding: 2px 8px; background: #e0e7ff; color: #4338ca; border-radius: 6px; font-weight: 600; font-size: 12px;">
                        {{ $user->getRoleNames()->first() ?? 'Author' }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 30px 0 24px;">
        <a href="{{ route('login') }}"
           style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; border-radius: 10px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            Log in to Your Dashboard &rarr;
        </a>
    </div>

    <p style="margin: 0; color: #64748b; font-size: 14px; line-height: 1.6;">
        Need assistance getting started? Don't hesitate to reach out to our editorial team or reply to this email.
    </p>
@endsection
