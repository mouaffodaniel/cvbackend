<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\Interet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteretController extends Controller
{
    /**
     * Afficher tous les centres d'intérêt d'un CV.
     */
    public function index(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $interets = $cv->interets()
            ->orderBy('nom')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Centres d\'intérêt récupérés avec succès.',
            'data' => $interets,
        ]);
    }

    /**
     * Ajouter un centre d'intérêt à un CV.
     */
    public function store(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
        ]);

        $interet = $cv->interets()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Centre d\'intérêt ajouté avec succès.',
            'data' => $interet,
        ], 201);
    }

    /**
     * Afficher un centre d'intérêt précis.
     */
    public function show(
        Cv $cv,
        Interet $interet
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeInteret($cv, $interet);

        return response()->json([
            'success' => true,
            'message' => 'Centre d\'intérêt récupéré avec succès.',
            'data' => $interet,
        ]);
    }

    /**
     * Modifier un centre d'intérêt.
     */
    public function update(
        Request $request,
        Cv $cv,
        Interet $interet
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeInteret($cv, $interet);

        $validated = $request->validate([
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $interet->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Centre d\'intérêt modifié avec succès.',
            'data' => $interet->fresh(),
        ]);
    }

    /**
     * Supprimer un centre d'intérêt.
     */
    public function destroy(
        Cv $cv,
        Interet $interet
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeInteret($cv, $interet);

        $interet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Centre d\'intérêt supprimé avec succès.',
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
     * Vérifier que le centre d'intérêt appartient bien au CV.
     */
    private function authorizeInteret(
        Cv $cv,
        Interet $interet
    ): void {
        abort_unless(
            $interet->cv_id === $cv->id,
            404,
            'Ce centre d\'intérêt n\'appartient pas à ce CV.'
        );
    }
}