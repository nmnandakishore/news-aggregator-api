<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ResponseService;
use App\Services\UserService;
use App\Services\ValidationService;
use Illuminate\Http\Response as HttpResponse;
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
    private ResponseService $responseService;

    public function __construct(Auth $auth, ValidationService $validationService, UserService $userService, Password $password, ResponseService $responseService)
    {
        $this->userService = $userService;
        $this->auth = $auth;
        $this->password = $password;
        $this->validationService = $validationService;
        $this->responseService = $responseService;
    }

    /**
     * User Registration
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $this->validationService->validate($request->all(), User::$createRules);
            $user = $this->userService->create($request->name, $request->email, $request->password);
            return response()->json($this->responseService->buildResponse('User registered', null, false), HttpResponse::HTTP_CREATED);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * User Login
     * @param Request $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        try {
            //For Open API
            /** @var "UserName" $username
             * @example "user@example.com"
             * */
            $username = $request->get('email');
            /** @var Password $password */
            $password = $request->get('password');

            $this->validationService->validate($request->all(), User::$loginRules);

            if (Auth::attempt($request->only('email', 'password'))) {
                $apiToken = $request->user()->createToken('api_token')->plainTextToken;
                return response()->json($this->responseService->buildResponse('User logged in', ['api_token' => $apiToken]));
            } else {
                throw(new AuthenticationException("Invalid credentials"));
            }

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * User Logout
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            //For Open API

            $request->user()->currentAccessToken()->delete();
            return response()->json($this->responseService->buildResponse('User logged out'));
        } catch (\Exception $exception) {
           throw $exception;
        }
    }

    /**
     * Forgot Password
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
                return $this->responseService->buildResponse('Password reset link sent');
            } else {
                throw new \Exception("Error sending Password reset link");
            }

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Reset Password
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
                return response()->json($this->responseService->buildResponse('Password updated'));
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
