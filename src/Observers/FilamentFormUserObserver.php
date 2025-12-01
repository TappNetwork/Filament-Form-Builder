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
        $this->sendNotifications($filamentFormUser);
    }

    protected function sendNotifications(FilamentFormUser $filamentFormUser): void
    {
        /** @var \Tapp\FilamentFormBuilder\Models\FilamentForm|null $form */
        $form = $filamentFormUser->filamentForm;

        // Check if notification emails are configured for this form
        if (! $form || ! $form->notification_emails) {
            return;
        }

        $notificationEmails = is_array($form->notification_emails)
            ? $form->notification_emails
            : json_decode($form->notification_emails, true);

        // Filter out empty email addresses and send notifications
        $emails = array_filter($notificationEmails ?? []);

        if (empty($emails)) {
            return;
        }

        foreach ($emails as $email) {
            Mail::to($email)->queue(new FormSubmissionNotification($form, $filamentFormUser));
        }
    }
}
