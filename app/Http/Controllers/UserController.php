<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\VehicleAssignment;
class UserController extends Controller
{
    // Display all users
    public function index(Request $request)
    {
        // Grab the 'search' query param, expected to be user ID or partial ID
        $search = $request->query('search');
        $query = User::with(['role', 'branch'])->latest();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $users = $query->get();
        $roles = UserRole::all();
        $branches = Branch::all();

        return view('dashboard.super-admin.role-management', compact('users', 'roles', 'branches'));
    }


    // Show form to create a new user
    // public function create()
    // {
    //     $roles = UserRole::all();
    //     $branches = Branch::all();
    //     return ['roles' => $roles, 'branches' => $branches];
    // }

    // Store new user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|unique:users,contact',
            'username' => 'string|unique:users,username',
            'password' => 'required|string|min:6',
            'branch_id' => 'nullable|exists:branches,id',
            'role_id' => 'required|exists:user_roles,id',
        ]);
        User::create($validated);
        return redirect()->route('users.role-management')->with('success', 'User created successfully.');
    }

    // Show form to edit a user
    // public function edit(User $user)
    // {
    //     $roles = UserRole::all();
    //     $branches = Branch::all();
    //     return view('users.edit', compact('user', 'roles', 'branches'));
    // }

    //Update user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => ['required', 'string', Rule::unique('users', 'contact')->ignore($user->id)],
            'username' => ['nullable', 'string', Rule::unique('users', 'username')->ignore($user->id)],
            'branch_id' => 'nullable|exists:branches,id',
            'role_id' => 'nullable|exists:user_roles,id',
            'password' => 'nullable|string|min:6',
        ]);
        if (!$request->filled('password')) {
            unset($validated['password']);
        }
        if (!$request->filled('role_id')) {
            unset($validated['role_id']);
        }
        if (!$request->filled('branch_id')) {
            unset($validated['branch_id']);
        }
        $user->update($validated);
        return redirect()->route('users.role-management')->with('success', 'User Updated Successfully');
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        // ✅ Check and end only if active assignments exist
        $activeAssignments = VehicleAssignment::where('user_id', $user->id)
            ->whereNull('returned_date');
        if ($activeAssignments->exists()) {
            $activeAssignments->update(['returned_date' => now()]);
        }
        // ✅ Soft-delete the user
        $user->delete();
        return response()->json(['message' => 'User deleted and active assignment(s) ended (if any).'], 200);
    }



}
