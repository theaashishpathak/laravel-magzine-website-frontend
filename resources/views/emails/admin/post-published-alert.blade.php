@extends('emails.layouts.master', ['badge' => 'Editorial Alert', 'subject' => '[Editorial Alert] Post Published: ' . $title])

@section('content')
    <h2 style="margin: 0 0 16px; color: #0f172a; font-size: 22px; font-weight: 700;">
        Article Published Live
    </h2>

    <p style="margin: 0 0 20px; color: #475569; font-size: 15px; line-height: 1.6;">
        An article has gone live on <strong>{{ config('app.name', 'Blogger4U') }}</strong>.
    </p>

    <!-- Details Card -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 0 0 24px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
            <tr>
                <td style="color: #64748b; padding: 6px 0; width: 35%;"><strong>Article Title:</strong></td>
                <td style="color: #0f172a; padding: 6px 0; font-weight: 700;">{{ $title }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Author:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ $authorName }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Published By:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ $publisherName }}</td>
            </tr>
            @if(!empty($categoryName))
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Category:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ $categoryName }}</td>
            </tr>
            @endif
            <tr>
                <td style="color: #64748b; padding: 6px 0;"><strong>Published At:</strong></td>
                <td style="color: #0f172a; padding: 6px 0;">{{ now()->toDayDateTimeString() }}</td>
            </tr>
        </table>
    </div>

    <!-- CTA Buttons -->
    <div style="text-align: center; margin: 30px 0 20px;">
        <a href="{{ $postUrl }}"
           style="display: inline-block; padding: 13px 24px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; border-radius: 10px; margin-right: 8px;">
            View Live Post &rarr;
        </a>
        <a href="{{ $adminEditUrl }}"
           style="display: inline-block; padding: 13px 24px; background: #0f172a; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; border-radius: 10px;">
            Edit in Admin &rarr;
        </a>
    </div>
@endsection
