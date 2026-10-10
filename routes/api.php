<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\CompetenceController;
use App\Http\Controllers\SavoirFaireController;
use App\Http\Controllers\LogicielController;
use App\Http\Controllers\LangueController;
use App\Http\Controllers\InteretController;
use App\Http\Controllers\InformationComplementaireController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    // Inscription et connexion
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Mot de passe oublié
    Route::post(
        '/forgot-password',
        [PasswordResetController::class, 'sendResetLink']
    );

    // Réinitialisation du mot de passe
    Route::post(
        '/reset-password',
        [PasswordResetController::class, 'reset']
    );

    // Routes nécessitant une authentification
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

/*
|--------------------------------------------------------------------------
| Routes CV protégées
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('cvs', CvController::class);

    Route::apiResource(
        'cvs.experiences',
        ExperienceController::class
    );

    Route::apiResource(
        'cvs.formations',
        FormationController::class
    );

    Route::apiResource(
        'cvs.competences',
        CompetenceController::class
    );

    Route::apiResource(
        'cvs.savoir-faires',
        SavoirFaireController::class
    );

    Route::apiResource(
        'cvs.logiciels',
        LogicielController::class
    );

    Route::apiResource(
        'cvs.langues',
        LangueController::class
    );

    Route::apiResource(
        'cvs.interets',
        InteretController::class
    );

    Route::apiResource(
    'cvs.informations-complementaires',
    InformationComplementaireController::class
);
});