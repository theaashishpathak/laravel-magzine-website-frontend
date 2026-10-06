<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\EmailDeliveryLog;
use Illuminate\Queue\Events\JobFailed;
use Throwable;

class LogFailedEmailJob
{
    /**
     * Handle queue job failures for mail/notification tasks.
     */
    public function handle(JobFailed $event): void
    {
        try {
            $jobName = $event->job->resolveName();

            // Check if this failed job is a mail or notification dispatch
            if (str_contains($jobName, 'Mail') || str_contains($jobName, 'Notification')) {
                $exception = $event->exception;
                $errorMessage = $exception ? $exception->getMessage() : 'Queue job failed without explicit exception.';

                // Find any recently created pending logs (within last 10 minutes)
                EmailDeliveryLog::query()
                    ->where('status', EmailDeliveryLog::STATUS_PENDING)
                    ->where('created_at', '>=', now()->subMinutes(10))
                    ->latest('id')
                    ->first()
                    ?->update([
                        'status' => EmailDeliveryLog::STATUS_FAILED,
                        'error_message' => $errorMessage,
                    ]);
            }
        } catch (Throwable $e) {
            report($e);
        }
    }
}
