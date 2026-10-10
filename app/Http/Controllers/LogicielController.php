<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\Logiciel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogicielController extends Controller
{
    /**
     * Afficher tous les logiciels d'un CV.
     */
    public function index(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $logiciels = $cv->logiciels()
            ->orderByDesc('niveau')
            ->orderBy('nom')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Logiciels récupérés avec succès.',
            'data' => $logiciels,
        ]);
    }

    /**
     * Ajouter un logiciel à un CV.
     */
    public function store(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'niveau' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $logiciel = $cv->logiciels()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Logiciel ajouté avec succès.',
            'data' => $logiciel,
        ], 201);
    }

    /**
     * Afficher un logiciel précis.
     */
    public function show(
        Cv $cv,
        Logiciel $logiciel
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeLogiciel($cv, $logiciel);

        return response()->json([
            'success' => true,
            'message' => 'Logiciel récupéré avec succès.',
            'data' => $logiciel,
        ]);
    }

    /**
     * Modifier un logiciel.
     */
    public function update(
        Request $request,
        Cv $cv,
        Logiciel $logiciel
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeLogiciel($cv, $logiciel);

        $validated = $request->validate([
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'niveau' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
        ]);

        $logiciel->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Logiciel modifié avec succès.',
            'data' => $logiciel->fresh(),
        ]);
    }

    /**
     * Supprimer un logiciel.
     */
    public function destroy(
        Cv $cv,
        Logiciel $logiciel
    ): JsonResponse {
        $this->authorizeCv($cv);
        $this->authorizeLogiciel($cv, $logiciel);

        $logiciel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logiciel supprimé avec succès.',
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
     * Vérifier que le logiciel appartient bien au CV.
     */
    private function authorizeLogiciel(
        Cv $cv,
        Logiciel $logiciel
    ): void {
        abort_unless(
            $logiciel->cv_id === $cv->id,
            404,
            'Ce logiciel n\'appartient pas à ce CV.'
        );
    }
}