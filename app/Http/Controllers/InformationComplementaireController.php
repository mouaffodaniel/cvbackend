<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\InformationComplementaire;
use Illuminate\Support\Facades\Auth;
class InformationComplementaireController extends Controller
{
    /**
     * Vérifie que le CV appartient à l'utilisateur connecté.
     */
    private function getCv(Request $request, int $cvId): Cv
    {
        return $request->user()
            ->cvs()
            ->findOrFail($cvId);
    }

    /**
     * Afficher les informations complémentaires d'un CV.
     */
    public function index(Request $request, int $cv): JsonResponse
    {
        $cvModel = $this->getCv($request, $cv);

        return response()->json([
            'message' => 'Informations complémentaires récupérées.',
            'data' => $cvModel->informationsComplementaires()->get(),
        ]);
    }

    /**
     * Enregistrer une nouvelle information complémentaire.
     */
    public function store(Request $request, int $cv): JsonResponse
    {
        $cvModel = $this->getCv($request, $cv);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'intitule' => ['required', 'string', 'max:255'],
            'etablissement' => ['nullable', 'string', 'max:255'],
            'date_obtention' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $information = $cvModel->informationsComplementaires()
            ->create($validated);

        return response()->json([
            'message' => 'Information complémentaire ajoutée avec succès.',
            'data' => $information,
        ], 201);
    }

    /**
     * Afficher une information complémentaire.
     */
    public function show(
        Request $request,
        int $cv,
        int $informations_complementaire
    ): JsonResponse {
        $cvModel = $this->getCv($request, $cv);

        $information = $cvModel->informationsComplementaires()
            ->findOrFail($informations_complementaire);

        return response()->json([
            'data' => $information,
        ]);
    }

    /**
     * Modifier une information complémentaire.
     */
    public function update(
        Request $request,
        int $cv,
        int $informations_complementaire
    ): JsonResponse {
        $cvModel = $this->getCv($request, $cv);

        $information = $cvModel->informationsComplementaires()
            ->findOrFail($informations_complementaire);

        $validated = $request->validate([
            'type' => ['sometimes', 'required', 'string', 'max:100'],
            'intitule' => ['sometimes', 'required', 'string', 'max:255'],
            'etablissement' => ['sometimes', 'nullable', 'string', 'max:255'],
            'date_obtention' => ['sometimes', 'nullable', 'date'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $information->update($validated);

        return response()->json([
            'message' => 'Information complémentaire modifiée avec succès.',
            'data' => $information->fresh(),
        ]);
    }

    /**
     * Supprimer une information complémentaire.
     */
    public function destroy(
        Request $request,
        int $cv,
        int $informations_complementaire
    ): JsonResponse {
        $cvModel = $this->getCv($request, $cv);

        $information = $cvModel->informationsComplementaires()
            ->findOrFail($informations_complementaire);

        $information->delete();

        return response()->json([
            'message' => 'Information complémentaire supprimée avec succès.',
        ]);
    }
}