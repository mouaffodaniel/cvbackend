<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\SavoirFaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavoirFaireController extends Controller
{
    /**
     * Afficher tous les savoir-faire d'un CV.
     */
    public function index(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $savoirFaires = $cv->savoirFaires()
            ->orderByDesc('niveau')
            ->orderBy('nom')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Savoir-faire récupérés avec succès.',
            'data' => $savoirFaires,
        ]);
    }

    /**
     * Ajouter un savoir-faire à un CV.
     */
    public function store(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'niveau' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $savoirFaire = $cv->savoirFaires()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Savoir-faire ajouté avec succès.',
            'data' => $savoirFaire,
        ], 201);
    }

    /**
     * Afficher un savoir-faire précis.
     */
    public function show(
        Cv $cv,
        SavoirFaire $savoirFaire
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeSavoirFaire($cv, $savoirFaire);

        return response()->json([
            'success' => true,
            'message' => 'Savoir-faire récupéré avec succès.',
            'data' => $savoirFaire,
        ]);
    }

    /**
     * Modifier un savoir-faire.
     */
    public function update(
        Request $request,
        Cv $cv,
        SavoirFaire $savoirFaire
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeSavoirFaire($cv, $savoirFaire);

        $validated = $request->validate([
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'niveau' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
        ]);

        $savoirFaire->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Savoir-faire modifié avec succès.',
            'data' => $savoirFaire->fresh(),
        ]);
    }

    /**
     * Supprimer un savoir-faire.
     */
    public function destroy(
        Cv $cv,
        SavoirFaire $savoirFaire
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeSavoirFaire($cv, $savoirFaire);

        $savoirFaire->delete();

        return response()->json([
            'success' => true,
            'message' => 'Savoir-faire supprimé avec succès.',
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
     * Vérifier que le savoir-faire appartient bien au CV.
     */
    private function authorizeSavoirFaire(
        Cv $cv,
        SavoirFaire $savoirFaire
    ): void {
        abort_unless(
            $savoirFaire->cv_id === $cv->id,
            404,
            'Ce savoir-faire n\'appartient pas à ce CV.'
        );
    }
}