<?php

namespace App\Http\Controllers\Dashboard;

use App\Rules\MatchPassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PanelController extends Controller
{
    public function index()
    {
        return view('dashboard.panel');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchPassword(Auth::user()->password)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if(Auth::user()->changePassword($request->password)){
            flash()->success(__('Password changed successfully'));
            return redirect()->route('dashboard.profile');
        }

        flash()->error(__('Could not change the password'));
        return back();
    }

    public function profile(Request $request)
    {
        if(Auth::user()->updateProfile($request->all())){
            flash()->success(__('Profile updated successfully'));
        }else{
            flash()->error(__('Could not update the profile'));
        }
        return back();
    }

    public function showChangePasswordForm()
    {
        return view('dashboard.change-password');
    }

    public function showProfileForm()
    {
        return view('dashboard.profile');
    }
}
