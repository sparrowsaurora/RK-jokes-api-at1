<?php

namespace App\Http\Controllers\Api\v3\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Number;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index(): JsonResponse
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

    public function users(): JsonResponse
    {
        $users = User::paginate(10);
        //$users = User::all();

        return ApiResponse::success(
            ['users' => $users],
            'Users retrieved successfully'
        );
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
