<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\Formation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormationController extends Controller
{
    /**
     * Afficher toutes les formations d'un CV.
     */
    public function index(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $formations = $cv->formations()
            ->orderByDesc('date_debut')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Formations récupérées avec succès.',
            'data' => $formations,
        ]);
    }

    /**
     * Ajouter une formation à un CV.
     */
    public function store(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $validated = $request->validate([
            'diplome' => ['required', 'string', 'max:255'],
            'etablissement' => ['required', 'string', 'max:255'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'description' => ['nullable', 'string'],
        ]);

        $formation = $cv->formations()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Formation ajoutée avec succès.',
            'data' => $formation,
        ], 201);
    }

    /**
     * Afficher une formation précise.
     */
    public function show(
        Cv $cv,
        Formation $formation
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeFormation($cv, $formation);

        return response()->json([
            'success' => true,
            'message' => 'Formation récupérée avec succès.',
            'data' => $formation,
        ]);
    }

    /**
     * Modifier une formation.
     */
    public function update(
        Request $request,
        Cv $cv,
        Formation $formation
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeFormation($cv, $formation);

        $validated = $request->validate([
            'diplome' => ['sometimes', 'required', 'string', 'max:255'],
            'etablissement' => ['sometimes', 'required', 'string', 'max:255'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'description' => ['nullable', 'string'],
        ]);

        $formation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Formation modifiée avec succès.',
            'data' => $formation->fresh(),
        ]);
    }

    /**
     * Supprimer une formation.
     */
    public function destroy(
        Cv $cv,
        Formation $formation
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeFormation($cv, $formation);

        $formation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Formation supprimée avec succès.',
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
     * Vérifier que la formation appartient bien au CV.
     */
    private function authorizeFormation(
        Cv $cv,
        Formation $formation
    ): void {
        abort_unless(
            $formation->cv_id === $cv->id,
            404,
            'Cette formation n\'appartient pas à ce CV.'
        );
    }
}