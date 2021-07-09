<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        if ($validator->fails())
        {
            return response()->json(['error' => $validator->getMessageBag()->toArray()]);
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json(['status' => 'success', 'created_id' => $user->id]);
    }

    public function profile()
    {
        $user = User::find(Auth::id());

        return response()->json(['data' => $user]);

    }
}
