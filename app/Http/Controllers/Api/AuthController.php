<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{

    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function create(Request $request){
        try{
            $validate = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone_number' => 'required',
                'password' => 'required|min:8'
            ]);

            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'account_type' => $request->account_type
            ]);

            return response()->json([
                'status' => true,
                'message' => "User Created Successfully",
                'data' => $user->load('account_type'),
                'token' => $user->createToken('API TOKEN')->plainTextToken
            ]);
        }catch(Throwable $throw){
            return response()->json([
                'status' => false,
                'message' => $throw->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function login(Request $request)
    {

        try{

            $validate = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->with('account_type')->first();

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'data' => $user,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ]);

        }catch(Throwable $throw){
            return response()->json([
                'status' => false,
                'message' => $throw->getMessage()
            ]);
        }

    }
}
