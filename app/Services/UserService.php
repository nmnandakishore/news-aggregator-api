<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Create a new user
     * @param $name
     * @param $email
     * @param $password
     * @return User
     * @throws \Exception
     */
    public function create($name, $email, $password): User
    {
        try {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();

        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
        return $user;
    }

    /**
     * Retrieve user by email ID
     * @param $email
     * @return User|null
     * @throws \Exception
     */
    public function getUser($email): ?User
    {
        try {
            $user = $this->user->where('email', $email)->first();
        }
        catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
        return $user;
    }
}
