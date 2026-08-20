<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ItAdminUserController extends Controller
{
    private const ROLES = ['Staff', 'HOD', 'SuperAdmin', 'ITAdmin'];

    /**
     * Departments shown in the create/edit user forms come from the users table itself
     * (who's actually in which department), not the separate departments reference table -
     * and ICT is deliberately excluded from this list.
     */
    private function departmentOptions()
    {
        return User::whereNotNull('department')
            ->where('department', '!=', '')
            ->where('department', '!=', 'ICT')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');
    }

    public function index(Request $request)
    {
        $activeMenu = 'itadmin_users';
        $activeDropdown = '';

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(15)->appends($request->query());
        $roles = self::ROLES;

        return view('frontend.itadmin.users.index', compact('activeMenu', 'activeDropdown', 'users', 'roles'));
    }

    public function create()
    {
        $activeMenu = 'itadmin_users';
        $activeDropdown = '';
        $departments = $this->departmentOptions();
        $roles = self::ROLES;

        return view('frontend.itadmin.users.create', compact('activeMenu', 'activeDropdown', 'departments', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department' => 'required|string|max:255',
            'role' => 'required|in:' . implode(',', self::ROLES),
            'password' => 'required|string|min:6',
            'emp_id' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'department' => $request->department,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'emp_id' => $request->emp_id,
            'designation' => $request->designation,
            'division' => $request->division,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active', true),
            'can_use_api' => $request->boolean('can_use_api'),
        ]);

        return redirect()->route('itadmin.users.index')->with([
            'message' => 'User created successfully',
            'alert-type' => 'success',
        ]);
    }

    public function edit($id)
    {
        $activeMenu = 'itadmin_users';
        $activeDropdown = '';
        $user = User::findOrFail($id);
        $departments = $this->departmentOptions();
        $roles = self::ROLES;

        return view('frontend.itadmin.users.edit', compact('activeMenu', 'activeDropdown', 'user', 'departments', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department' => 'required|string|max:255',
            'role' => 'required|in:' . implode(',', self::ROLES),
            'password' => 'nullable|string|min:6',
            'emp_id' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'division' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'department' => $request->department,
            'role' => $request->role,
            'emp_id' => $request->emp_id,
            'designation' => $request->designation,
            'division' => $request->division,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active'),
            'can_use_api' => $request->boolean('can_use_api'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('itadmin.users.index')->with([
            'message' => 'User updated successfully',
            'alert-type' => 'success',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('itadmin.users.index')->with([
                'message' => 'You cannot delete your own account.',
                'alert-type' => 'error',
            ]);
        }

        // is_active is checked independently in places that don't go through Eloquent's
        // soft-delete scope (e.g. raw DB::table('users') queries) - set it explicitly so a
        // deleted user is locked out everywhere, not just wherever the soft delete is respected.
        $user->update(['is_active' => false]);
        $user->delete();

        return redirect()->route('itadmin.users.index')->with([
            'message' => 'User deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
