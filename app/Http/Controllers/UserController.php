<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Exclude customers from User & Role Access Management page
        $query = User::whereIn('role', ['super_admin', 'admin', 'staff'])->withCount('orders');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $roleCounts = [
            'super_admin' => User::where('role', 'super_admin')->count(),
            'admin'       => User::where('role', 'admin')->count(),
            'staff'       => User::where('role', 'staff')->count(),
        ];

        return view('users.index', compact('users', 'roleCounts'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:super_admin,admin,staff',
        ]);

        User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Staff/Admin account for {$request->name} created successfully with role " . strtoupper($request->role) . "!");
    }

    public function edit($id)
    {
        $user = User::whereIn('role', ['super_admin', 'admin', 'staff'])->findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::whereIn('role', ['super_admin', 'admin', 'staff'])->findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'  => 'required|in:super_admin,admin,staff',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name'  => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', "User profile for {$user->name} updated successfully!");
    }

    public function destroy($id)
    {
        $user = User::whereIn('role', ['super_admin', 'admin', 'staff'])->findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own logged-in account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User account for {$user->name} deleted successfully.");
    }

    /**
     * Impersonate a Staff or Admin user account (Super Admin feature)
     */
    public function impersonate($id)
    {
        $targetUser = User::whereIn('role', ['super_admin', 'admin', 'staff'])->findOrFail($id);

        if ($targetUser->id === auth()->id()) {
            return redirect()->back()->with('error', 'You are already logged into this account.');
        }

        if (!session()->has('impersonator_id')) {
            session(['impersonator_id' => auth()->id()]);
        }

        auth()->login($targetUser);

        return redirect()->route('dashboard')
            ->with('success', "Impersonating account: {$targetUser->name} (" . strtoupper($targetUser->role) . ").");
    }

    /**
     * Stop impersonating and return to original Super Admin account
     */
    public function stopImpersonating()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $superAdmin = User::findOrFail(session('impersonator_id'));
        session()->forget('impersonator_id');
        auth()->login($superAdmin);

        return redirect()->route('users.index')
            ->with('success', "Stopped impersonation. Returned to your Super Admin account ({$superAdmin->name}).");
    }
}
