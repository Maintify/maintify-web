<section>
    <header>
        <h2 class="text-lg font-medium" style="color: var(--color-text-primary);">
            {{ __('Pengaturan Notifikasi') }}
        </h2>

        <p class="mt-1 text-sm" style="color: var(--color-text-secondary);">
            {{ __('Atur bagaimana Anda ingin menerima pengingat dan pemberitahuan.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.notifications.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-4">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_service_reminders" name="enable_service_reminders" type="checkbox" value="1" {{ old('enable_service_reminders', $user->enable_service_reminders) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_service_reminders" class="font-medium" style="color: var(--color-text-primary);">{{ __('Aktifkan Pengingat Servis (In-App)') }}</label>
                    <p style="color: var(--color-text-muted);">{{ __('Terima pemberitahuan langsung di aplikasi saat kendaraan Anda mendekati atau melewati jadwal servis berkala.') }}</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="enable_email_notifications" name="enable_email_notifications" type="checkbox" value="1" {{ old('enable_email_notifications', $user->enable_email_notifications) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                </div>
                <div class="ml-3 text-sm">
                    <label for="enable_email_notifications" class="font-medium" style="color: var(--color-text-primary);">{{ __('Aktifkan Notifikasi Email') }}</label>
                    <p style="color: var(--color-text-muted);">{{ __('Terima pemberitahuan dan rangkuman aktivitas akun Anda melalui email.') }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'notifications-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm"
                    style="color: var(--color-text-secondary);"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
