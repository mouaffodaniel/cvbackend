<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\Langue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LangueController extends Controller
{
    /**
     * Afficher toutes les langues d'un CV.
     */
    public function index(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $langues = $cv->langues()
            ->orderBy('nom')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Langues récupérées avec succès.',
            'data' => $langues,
        ]);
    }

    /**
     * Ajouter une langue à un CV.
     */
    public function store(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'niveau' => ['required', 'string', 'max:100'],
        ]);

        $langue = $cv->langues()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Langue ajoutée avec succès.',
            'data' => $langue,
        ], 201);
    }

    /**
     * Afficher une langue précise.
     */
    public function show(
        Cv $cv,
        Langue $langue
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeLangue($cv, $langue);

        return response()->json([
            'success' => true,
            'message' => 'Langue récupérée avec succès.',
            'data' => $langue,
        ]);
    }

    /**
     * Modifier une langue.
     */
    public function update(
        Request $request,
        Cv $cv,
        Langue $langue
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeLangue($cv, $langue);

        $validated = $request->validate([
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'niveau' => ['sometimes', 'required', 'string', 'max:100'],
        ]);

        $langue->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Langue modifiée avec succès.',
            'data' => $langue->fresh(),
        ]);
    }

    /**
     * Supprimer une langue.
     */
    public function destroy(
        Cv $cv,
        Langue $langue
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeLangue($cv, $langue);

        $langue->delete();

        return response()->json([
            'success' => true,
            'message' => 'Langue supprimée avec succès.',
        ]);
    }

    /**
     * Vérifier que le CV appartient à l'utilisateur connecté.
     */
    private function authorizeCv(Cv $cv): void
    {
        abort_unless(
            $cv->user_id === Auth::id(),
            403,
            'Vous n\'êtes pas autorisé à accéder à ce CV.'
        );
    }

    /**
     * Vérifier que la langue appartient bien au CV.
     */
    private function authorizeLangue(
        Cv $cv,
        Langue $langue
    ): void {
        abort_unless(
            $langue->cv_id === $cv->id,
            404,
            'Cette langue n\'appartient pas à ce CV.'
        );
    }
}