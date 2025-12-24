<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the profile page (read-only).
     */
    public function index()
    {
        $user = Auth::user();
        return view('staff.profile.index', compact('user'));
    }
}
