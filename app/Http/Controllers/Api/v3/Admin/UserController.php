<?php

namespace App\Http\Controllers\Api\v3\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Number;


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
}
