<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ResponseService;
use App\Services\UserService;
use App\Services\ValidationService;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private UserService $userService;
    private Auth $auth;
    private Password $password;
    private ValidationService $validationService;
    private ResponseService $response;

    public function __construct(Auth $auth, ValidationService $validationService, UserService $userService, Password $password, ResponseService $response)
    {
        $this->userService = $userService;
        $this->auth = $auth;
        $this->password = $password;
        $this->validationService = $validationService;
        $this->response = $response;
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
            return $this->response->sendJson('User created');
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Login user with email and password
     * @param Request $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $this->validationService->validate($request->all(), User::$loginRules);

            if (Auth::attempt($request->only('email', 'password'))) {
                $apiToken = $request->user()->createToken('api_token')->plainTextToken;
                return $this->response->sendJson('User logged in', ['api_token' => $apiToken]);
            } else {
                throw(new AuthenticationException("Invalid credentials"));
            }

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Log the user out
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->response->sendJson('User logged out');
        } catch (\Exception $exception) {
           throw $exception;
        }
    }

    /**
     * Send the password reset link
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendPasswordResetLink(Request $request): JsonResponse
    {
        try {
            $this->validationService->validate($request->all(), User::$emailRules);
            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return $this->response->sendJson('Password reset link sent');
            } else {
                throw new \Exception("Error sending Password reset link");
            }

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Reset user password after verifying the token
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
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
                return $this->response->sendJson('Password updated');
            } elseif ($status === Password::INVALID_TOKEN){
                throw new InvalidParameterException("Invalid reset token");
            } else {
                throw new \Exception("Error resetting password");
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
