<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Envoyer un lien de réinitialisation par e-mail.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Si cette adresse correspond à un compte, un lien de réinitialisation sera envoyé.',
            ]);
        }

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Une demande récente existe déjà. Veuillez patienter avant de réessayer.',
            ], 429);
        }

        /*
         * On ne révèle pas si l'adresse e-mail existe.
         */
        return response()->json([
            'message' => 'Si cette adresse correspond à un compte, un lien de réinitialisation sera envoyé.',
        ]);
    }

    /**
     * Réinitialiser le mot de passe à l'aide du token reçu.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Votre mot de passe a été réinitialisé avec succès.',
            ]);
        }

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Trop de tentatives. Veuillez patienter avant de réessayer.',
            ], 429);
        }

        throw ValidationException::withMessages([
            'email' => [
                'Le lien est invalide ou expiré. Veuillez demander un nouveau lien.',
            ],
        ]);
    }
}