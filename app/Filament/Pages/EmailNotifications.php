<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailNotifications extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Email Notifications';
    protected static ?string $navigationGroup = 'Tools & Utilities';
    protected static ?int    $navigationSort  = 100;
    protected static ?string $title           = 'Email Notifications';
    protected static string  $view            = 'filament.pages.email-notifications';

    /*
    |--------------------------------------------------------------------------
    | Form State
    |--------------------------------------------------------------------------
    */

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'to'      => '',
            'subject' => '',
            'body'    => '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('Compose Email')
                    ->description('Send an email directly from the dashboard using the configured SMTP settings.')
                    ->schema([
                        TextInput::make('to')
                            ->label('To (Recipient)')
                            ->email()
                            ->required()
                            ->placeholder('recipient@example.com'),

                        TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Your email subject'),

                        Textarea::make('body')
                            ->label('Message Body')
                            ->required()
                            ->rows(8)
                            ->placeholder('Write your message here…'),
                    ]),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Page Actions
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send')
                ->label('Send Email')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->action('sendEmail'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Send Logic
    |--------------------------------------------------------------------------
    */

    public function sendEmail(): void
    {
        $data = $this->form->getState();

        try {
            Mail::raw($data['body'], function ($message) use ($data) {
                $message->to($data['to'])
                    ->subject($data['subject'])
                    ->from(
                        config('mail.from.address', 'noreply@example.com'),
                        config('mail.from.name', config('app.name'))
                    );
            });

            Notification::make()
                ->title('Email Sent')
                ->body("Email successfully sent to {$data['to']}.")
                ->success()
                ->send();

            // Reset form after success
            $this->form->fill(['to' => '', 'subject' => '', 'body' => '']);
        } catch (Throwable $e) {
            Notification::make()
                ->title('Failed to Send Email')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Extra data passed to the Blade view
    |--------------------------------------------------------------------------
    */

    protected function getViewData(): array
    {
        $mailer = config('mail.default', 'log');

        $smtpConfig = [
            'Mailer'       => strtoupper($mailer),
            'Host'         => config('mail.mailers.' . $mailer . '.host', config('mail.host', '—')),
            'Port'         => config('mail.mailers.' . $mailer . '.port', config('mail.port', '—')),
            'Encryption'   => config('mail.mailers.' . $mailer . '.encryption', config('mail.encryption', '—')) ?: 'none',
            'Username'     => config('mail.mailers.' . $mailer . '.username', config('mail.username'))
                ? '••••••••' : '(not set)',
            'From Address' => config('mail.from.address', '—'),
            'From Name'    => config('mail.from.name', '—'),
        ];

        return compact('smtpConfig');
    }
}
