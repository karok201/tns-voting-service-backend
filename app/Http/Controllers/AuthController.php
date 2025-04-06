<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Валидация данных
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:3',
        ]);

        // Попытка аутентификации
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Генерация токена
        $token = $user->createToken('Laravel Gateway')->plainTextToken;

        // Возвращаем токен в ответе
        return response()->json(['token' => $token]);
    }
}
