<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        {{-- Read-only Chama Profile Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 rounded-xl bg-slate-900 border border-slate-800 text-white">
            <div>
                <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Chama Group</span>
                <span class="text-sm font-semibold text-gold-400">{{ $user->chama->name ?? 'None' }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">System Role</span>
                <span class="text-sm font-semibold capitalize text-slate-300">{{ $user->role ?? 'Guest' }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Account Status</span>
                @php
                    $statusColors = [
                        'active'  => 'text-emerald-400 font-bold',
                        'overdue' => 'text-rose-400 font-bold animate-pulse',
                    ];
                    $color = $statusColors[strtolower($user->account_status)] ?? 'text-slate-300';
                @endphp
                <span class="text-sm capitalize {{ $color }}">{{ $user->account_status ?? 'Active' }}</span>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-slate-300">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gold-500 hover:text-gold-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500">
                             {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone Number (M-Pesa SMS Mapping)')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" placeholder="e.g. 254712345678" />
            <p class="text-[10px] text-slate-400 mt-1">Must start with 254 or 0 (e.g. 254712345678). Used to match M-Pesa SMS records to your account.</p>
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="national_id" :value="__('National ID Number')" />
            <x-text-input id="national_id" name="national_id" type="text" class="mt-1 block w-full" :value="old('national_id', $user->national_id)" placeholder="e.g. 12345678" />
            <x-input-error class="mt-2" :messages="$errors->get('national_id')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-400 font-semibold"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
