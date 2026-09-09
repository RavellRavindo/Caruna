<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Caregiver;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:client,caregiver'],
            'specialization' => ['required_if:role,caregiver', 'nullable', 'string', 'max:255'],
            'price_per_day' => ['required_if:role,caregiver', 'nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'experience_years' => ['required_if:role,caregiver', 'nullable', 'integer', 'min:0', 'max:80'],
            'gender' => ['required_if:role,caregiver', 'nullable', 'in:Laki-laki,Perempuan'],
            'about_me' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = DB::transaction(function () use ($request): User {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            if ($user->role === 'caregiver') {
                Caregiver::create([
                    'user_id' => $user->id,
                    'specialization' => $request->specialization,
                    'price_per_day' => $request->price_per_day,
                    'experience_years' => $request->experience_years,
                    'gender' => $request->gender,
                    'about_me' => $request->about_me,
                    'is_available' => false,
                    'is_verified' => false,
                    'verification_status' => Caregiver::VERIFICATION_PENDING,
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
