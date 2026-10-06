<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\EmailDeliveryLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Email;
use Throwable;

class LogOutgoingEmail
{
    private const HEADER_TRACKING_ID = 'X-Delivery-Log-ID';

    /**
     * Handle MessageSending event (queued or immediate attempt).
     */
    public function handleSending(MessageSending $event): void
    {
        try {
            /** @var Email $email */
            $email = $event->message;

            $recipients = $email->getTo();
            if (empty($recipients)) {
                return;
            }

            $primaryRecipient = $recipients[0];
            $recipientEmail = $primaryRecipient->getAddress();
            $recipientName = $primaryRecipient->getName() ?: null;

            $from = $email->getFrom();
            $senderEmail = !empty($from) ? $from[0]->getAddress() : config('mail.from.address');

            $subject = (string) ($email->getSubject() ?? '(No Subject)');
            $mailer = (string) config('mail.default', 'smtp');

            $log = EmailDeliveryLog::create([
                'recipient_email' => $recipientEmail,
                'recipient_name' => $recipientName,
                'sender_email' => $senderEmail,
                'subject' => $subject,
                'mailer' => $mailer,
                'status' => EmailDeliveryLog::STATUS_PENDING,
                'metadata' => [
                    'all_recipients' => array_map(fn ($r) => $r->getAddress(), $recipients),
                    'queued_at' => now()->toIso8601String(),
                ],
            ]);

            // Stamp tracking header so handleSent can match with 100% precision
            $email->getHeaders()->addTextHeader(self::HEADER_TRACKING_ID, (string) $log->id);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Handle MessageSent event (successful transport).
     */
    public function handleSent(MessageSent $event): void
    {
        try {
            /** @var Email $email */
            $email = $event->message;

            $trackingHeader = $email->getHeaders()->get(self::HEADER_TRACKING_ID);
            $logId = $trackingHeader ? (int) $trackingHeader->getBodyAsString() : null;

            if ($logId) {
                EmailDeliveryLog::where('id', $logId)->update([
                    'status' => EmailDeliveryLog::STATUS_DELIVERED,
                    'sent_at' => now(),
                    'error_message' => null,
                ]);
                return;
            }

            // Fallback: match by most recent pending entry for recipient
            $recipients = $email->getTo();
            if (!empty($recipients)) {
                $recipientEmail = $recipients[0]->getAddress();
                $subject = (string) ($email->getSubject() ?? '');

                EmailDeliveryLog::query()
                    ->where('recipient_email', $recipientEmail)
                    ->where('subject', $subject)
                    ->where('status', EmailDeliveryLog::STATUS_PENDING)
                    ->latest('id')
                    ->first()
                    ?->update([
                        'status' => EmailDeliveryLog::STATUS_DELIVERED,
                        'sent_at' => now(),
                        'error_message' => null,
                    ]);
            }
        } catch (Throwable $e) {
            report($e);
        }
    }
}
