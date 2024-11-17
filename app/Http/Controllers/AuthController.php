<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Services\ValidationService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use mysql_xdevapi\Exception;

class AuthController extends Controller
{
    private UserService $userService;
    private Auth $auth;
    private Password $password;
    private ValidationService $validationService;

    public function __construct(Auth $auth, ValidationService $validationService, UserService $userService, Password $password)
    {
        $this->userService = $userService;
        $this->auth = $auth;
        $this->password = $password;
        $this->validationService = $validationService;
    }

    /**
     * Register a new user
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $this->validationService->validate($request->all(), User::$createRules);

            $user = $this->userService->create($request->name, $request->email, $request->password);
            return response()->json([
                'message' => 'User created',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Login user with email and password
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $this->validationService->validate($request->all(), User::$loginRules);

            if (Auth::attempt($request->only('email', 'password'))) {
                $apiToken = $request->user()->createToken('api_token')->plainTextToken;
                return response()->json([
                    'message' => 'User logged in',
                    'api_token' => $apiToken,
                ]);
            } else {
                throw(new AuthenticationException("Invalid credentials"));
            }
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $exception) {
            // TODO: Handle exceptions in a middleware and send appropriate responses
            return response()->json([$exception->getMessage()], 500);
        }
    }

    /**
     * Log the user out
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'User logged out'
            ]);
        } catch (\Exception $exception) {
            // TODO: Handle exceptions in a middleware and send appropriate responses
            return response()->json([$exception->getMessage()], 500);
        }
    }

    /**
     * Send the password reset link
     * @param Request $request
     * @return JsonResponse
     */
    public function sendPasswordResetLink(Request $request): JsonResponse
    {
        try {
            $this->validationService->validate($request->all(), User::$emailRules);
            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => "Password reset link sent"
                ]);
            } else {
                throw new \Exception("Error sending Password reset link");
            }

        } catch (\Exception $exception) {
            // TODO: Handle exceptions in a middleware and send appropriate responses
            return response()->json([$exception->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $this->validationService->validate($request->all(), User::$passwordResetRules);

            $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ]);
                    $user->save();
                });
            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'message' => "Password updated"
                ]);
            } elseif ($status === Password::INVALID_TOKEN){
                throw new \Exception("Invalid reset token");
            } else {
                throw new \Exception("Error resetting password");
            }
        } catch (\Exception $exception) {
            // TODO: Handle exceptions in a middleware and send appropriate responses
            return response()->json([$exception->getMessage()], 500);
        }
    }
}
