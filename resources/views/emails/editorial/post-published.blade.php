@extends('emails.layouts.master', ['badge' => $isAuthor ? 'Post Published' : 'New Story', 'subject' => ($isAuthor ? 'Your post has been published: ' : 'New Article: ') . $title])

@section('content')
    <h2 style="margin: 0 0 16px; color: #0f172a; font-size: 22px; font-weight: 700;">
        @if($isAuthor)
            Congratulations! Your post is live 🚀
        @else
            New story from {{ $authorName }}
        @endif
    </h2>

    <p style="margin: 0 0 20px; color: #475569; font-size: 15px; line-height: 1.6;">
        @if($isAuthor)
            Your article <strong>"{{ $title }}"</strong> has been reviewed and officially published on <strong>{{ config('app.name', 'Blogger4U') }}</strong>.
        @else
            An article you might like by <strong>{{ $authorName }}</strong> has just been published on <strong>{{ config('app.name', 'Blogger4U') }}</strong>.
        @endif
    </p>

    <!-- Post Preview Card -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; margin: 0 0 24px;">
        @if(!empty($coverImage))
            <div style="margin: 0 0 16px; text-align: center;">
                <img src="{{ $coverImage }}" alt="{{ $title }}" style="max-width: 100%; border-radius: 8px; max-height: 240px; object-fit: cover;">
            </div>
        @endif

        <h3 style="margin: 0 0 10px; color: #0f172a; font-size: 18px; font-weight: 700;">
            {{ $title }}
        </h3>

        @if(!empty($excerpt))
            <p style="margin: 0 0 14px; color: #64748b; font-size: 14px; line-height: 1.6;">
                {{ $excerpt }}
            </p>
        @endif

        <div style="font-size: 13px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 12px;">
            <span>By <strong>{{ $authorName }}</strong></span>
            @if(!empty($categoryName))
                &bull; <span>{{ $categoryName }}</span>
            @endif
        </div>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 30px 0 20px;">
        <a href="{{ $postUrl }}"
           style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; border-radius: 10px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
            Read Full Article &rarr;
        </a>
    </div>
@endsection
