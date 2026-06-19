<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Chama;  // ← This fixes the error
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
    public function create(): View
    {
        // Pass list of Chamas to the registration view (if you want a dropdown)
        $chamas = Chama::all();
        return view('auth.register', compact('chamas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'registration_type' => ['nullable', 'in:join,create'],
            'chama_id' => ['nullable', 'exists:chamas,id'],
            'new_chama_name' => ['required_if:registration_type,create', 'nullable', 'string', 'max:255'],
        ]);

        $registrationType = $request->input('registration_type', 'join');
        $role = 'member';
        $chamaId = null;

        if ($registrationType === 'create') {
            $chama = Chama::create([
                'name' => $request->new_chama_name,
                'location' => 'Nairobi',
                'currency' => 'KES',
                'min_credit_score' => 5.0,
                'interest_rate_pct' => 5.0,
                'savings_weight' => 0.40,
                'attendance_weight' => 0.20,
                'repayment_weight' => 0.40,
            ]);
            $chamaId = $chama->id;
            $role = 'treasurer';
        } else {
            if ($request->filled('chama_id')) {
                $chamaId = $request->chama_id;
            } else {
                $chama = Chama::first();
                if (!$chama) {
                    $chama = Chama::create([
                        'name' => 'Default Chama',
                        'location' => 'Nairobi',
                        'currency' => 'KES',
                        'min_credit_score' => 5.0,
                        'interest_rate_pct' => 5.0,
                        'savings_weight' => 0.40,
                        'attendance_weight' => 0.20,
                        'repayment_weight' => 0.40,
                    ]);
                }
                $chamaId = $chama->id;
            }
            $role = 'member';
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'chama_id' => $chamaId,
            'account_status' => 'active',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}