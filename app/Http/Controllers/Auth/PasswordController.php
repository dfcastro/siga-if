<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password; // Certifique-se de que esta linha está presente

class PasswordController extends Controller
{
    /**
     * Atualiza a senha do usuário.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed', // Garante que o campo 'password_confirmation' corresponde
                
                // --- REGRAS DE SEGURANÇA ADICIONADAS AQUI ---
                Password::min(8)       // Mínimo de 8 caracteres
                        ->mixedCase()  // Requer letras maiúsculas e minúsculas
                        ->numbers()    // Requer números
                        ->symbols(),   // Requer símbolos (ex: ! @ # $)
                
                Password::uncompromised(), // Verifica se a senha já foi vazada
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}