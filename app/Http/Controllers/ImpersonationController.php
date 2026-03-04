<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating another user.
     */
    public function enter(User $user)
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        // 1. Check if the current user is allowed to impersonate.
        if (! $currentUser || ! $currentUser->canImpersonate()) {
            abort(403, 'You do not have permission to impersonate users.');
        }

        // 2. Prevent impersonating other Admins/Super Admins to avoid privilege escalation.
        if ($user->canImpersonate()) {
            abort(403, 'You cannot impersonate another admin or super admin.');
        }

        // 3. Store the original admin ID in the session.
        session()->put('impersonator_id', $currentUser->id);

        // 4. Log in as the target user.
        Auth::login($user);

        // 5. Redirect to the client area.
        return redirect()->to('/client-area');
    }

    /**
     * Stop impersonating and return to the admin panel.
     */
    public function leave()
    {
        if (! session()->has('impersonator_id')) {
            return redirect()->to('/client-area');
        }

        $adminId = session()->pull('impersonator_id');
        $admin = User::find($adminId);

        if ($admin) {
            Auth::login($admin);
            return redirect()->to('/rbdashboard/users'); // Redirect back to User Management
        }

        // Fallback in case the admin was deleted during the session
        Auth::logout();
        return redirect('/');
    }
}
