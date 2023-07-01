<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Notifications\RegistrationEmail;
use App\Notifications\ResetPasswordEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Register
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string'],                
                'email' => ['required', 'email','string', 'max:255', 'unique:users'],               
                'password' => ['required', 'string', Password::defaults()],
                'confirmPassword' => ['required', 'string'],
                'username' => ['required', 'string', 'max:25', 'unique:users', 'min:6'],                
                'phone' => ['required', 'string', 'max:15'],                
                'university' => ['required', 'string'],                
                'major' => ['required', 'string'],                
                'year' => ['required', 'string'],                
            ]);

            if ($request->password != $request->confirmPassword) {
                return ResponseFormatter::error([
                    'message' => 'Something when wrong',
                    'error' => "Password not match",    
                ], 
                    'Register Failed', 
                    500,
                );
            }

            $getKodePhone = substr($request->phone,0,1);
            if ($getKodePhone != '+') {
                return ResponseFormatter::error([
                    'message' => 'Something when wrong',
                    'error' => "Invalid Phone Number, Use Country Code",    
                ], 
                    'Register Failed', 
                    500,
                );
            }

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'username' => $request->username,
                'phone' => $request->phone,
                'university' => $request->university,
                'major' => $request->major,
                'year' => $request->year,
            ]);

            $user = User::where('email', $request->email)->first();
            $user->notify(new RegistrationEmail($user->name));

            $tokenResult = $user->createToken('authToken')->plainTextToken;


            return ResponseFormatter::success(
                [
                    'acess_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user
                ],
                'User Registered'
            );

        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Register Failed', 
                500,
            );
        }
    }

    // Login
    public function login(Request $request) 
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            // Cek apakah ada email dan password yang sesuai
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized',
                    'error' => 'Password incorrect'
                ],
                    'Authentication Failed',
                    500
                );
            }

            $user = User::where('email', $request->email)->first();

            // cek ulang apakah password sesuai (opsional)
            if(!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'acess_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Login Failed', 
                500,
            );
        }
    }

    // Get data user nanti dari token
    public function get(Request $request)
    {
        $user = $request->user();
        return ResponseFormatter::success($user, 'Get user data success');
    }

    // Logout
    public function logout(Request $request)
    {
        try {
            $token = $request->user()->currentAccessToken()->delete();
            return ResponseFormatter::success($token, 'Token Revoked');

        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Logout Failed', 
                500,
            );
        }
    }

    // edit profile
    public function editProfile(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string'],                
                'phone' => ['required', 'string', 'max:15'],                
                'university' => ['required', 'string'],                
                'major' => ['required', 'string'],                
                'year' => ['required', 'string'],    
            ]);

            $getKodePhone = substr($request->phone,0,1);
            if ($getKodePhone != '+') {
                return ResponseFormatter::error([
                    'message' => 'Something when wrong',
                    'error' => "Invalid Phone Number, Use Country Code",    
                ], 
                    'Edit Profile Failed', 
                    500,
                );
            }

            $id = Auth::id();

            // cari di database sesuai id
            $user = User::find($id);

            // update profile
            $user->update([
                'name' => $request->name,         
                'phone' => $request->phone,               
                'university' => $request->university,               
                'major' => $request->major,               
                'year' => $request->year,
            ]);

            return ResponseFormatter::success(
                $user->get(), 
                'Profile Updated'
            );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit Profile Failed', 
                500,
            );
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            // Validate and Check
            $request->validate([
                'email' => 'email|required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->email) {
                return ResponseFormatter::error([
                    'error' => 'Incorrect email address provided',    
                ], 
                    'No Record Found', 
                    404,
                );
            }

            // Generate Token
            $resetToken = str_pad(random_int(1,9999), 16, '0', STR_PAD_LEFT);

            if(!$userPassReset = PasswordResetToken::where('email', $request->email)->first()) {
                PasswordResetToken::create([
                    'email' => $user->email,
                    'token' => $resetToken,
                ]);
            } else {
                $userPassReset->update([
                    'email' => $user->email,
                    'token' => $resetToken,
                ]);
            }

            $user->notify(new ResetPasswordEmail($resetToken));

            return ResponseFormatter::success(["token" => $resetToken], 'Forgot success, check your email');
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Forgot Password Failed', 
                500,
            );
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            // Validate and Check
            $request->validate([
                'password' => ['required', 'string', Password::defaults()],
                'confirmPassword' => ['required', 'string'],
                'token' => ['required', 'integer'],
            ]);

            if ($request->password != $request->confirmPassword) {
                return ResponseFormatter::error([
                    'message' => 'Something when wrong',
                    'error' => "Password not match",    
                ], 
                    'Reset Password Failed', 
                    500,
                );
            }

            $email = PasswordResetToken::where('token', $request->token)->first()->email;
            $user = User::where('email', $email)->first();

            if (!$user || !$user->email) {
                return ResponseFormatter::error([
                    'error' => 'Incorrect email address provided',    
                ], 
                    'No Record Found', 
                    404,
                );
            }

            $resetRequest = PasswordResetToken::where('email', $user->email)->first();

            if (!$resetRequest || ($resetRequest->token != $request->token)) {
                return ResponseFormatter::error([
                    'error' => 'Token mismatch',    
                ], 
                    'Check token again', 
                    404,
                );
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // delete all previous token
            $user->tokens()->delete();

            return ResponseFormatter::success(null, 'Password Changed');
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Reset Password Failed', 
                500,
            );
        }
    }

    public function changePassword(Request $request)
    {
        try {
            // Validate and Check
            $request->validate([
                'password' => ['required', 'string', Password::defaults()],
                'confirmPassword' => ['required', 'string'],
            ]);

            if ($request->password != $request->confirmPassword) {
                return ResponseFormatter::error([
                    'message' => 'Something when wrong',
                    'error' => "Password not match",    
                ], 
                    'Change Password Failed', 
                    500,
                );
            }

            $user = User::where('id', Auth::id())->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'error' => 'User not found!',    
                ], 
                    'No Record Found', 
                    404,
                );
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return ResponseFormatter::success(null, 'Password Changed');
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Reset Password Failed!', 
                500,
            );
        }
    }
}
