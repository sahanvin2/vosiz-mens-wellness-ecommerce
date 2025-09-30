<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();
        
        if ($user && $user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user && $user->role === 'supplier') {
            return redirect()->intended(route('supplier.dashboard'));
        }
        
        return redirect()->intended(route('dashboard'));
    }
}