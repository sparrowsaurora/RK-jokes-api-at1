<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasRole('Staff')) {
            // Staff only sees their own assigned clients
            $users = User::where('assigned_staff_id', auth()->id())->paginate(10);
        } else {
            // Admins see all users
            $users = User::paginate(10);
        }

        return view('users.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->hasAnyRole(['Staff', 'Administrator'])) {
            $roles = Role::where('name', 'Client')->get(); // Only allow Client role
        } else {
            $roles = Role::all();
        }

        return view('users.create', compact('roles'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'given_name' => $request->input('given_name'),
            'family_name' => $request->input('family_name'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
        ]);

        if (auth()->user()->hasRole('Staff')) {
            $user->assignRole('Client');
        } else {
            $user->assignRole($request->role);
        }

        return redirect()->route('users.index')->with('success', 'User created and assigned Client role.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (auth()->user()->hasRole('Staff')) {
            if ($user->hasRole(['Staff', 'Administrator'])) {
                abort(403, 'Access denied.');
            }
        }

        $roles = auth()->user()->hasRole('Staff')
            ? Role::where('name', 'Client')->get()
            : Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        echo 1;
        if (auth()->user()->hasRole('Staff') && !$user->hasRole('Client')) {
            abort(403);
        }
        echo 2;
        $user->update($request->validated());
        echo 3;
        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->user()->hasRole('Staff') && $user->hasAnyRole(['Staff', 'Administrator'])) {
            abort(403, 'Access denied.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }


    public function delete(User $user)
    {
        if (auth()->user()->hasRole('Staff') && !$user->hasRole('Client')) {
            abort(403);
        }
        $roles = auth()->user()->hasRole('Staff')
            ? Role::where('name', 'Client')->get()
            : Role::all();

        return view('users.delete', compact('user'));
    }

//    public function trash(): View
//    {
//        $users = User::onlyTrashed()->latest()->get();
//        return view('users.trash', compact('users'));
//    }

    public function trash(): View
    {
        if (auth()->user()->hasRole('Staff')) {
            // Staff only sees their own assigned clients
            $users = User::where('assigned_staff_id', auth()->id())->paginate(10);
        } else {
            // Admins see all users
            $users = User::paginate(10);
        }

        return view('users.trash', compact('users'));
    }


    public function recoverOne($id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('users.trash')->with('status', 'User restored.');
    }

    public function emptyAll(): RedirectResponse
    {
        $users = User::onlyTrashed()->get();
        foreach ($users as $user) {
            $user->forceDelete();
        }
        return redirect()->route('users.trash')->with('status', 'All trashed users permanently deleted.');
    }

    public function recoverAll(): RedirectResponse
    {
        $users = User::onlyTrashed()->get();
        foreach ($users as $user) {
            $user->restore();
        }
        return redirect()->route('users.trash')->with('status', 'All trashed users restored.');
    }

    public function emptyOne($id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->route('users.trash')->with('status', 'User permanently deleted.');
    }
}
