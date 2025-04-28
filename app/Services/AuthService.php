<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $tokenService;
    protected $mailService;

    public function __construct(TokenService $tokenService, MailService $mailService)
    {
        $this->tokenService = $tokenService;
        $this->mailService = $mailService;
    }

    /**
     * Register a new user
     * @param array $data User data (name, email, password)
     * @return User The created user
     */
    public function registerUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $this->mailService->sendWelcomeEmail($user);

        return $user;
    }

    /**
     * Authenticate a user
     * @param array $credentials Login credentials (email, password)
     * @return array Contains user and token
     * @throws \Exception If credentials are invalid
     */
    public function authenticateUser(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        return [
            'user' => $user,
            'token' => $this->tokenService->generateToken($user)
        ];
    }
}