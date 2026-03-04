@if (session()->has('impersonator_id'))
<div style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ef4444; color: white; z-index: 999999; display: flex; align-items: center; justify-content: center; padding: 10px 20px; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4); font-family: sans-serif; font-size: 14px; gap: 16px;">
    <div style="display: flex; align-items: center; gap: 8px; font-weight: 500;">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        Impersonation Mode Active
    </div>
    <div style="opacity: 0.9;">
        You are currently logged in as <strong>{{ auth()->user()->name }}</strong>. Actions taken here will be recorded under their account.
    </div>
    <a href="{{ route('impersonate.leave') }}" style="background: white; color: #ef4444; padding: 6px 14px; border-radius: 99px; text-decoration: none; font-weight: bold; font-size: 13px; transition: transform 0.2s, background 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        Return to Admin
    </a>
</div>
<!-- Push body down to prevent overlap -->
<style>
    body {
        padding-top: 50px !important;
    }
</style>
@endif