<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Compose Form ── --}}
        <div class="lg:col-span-2">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-envelope class="h-5 w-5 text-indigo-500" />
                        Compose Email
                    </div>
                </x-slot>
                <x-slot name="description">
                    Send an email directly from the dashboard using the configured SMTP settings below.
                </x-slot>

                {{ $this->form }}

                <div class="mt-6 flex justify-end">
                    <x-filament::button
                        wire:click="sendEmail"
                        wire:loading.attr="disabled"
                        icon="heroicon-o-paper-airplane"
                        color="primary"
                        size="lg">
                        <span wire:loading.remove wire:target="sendEmail">Send Email</span>
                        <span wire:loading wire:target="sendEmail">Sending…</span>
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>

        {{-- ── SMTP Info ── --}}
        <div class="lg:col-span-1">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-cog-6-tooth class="h-5 w-5 text-gray-400" />
                        SMTP Configuration
                    </div>
                </x-slot>
                <x-slot name="description">
                    Read-only. Edit values in your <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">.env</code> file.
                </x-slot>

                <dl class="space-y-3">
                    @foreach ($smtpConfig as $label => $value)
                    <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-3">
                        <dt class="w-32 flex-shrink-0 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pt-0.5">
                            {{ $label }}
                        </dt>
                        <dd class="font-mono text-sm text-gray-800 dark:text-gray-100 break-all">
                            {{ $value }}
                        </dd>
                    </div>
                    @endforeach
                </dl>

                <div class="mt-5 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-3">
                    <div class="flex gap-2">
                        <x-heroicon-o-exclamation-triangle class="h-4 w-4 text-yellow-500 flex-shrink-0 mt-0.5" />
                        <div class="text-xs text-yellow-700 dark:text-yellow-300">
                            <p class="font-semibold">Using <code>MAIL_MAILER=log</code>?</p>
                            <p class="mt-0.5">Emails will be written to <code>storage/logs/laravel.log</code> instead of being delivered. Set real SMTP credentials in <code>.env</code> to send actual emails.</p>
                        </div>
                    </div>
                </div>

                {{-- SMTP Variable Reference --}}
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        .env Variables
                    </p>
                    <pre class="text-xs font-mono bg-gray-100 dark:bg-gray-800 rounded-lg p-3 overflow-x-auto text-gray-700 dark:text-gray-200 leading-relaxed">MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=user@example.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"</pre>
                </div>
            </x-filament::section>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>