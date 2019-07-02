<?php

namespace App\Http\Controllers\Dashboard;

use App\Rules\MatchPassword;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

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

    public function lockScreen(Request $request)
    {
        $request->session()->put('after_unlock_url', URL::previous(false));
        $request->session()->put('protect_session', true);
        return redirect()->route('dashboard.locked-screen');
    }

    public function lockedScreenForm(Request $request)
    {
        if(!$request->session()->has('protect_session')){
            return redirect()->route('dashboard.panel');
        }
        return view('dashboard.locked-screen');
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

    public function unlockScreen(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);
        /** @var User $user */
        $user = $request->session()->has('admin_id') ? User::find($request->session()->get('admin_id')) : Auth::user();
        if(Hash::check($request->password, $user->password)){
            $url = $request->session()->get('after_unlock_url', route('dashboard.panel'));
            $request->session()->forget(['after_unlock_url', 'protect_session']);
            return redirect($url);
        }
        return back()->withErrors(['password' => 'Invalid password']);
    }
}
