<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function fetchUsers(Request $request)
    {
        $client = $request->header('X-API-CLIENT-APP-IDENTIFIER');
        $users = User::whereClient($client)->get();
        return response()->json(UserResource::collection($users), 200);
    }
}
