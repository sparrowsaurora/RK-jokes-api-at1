<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Number;

// BREAD Routes

class UserController extends Controller
{
    public function adminDashboard()
    {
        $userCount = Number::format(User::count());
        $userSuspendedCount = Number::format(User::where('status', 'suspended')->count());

        return ApiResponse::success(
            [
                'userCount' => $userCount,
                'userSuspendedCount' => $userSuspendedCount
            ],
            'Dashboard data retrieved successfully'
        );
    }

    public function index(): JsonResponse
    {
        $users = User::with('roles')->get();

        // Convert users to array and replace 'roles' with just role names
        $usersArray = $users->map(function ($user) {
            $userArray = $user->toArray();
            $userArray['roles'] = $user->roles->pluck('name')->toArray();
            return $userArray;
        });

        $userCount = $usersArray->count();

        return ApiResponse::success(
            ['userCount' => $userCount, 'users' => $usersArray, ],
            'Users retrieved successfully'
        );
    }

// NOT NEEDED. users will add their own accounts
//    public function store(Request $request): JsonResponse
//    {
//
//    }

    public function show(string $id): JsonResponse
    {
        $user = User::with('roles')->find($id);

        if (!$user) {
            return ApiResponse::error([], "User not found");
        }

        $userArray = $user->toArray();
        $userArray['roles'] = $user->roles->pluck('name')->toArray();

        return ApiResponse::success(['User' => $userArray], 'User retrieved successfully');
    }


    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return ApiResponse::error([], "User not found");
        }
//        $validated = $request->validate([
//            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
//            'name' => ['sometimes', 'string', 'max:255'],
//            'password' => ['sometimes', 'string', 'min:6', 'confirmed'], // must include password_confirmation field
//            'current_password' => ['required_with:password', 'string'], // only required if password change attempted
//        ]);
        $validated = $request->all();


        if (isset($validated['password'])) {
            if (!isset($validated['current_password']) || !Hash::check($validated['current_password'], $user->password)) {
                return ApiResponse::error([], 'Current password is incorrect');
            }

            $validated['password'] = Hash::make($validated['password']);
            unset($validated['current_password']);
        }

        $user->fill($validated);

        if (!$user->isDirty()) {
            return ApiResponse::error([], "No changes detected");
        }

        $user->save();
        return ApiResponse::success(['User' => $user], 'User updated successfully');

    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return ApiResponse::error([], "User not found", 404);
        }

        $user->delete();

        return ApiResponse::success([], "User <$id> moved deleted successfully");
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:32'],
        ]);
        $search = $validated['search'];

        if (empty($search) || $search == '') {
            return ApiResponse::error([], "search parameter is empty");
        };

        $users = User::where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        })
            ->orderBy('title')
            ->withCount('jokes')
            ->get();

        if ($users->isEmpty()) {
            return ApiResponse::error([], "No results found");
        }

        return ApiResponse::success(['Users' => $users, 'resultsCount' => $users->count()], "Users retrieved");
    }

    public function suspendUser(string $id)
    {
        $user = User::find($id);
        $user->suspendUser();
        return ApiResponse::success([$user->status], "suspended user <{$user->id}> successfully");
    }

    public function unsuspendUser(string $id)
    {
        $user = User::find($id);
        $user->unsuspendUser();
        return ApiResponse::success([], "unsuspended user <{$user->id}> successfully");
    }
}
