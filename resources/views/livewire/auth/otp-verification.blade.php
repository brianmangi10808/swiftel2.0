<div>
    <div style="display:flex; justify-content:center; align-items:center; min-height:100vh; background:#f3f4f6;">
        <div style="background:white; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.1); padding:2rem; width:100%; max-width:420px; margin:1rem;">
            
            {{-- Logo --}}
            <div style="text-align:center; margin-bottom:1.5rem;">
                <h1 style="color:#4FDF54; font-size:1.8rem; font-weight:700;">Swiftel</h1>
            </div>

            {{-- Title --}}
            <div style="text-align:center; margin-bottom:1.5rem;">
                <h2 style="font-size:1.4rem; color:#111827; font-weight:600;">OTP Verification</h2>
                <p style="color:#6b7280; font-size:0.875rem; margin-top:0.5rem;">
                    We've sent a 6-digit code to<br>
                    <strong style="color:#111827;">{{ $email }}</strong>
                </p>
            </div>

            {{-- Form --}}
            <form wire:submit="verify">
                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.875rem; font-weight:500; color:#374151; margin-bottom:0.5rem;">
                        Enter OTP Code
                    </label>
                    <input 
                        type="text" 
                        wire:model="data.otp"
                        placeholder="000000"
                        maxlength="6"
                        autocomplete="one-time-code"
                        autofocus
                        style="width:100%; padding:0.75rem 1rem; border:1px solid #d1d5db; border-radius:8px; font-size:1.5rem; letter-spacing:0.5rem; text-align:center; outline:none;"
                    >
                    @error('data.otp')
                        <p style="color:#ef4444; font-size:0.8rem; margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <button 
                    type="submit"
                    style="width:100%; background:#4FDF54; color:white; border:none; padding:0.75rem; border-radius:8px; font-size:1rem; font-weight:600; cursor:pointer; margin-top:1rem;"
                >
                    <span wire:loading.remove>Verify OTP</span>
                    <span wire:loading>Verifying...</span>
                </button>
            </form>

            {{-- Links --}}
            <div style="text-align:center; margin-top:1.5rem;">
                <button 
                    wire:click="resendOtp" 
                    type="button"
                    style="color:#4FDF54; font-size:0.875rem; background:none; border:none; cursor:pointer; display:block; margin:0.5rem auto;"
                >
                    Didn't receive OTP? Resend
                </button>
                <a 
                    href="{{ route('filament.admin.auth.login') }}"
                    style="color:#6b7280; font-size:0.875rem; text-decoration:none; display:block; margin-top:0.5rem;"
                >
                    ← Back to Login
                </a>
            </div>

        </div>
    </div>
</div>