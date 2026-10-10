<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\Experience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExperienceController extends Controller
{
    /**
     * Afficher toutes les expériences d'un CV.
     */
    public function index(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $experiences = $cv->experiences()
            ->orderByDesc('date_debut')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Expériences récupérées avec succès.',
            'data' => $experiences,
        ]);
    }

    /**
     * Ajouter une expérience à un CV.
     */
    public function store(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $validated = $request->validate([
            'poste' => ['required', 'string', 'max:255'],
            'entreprise' => ['required', 'string', 'max:255'],
            'lieu' => ['nullable', 'string', 'max:255'],

            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],

            'poste_actuel' => ['boolean'],

            'description' => ['nullable', 'string'],
        ]);

        if (($validated['poste_actuel'] ?? false) === true) {
            $validated['date_fin'] = null;
        }

        $experience = $cv->experiences()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expérience ajoutée avec succès.',
            'data' => $experience,
        ], 201);
    }

    /**
     * Afficher une expérience précise.
     */
    public function show(Cv $cv, Experience $experience): JsonResponse
    {
        $this->authorizeCv($cv);
        $this->authorizeExperience($cv, $experience);

        return response()->json([
            'success' => true,
            'message' => 'Expérience récupérée avec succès.',
            'data' => $experience,
        ]);
    }

    /**
     * Modifier une expérience.
     */
    public function update(
        Request $request,
        Cv $cv,
        Experience $experience
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeExperience($cv, $experience);

        $validated = $request->validate([
            'poste' => ['sometimes', 'required', 'string', 'max:255'],
            'entreprise' => ['sometimes', 'required', 'string', 'max:255'],
            'lieu' => ['nullable', 'string', 'max:255'],

            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],

            'poste_actuel' => ['boolean'],

            'description' => ['nullable', 'string'],
        ]);

        if (($validated['poste_actuel'] ?? $experience->poste_actuel) === true) {
            $validated['date_fin'] = null;
        }

        $experience->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expérience modifiée avec succès.',
            'data' => $experience->fresh(),
        ]);
    }

    /**
     * Supprimer une expérience.
     */
    public function destroy(
        Cv $cv,
        Experience $experience
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeExperience($cv, $experience);

        $experience->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expérience supprimée avec succès.',
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
     * Vérifier que l'expérience appartient bien au CV.
     */
    private function authorizeExperience(
        Cv $cv,
        Experience $experience
    ): void {
        abort_unless(
            $experience->cv_id === $cv->id,
            404,
            'Cette expérience n\'appartient pas à ce CV.'
        );
    }
}