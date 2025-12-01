<?php

declare(strict_types=1);

namespace Tapp\FilamentFormBuilder\Observers;

use Illuminate\Support\Facades\Mail;
use Tapp\FilamentFormBuilder\Mail\FormSubmissionNotification;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

class FilamentFormUserObserver
{
    public function created(FilamentFormUser $filamentFormUser): void
    {
        $this->sendNotifications($filamentFormUser);
    }

    public function updated(FilamentFormUser $filamentFormUser): void
    {
        // Log that the updated event fired
        \Log::info('FilamentFormUserObserver::updated() fired for entry ID: '.$filamentFormUser->id);

        $this->sendNotifications($filamentFormUser);
    }

    protected function sendNotifications(FilamentFormUser $filamentFormUser): void
    {
        /** @var \Tapp\FilamentFormBuilder\Models\FilamentForm|null $form */
        $form = $filamentFormUser->filamentForm;

        // Check if notification emails are configured for this form
        if (! $form || ! $form->notification_emails) {
            \Log::info('No notification emails configured for form ID: '.($form?->id ?? 'null'));

            return;
        }

        $notificationEmails = is_array($form->notification_emails)
            ? $form->notification_emails
            : json_decode($form->notification_emails, true);

        // Filter out empty email addresses and send notifications
        $emails = array_filter($notificationEmails ?? []);

        if (empty($emails)) {
            \Log::info('Notification emails array is empty after filtering');

            return;
        }

        \Log::info('Sending notifications for entry ID: '.$filamentFormUser->id.' to: '.json_encode($emails));

        foreach ($emails as $email) {
            Mail::to($email)->queue(new FormSubmissionNotification($form, $filamentFormUser));
        }

        \Log::info('Queued '.count($emails).' notification emails for entry ID: '.$filamentFormUser->id);
    }
}
