<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Joke;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Throwable;
use function PHPUnit\Framework\isEmpty;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return ApiResponse::success($roles, "Roles retrieved");
    }

//    public function search(Request $request): JsonResponse {}
//    IN USER CONTROLLER

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:4'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web'
        ]);
        return ApiResponse::success($role, 'Role created successfully', 201);
    }

    public function show(string $id)
    {
        $role = Role::findById($id);
        return ApiResponse::success($role, "Role retrieved");
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:4'],
        ]);

        $role = Role::findById($id);

        $role->update($validated);
        return ApiResponse::success($role, 'Role updated successfully', 201);
    }

    public function destroy(string $id)
    {
        if ($id == '1') {
            return ApiResponse::error([], "'super-user' role cannot be deleted");
        }
        $role = Role::findById($id);
        $role->delete();
        return ApiResponse::success($role, 'Role deleted successfully');
    }

    public function logoutRole(Request $request, string $id)
    {
        if ($id == '1') {
            return ApiResponse::error([], "'super-user' role cannot be logged out");
        }

        $user = $request->user();
        if ($user->role == 'admin' && $id == '2') {
            return ApiResponse::error([], "'admin' roles' cannot be logged out by an admin user");
        }

        $role = Role::findById($id);
        $roleName = $role->name;
        // get all users in role
        $users = $role->users;
        for ($i = 0; $i < count($users); $i++) {
            $user = $users[$i];
            $user->logout();
        }
        return ApiResponse::success([], "$roleName Users logged out successfully");
    }
}
