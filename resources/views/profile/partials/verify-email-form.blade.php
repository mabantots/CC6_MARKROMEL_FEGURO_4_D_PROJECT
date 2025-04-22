<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Email Verification') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Please verify your email address. A verification code will be sent to your registered email.") }}
        </p>
    </header>

    {{-- Send Code Form --}}
    <form method="post" action="{{ route('make.send.code') }}" class="mt-6 space-y-6">
        @csrf

        <div class="flex items-center gap-4">
            <button
                id="sendCode"
                type="submit"
                data-status="{{ session('status') === 'verification-link-sent' ? 'sent' : '' }}"
                onclick="sendCode(event)"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md"
            >
                {{ __('Send Code') }}
            </button>

            @if (session('status') === 'verification-link-sent')
                <p class="text-sm text-green-600">{{ __('Verification code sent! Check your email.') }}</p>
            @endif
        </div>
    </form>

    {{-- Verify Code Form --}}
    <form method="post" action="{{ route('make.email.verify') }}" class="mt-6 space-y-6">
        @csrf
        @method('PATCH')

        <div>
            <x-input-label for="code" :value="__('Verification Code')" />
            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('code')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Verify') }}</x-primary-button>
        </div>

    </form>
</section>
<script>
    function sendCode(event) {
        if (event) event.preventDefault();

        let sendCodeBTN = document.getElementById('sendCode');
        let originalText = sendCodeBTN.textContent;
        let duration = 120000; // 2 minutes in milliseconds
        let start = Date.now();
        
        let savedTime = localStorage.getItem('countdownTime');
        if (savedTime) {
            start = Date.now() - savedTime;
        }

        sendCodeBTN.disabled = true;

        let interval = setInterval(() => {
            let elapsed = Date.now() - start;
            let remaining = Math.max(0, (duration - elapsed) / 1000);
            let minutes = Math.floor(remaining / 60);
            let seconds = Math.floor(remaining % 60);

            localStorage.setItem('countdownTime', remaining);

            sendCodeBTN.textContent = `Send again in ${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (remaining <= 0) {
                clearInterval(interval);
                sendCodeBTN.disabled = false;
                sendCodeBTN.textContent = originalText;

                localStorage.removeItem('countdownTime');

                if (event) {
                    sendCodeBTN.closest('form').submit();
                }
            }
        }, 1000);
    }

    window.addEventListener('DOMContentLoaded', () => {
        const sendCodeBTN = document.getElementById('sendCode');
        if (sendCodeBTN.dataset.status === 'sent') {
            sendCode();
        }
    });
</script>