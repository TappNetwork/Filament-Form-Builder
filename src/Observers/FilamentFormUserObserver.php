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

        if (! $form || ! $form->notification_emails) {
            return;
        }

        $emails = array_filter($form->notification_emails);

        if (empty($emails)) {
            return;
        }

        foreach ($emails as $email) {
            Mail::to($email)->queue(new FormSubmissionNotification($form, $filamentFormUser));
        }
    }
}
