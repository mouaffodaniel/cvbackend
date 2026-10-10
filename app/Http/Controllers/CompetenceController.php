<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use App\Models\Cv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetenceController extends Controller
{
    /**
     * Afficher toutes les compétences d'un CV.
     */
    public function index(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $competences = $cv->competences()
            ->orderByDesc('niveau')
            ->orderBy('nom')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Compétences récupérées avec succès.',
            'data' => $competences,
        ]);
    }

    /**
     * Ajouter une compétence à un CV.
     */
    public function store(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'niveau' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $competence = $cv->competences()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Compétence ajoutée avec succès.',
            'data' => $competence,
        ], 201);
    }

    /**
     * Afficher une compétence précise.
     */
    public function show(
        Cv $cv,
        Competence $competence
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeCompetence($cv, $competence);

        return response()->json([
            'success' => true,
            'message' => 'Compétence récupérée avec succès.',
            'data' => $competence,
        ]);
    }

    /**
     * Modifier une compétence.
     */
    public function update(
        Request $request,
        Cv $cv,
        Competence $competence
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeCompetence($cv, $competence);

        $validated = $request->validate([
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'niveau' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
        ]);

        $competence->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Compétence modifiée avec succès.',
            'data' => $competence->fresh(),
        ]);
    }

    /**
     * Supprimer une compétence.
     */
    public function destroy(
        Cv $cv,
        Competence $competence
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeCompetence($cv, $competence);

        $competence->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compétence supprimée avec succès.',
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
     * Vérifier que la compétence appartient bien au CV.
     */
    private function authorizeCompetence(
        Cv $cv,
        Competence $competence
    ): void {
        abort_unless(
            $competence->cv_id === $cv->id,
            404,
            'Cette compétence n\'appartient pas à ce CV.'
        );
    }
}