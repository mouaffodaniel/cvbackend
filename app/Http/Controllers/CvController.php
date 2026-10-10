<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CvController extends Controller
{
    /**
     * Afficher tous les CV de l'utilisateur connecté.
     */
    public function index(): JsonResponse
    {
        $cvs = Cv::where('user_id', Auth::id())
            ->with([
                'experiences',
                'formations',
                'competences',
                'savoirFaires',
                'logiciels',
                'langues',
                'interets',
            ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des CV récupérée avec succès.',
            'data' => $cvs,
        ]);
    }

    /**
     * Créer un nouveau CV.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template' => [
    'sometimes',
    'required',
    'string',
    'in:classic,minimal,modern,student,elegant,coporate'],
            'titre_poste' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],

            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],

            'telephone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string'],

            'linkedin' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'portfolio' => ['nullable', 'url', 'max:255'],

            'profil_professionnel' => ['nullable', 'string'],

            'template' => ['sometimes', 'required', 'string', 'in:classic,minimal,modern,student,elegant,coporate'],
    ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request
                ->file('photo')
                ->store('cvs/photos', 'public');
        }

        $validated['user_id'] = Auth::id();

        $cv = Cv::create($validated);

        $cv->load([
            'experiences',
            'formations',
            'competences',
            'savoirFaires',
            'logiciels',
            'langues',
            'interets',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CV créé avec succès.',
            'data' => $cv,
        ], 201);
    }

    /**
     * Afficher un CV précis.
     */
    public function show(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $cv->load([
            'user',
            'experiences',
            'formations',
            'competences',
            'savoirFaires',
            'logiciels',
            'langues',
            'interets',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CV récupéré avec succès.',
            'data' => $cv,
        ]);
    }

    /**
     * Modifier un CV.
     */
    public function update(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        $validated = $request->validate([
            'template' => [
    'sometimes',
    'required',
    'string',
    'in:classic,minimal,modern,student,elegant,coporate',
],
            'titre_poste' => ['sometimes', 'required', 'string', 'max:255'],
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'prenom' => ['sometimes', 'required', 'string', 'max:255'],

            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],

            'telephone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string'],

            'linkedin' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'portfolio' => ['nullable', 'url', 'max:255'],

            'profil_professionnel' => ['nullable', 'string'],

            'template' => ['sometimes', 'required', 'string', 'in:classic,minimal,modern,student,elegant,coporate'],
        ]);

        if ($request->hasFile('photo')) {
            if ($cv->photo) {
                Storage::disk('public')->delete($cv->photo);
            }

            $validated['photo'] = $request
                ->file('photo')
                ->store('cvs/photos', 'public');
        }

        $cv->update($validated);

        $cv->load([
            'experiences',
            'formations',
            'competences',
            'savoirFaires',
            'logiciels',
            'langues',
            'interets',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CV modifié avec succès.',
            'data' => $cv,
        ]);
    }

    /**
     * Supprimer un CV.
     */
    public function destroy(Cv $cv): JsonResponse
    {
        $this->authorizeCv($cv);

        if ($cv->photo) {
            Storage::disk('public')->delete($cv->photo);
        }

        $cv->delete();

        return response()->json([
            'success' => true,
            'message' => 'CV supprimé avec succès.',
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
}