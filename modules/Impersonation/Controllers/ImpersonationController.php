<?php

namespace Modules\Impersonation\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate($uuid)
    {
        $organization = Organization::where('uuid', $uuid)->firstOrFail();
        $ownerTeam = $organization->owner()->with('user')->first();

        if (!$ownerTeam || !$ownerTeam->user) {
            return back()->with('status', [
                'type' => 'error',
                'message' => __('Owner user not found.')
            ]);
        }

        session([
            'impersonator_id' => Auth::guard('admin')->id(),
            'impersonator_guard' => 'admin',
        ]);

        Auth::guard('user')->login($ownerTeam->user);
        session()->put('current_organization', $organization->id);

        return redirect('/dashboard');
    }

    public function stop()
    {
        if (session()->has('impersonator_id')) {
            $adminId = session()->pull('impersonator_id');
            session()->forget('current_organization');
            Auth::guard('user')->logout();
            Auth::guard('admin')->loginUsingId($adminId);
        }

        return redirect('/admin/organizations');
    }
}
