<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $this->staffUser($request);

        if (! $user) {
            return redirect('/staff-login');
        }

        try {
            $user->load('profile');
            $profile = $user->profile ?: $user->profile()->create();
        } catch (QueryException $exception) {
            return redirect('/staff-login')
                ->withErrors(['database' => 'Database is not connected. Please start MySQL in XAMPP and try again.']);
        }

        return view('profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = $this->staffUser($request);

        if (! $user) {
            return redirect('/staff-login');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        try {
            $profile = $user->profile ?: $user->profile()->create();
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->withErrors(['database' => 'Database is not connected. Please start MySQL in XAMPP and try again.']);
        }

        if ($request->hasFile('avatar')) {
            if ($profile->avatar_path) {
                Storage::disk('public')->delete($profile->avatar_path);
            }

            $data['avatar_path'] = $request->file('avatar')->store('profiles', 'public');
        }

        try {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            $profile->update([
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'bio' => $data['bio'] ?? null,
                'avatar_path' => $data['avatar_path'] ?? $profile->avatar_path,
            ]);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->withErrors(['database' => 'Database is not connected. Please start MySQL in XAMPP and try again.']);
        }

        return redirect('/profile')->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = $this->staffUser($request);

        if (! $user) {
            return redirect('/staff-login');
        }

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        try {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        } catch (QueryException $exception) {
            return back()
                ->withErrors(['database' => 'Database is not connected. Please start MySQL in XAMPP and try again.']);
        }

        return redirect('/profile')->with('success', 'Password updated.');
    }

    private function staffUser(Request $request): ?User
    {
        $userId = $request->session()->get('staff_user_id');

        try {
            return $userId ? User::find($userId) : null;
        } catch (QueryException $exception) {
            return null;
        }
    }
}
