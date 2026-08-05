<section>
    <header>
        <h2 class="text-lg font-medium" style="color: var(--color-text-primary);">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm" style="color: var(--color-text-secondary);">
            {{ __('Perbarui informasi profil dan alamat email akun Anda.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Profile Photo --}}
        <div x-data="{ photoPreview: null, removePhoto: false }">
            <x-input-label for="photo" :value="__('Foto Profil')" />
            
            <div class="my-3 flex items-center gap-4">
                {{-- Preview --}}
                <div class="relative w-16 h-16 rounded-full overflow-hidden border border-zinc-700 bg-zinc-900 flex items-center justify-center flex-shrink-0">
                    <template x-if="photoPreview && !removePhoto">
                        <img :src="photoPreview" class="w-full h-full object-cover" />
                    </template>
                    <template x-if="!photoPreview && !removePhoto">
                        @if($user->photo_url)
                            <img src="{{ asset($user->photo_url) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                        @else
                            <span class="text-xl font-bold text-zinc-400">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        @endif
                    </template>
                    <template x-if="removePhoto">
                        <span class="text-xl font-bold text-zinc-400">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </template>
                </div>

                <div class="space-y-2">
                    <input id="photo"
                           name="photo"
                           type="file"
                           accept="image/png, image/jpeg, image/jpg"
                           @change="
                               removePhoto = false;
                               const file = $event.target.files[0];
                               if (file) {
                                   const reader = new FileReader();
                                   reader.onload = (e) => { photoPreview = e.target.result; };
                                   reader.readAsDataURL(file);
                               }
                           "
                           class="block w-full text-xs text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 cursor-pointer" />
                    
                    @if($user->photo_url)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="remove_photo" name="remove_photo" value="1" x-model="removePhoto" class="rounded border-zinc-700 text-red-600 focus:ring-red-500 bg-zinc-900">
                            <label for="remove_photo" class="text-xs text-zinc-400">Hapus foto profil saat ini</label>
                        </div>
                    @endif
                    <p class="text-xs text-zinc-500">Format: JPG, JPEG, PNG (Maks. 5MB)</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="phone_number" :value="__('Nomor Telepon')" />
            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" :value="old('phone_number', $user->phone_number)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2" style="color: var(--color-text-secondary);">
                        {{ __('Alamat email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="underline text-sm rounded-md focus:outline-none" style="color: var(--color-text-muted);">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm"
                    style="color: var(--color-text-secondary);"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
