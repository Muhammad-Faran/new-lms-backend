<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\MagicToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use Hash;




class AuthController extends BaseController
{
    protected $device;

   public function login(Request $request, $token = null)
    {
        $user = empty($token) ?
            $this->loginByCredentials($request, true) :
            $this->loginByToken($token);

        // Delete all existing tokens for the user
        $user->tokens()->delete();

        $this->saveDevice($user, $request);

        $tokenName = 'Token-' . Str::uuid() . '-' . time();
        $tokenExpiration = now()->addMinutes(config('sanctum.expiration')); // Use expiration from config
        $accessToken = $user->createToken($tokenName, ['*'], $tokenExpiration)->plainTextToken;

        Auth::attempt($request->only(['email', 'password']));

        return $this->loginResponse($user, $accessToken);
    }



     private function checkUserDevice($user)
    {
        $this->device = $user->devices()
            ->where('device_name', request()->header('User-Agent'))
            ->where('device_cookie', request()->cookie('deviceCookieUser_' . $user->id))
            ->where('device_expiry', '>=', now()->format('Y-m-d H:i:s'))
            ->first();

        return $this->device;
    }

    private function saveDevice($user, $request)
    {
        if ($request->filled('remember_device') && $request->remember_device) {
            $device = $this->checkUserDevice($user);
            if (empty($device)) {
                $this->device = $user->devices()->create([
                    'device_name' => $request->header('User-Agent'),
                    'device_ip_address' => $request->ip(),
                    'device_cookie' => Str::random(20),
                    'device_expiry' => now()->addDays(30)->format('Y-m-d H:i'),
                ]);
            }
        }
        return true;
    }


    private function loginByCredentials(Request $request, bool $once = false): Authenticatable
    {
        
        if ($once && Auth::once($request->only(['email', 'password']))) {
            return Auth::user();
        }

        if (Auth::attempt($request->only(['email', 'password']))) {
            return Auth::user();
        }

        abort($this->sendError('Unauthorized', ['error' => __("auth.login-attempt-failed")], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    private function loginByToken($token)
    {
        $token = $this->verifyToken($token, true);
        Auth::login($token->user);
        return $token->user;
    }

    public function verifyToken($token, $consume = false)
    {
        $token = MagicToken::whereToken(hash('sha256', $token))->first();

        if (request()->hasValidSignature(false) && $token->isValid() && !empty($token->user)) {
            if ($consume) {
                $token->consume();
            }
            if (request()->isMethod('GET')) {
                return $this->sendResponse('success', $token->user->only(['first_name', 'last_name', 'email']));
            }
            return $token;
        }

        abort($this->sendError('Unauthorized', ['error' => __("auth.error-invalid-token")]));
    }

    public function loginRequest(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::whereEmail($data['email'])->first();

        if ($user) {
            $user->generateMagicToken();
            return $this->sendResponse('success', __("auth.magiclink-sent"));
        } else {
            // Don't be explicit on the error, as we don't want to reveal too
            // much to non-authorized users
            abort($this->sendError('Unauthorized', ['error' => __("auth.magiclink-error")]));
        }
    }

    // Logout method
    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully']);
    }

    private function loginResponse($user, $access_token)
    {
        $permissionsArray = auth()->user()->getAllPermissionSlugs();
        return $this->sendResponse([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'personal_access_token' => $access_token,
            'permissions' => $permissionsArray,
        ], __("auth.login-succesfull"));
    }

    // Get authenticated user's information
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function changePassword(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'old_password' => 'required|string',  // Check for the old password
        'password' => 'required|string|min:8|confirmed',  // 'confirmed' checks for password_confirmation
    ]);

    // Get the currently authenticated user
    $user = Auth::user();

    // Verify the old password
    if (!Hash::check($request->old_password, $user->password)) {
        return response()->json([
            'message' => 'The current password is incorrect.',
        ], 403);  // Return forbidden if the old password doesn't match
    }

    // Update the user's password
    $user->password = Hash::make($request->password);
    $user->save();

    // Revoke all existing tokens after password change for security
    $user->tokens()->delete();

    return response()->json([
        'message' => 'Password changed successfully.',
    ], 200);
}


}
