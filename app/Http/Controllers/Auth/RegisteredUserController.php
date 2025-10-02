<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'position' => ['required', 'string', 'max:255'],
            'project_name' => ['required', 'string', 'max:255'],
            'project_company' => ['required', 'string', 'max:255'],
            'contractor_name' => ['required', 'string', 'max:255'],
            'contractor_supervisor_name' => ['required', 'string', 'max:255'],
            'contractor_supervisor_title' => ['required', 'string', 'max:255'],
            'project_supervisor_name' => ['required', 'string', 'max:255'],
            'project_supervisor_title' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'position' => $validated['position'],
            'project_name' => $validated['project_name'],
            'project_company' => $validated['project_company'],
            'contractor_name' => $validated['contractor_name'],
            'contractor_supervisor_name' => $validated['contractor_supervisor_name'],
            'contractor_supervisor_title' => $validated['contractor_supervisor_title'],
            'project_supervisor_name' => $validated['project_supervisor_name'],
            'project_supervisor_title' => $validated['project_supervisor_title'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

