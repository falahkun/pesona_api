<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class AuthController extends Controller
{
    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users'],
                'phone_number' => 'nullable',
                'password' => ['required', Password::min(6)->uncompromised()],
                'role' => ['nullable'],
            ]);

            $user = new User([
                'id' => Uuid::uuid6()->toString(),
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => $request->role == null ? 'user' : strtolower($request->role),
            ]);

            $user->save();

            return ResponseFormatter::success($user, 'Akunmu berhasil dibuat, tunggu konfirmasi dari kita ya!');
        } catch (ValidationException $err) {
                        return ResponseFormatter::error(array(
                'message' => 'something went wrong',
                'error' => $err->errors()
            ), 'Authentication failed');

        } catch (Exception $err) {
                        return ResponseFormatter::error(array(
                'message' => 'something went wrong',
                'error' => $err->getMessage()
            ), 'Authentication failed');

        }
    }

    public function update(Request $request)
    {
        try {
            $user = User::findOrFail($request->user()->id);

            $user->update($request->all());

            return ResponseFormatter::success($user);
        } catch (Exception $err) {
            return ResponseFormatter::error($err);
        }
    }

    public function activate(Request $request)
    {
        try {
            $admin = $request->user();

            if ($admin->role != 'admin') {
                throw new \Exception('oops, kamu ngga boleh akses menu ini ya!');
            }

            $request->validate([
                'user_id' => ['required'],
            ]);

            $user = User::findOrFail($request->user_id);
            // $user->active = true;

            $user->update([
                'active' => true,
            ]);

            return ResponseFormatter::success($user);
        } catch (Exception $err) {

            return ResponseFormatter::error($err->getMessage());
        }
    }

    public function signin(Request $request)
    {
        try {

            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'min:6'],
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw new \Exception('Sorry, ' . $request->email . ' is not recognized as an active username or email address');
                // return ResponseFormatter::error(null, 'Sorry, ' . $request->email . ' is not recognized as an active username or email address');
            }

            if (!password_verify($request->password, $user->password)) {
                throw new \Exception('You have entered an invalid email or password');
                // return ResponseFormatter::error(null, 'You have entered an invalid email or password');
            }
            
            if ($user->active == FALSE) {
                throw new \Exception('Maaf Akunmu belum dikonfirmasi, silahkan tunggu konfirmasi dari kami');
            }

            $getToken = $user->createToken('Personal Access Token');
            $getToken->token->save();

            return ResponseFormatter::success(array(
                'access_token' => $getToken->accessToken,
                'token_type' => 'Bearer',
                'user' => $user
            ));
        } catch (ValidationException $err) {
            return ResponseFormatter::error(array(
                'message' => 'something went wrong',
                'error' => $err->errors()
            ), 'Authentication failed');

        } catch (Exception $err) {
            return ResponseFormatter::error(array(
                'message' => 'something went wrong',
                'error' => $err->getMessage()
            ), 'Authentication failed');
        }
    }

    public function myaccount(Request $request) {
        return ResponseFormatter::success($request->user());
    }
    
    public function getAllAccount(Request $request)
    {
        try {
            
            $admin = $request->user();

        if ($admin->role != 'admin') {
            throw new \Exception('oops, kamu ngga boleh akses menu ini ya!');
        }

        return ResponseFormatter::success(User::all());
            
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }
}
