<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // Import for validation

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->fill($request->only('name', 'email'));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null; // Mark email unverified if email changes
        }

        $user->save();

        // Redirect back with a success message
        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => \Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout(); // Log out user before deleting

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Redirect to homepage or login
    }
}