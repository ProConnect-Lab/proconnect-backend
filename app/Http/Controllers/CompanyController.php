<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/companies",
     *     summary="Liste des entreprises de l'utilisateur",
     *     tags={"Companies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des entreprises récupérée",
     *         @OA\JsonContent(
     *             @OA\Property(property="companies", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ma Société"),
     *                 @OA\Property(property="cfe_number", type="string", example="123456789"),
     *                 @OA\Property(property="address", type="string", example="456 Avenue des Entreprises"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $companies = $request->user()->companies()->latest()->get();

        return response()->json(['companies' => $companies]);
    }

    /**
     * @OA\Post(
     *     path="/api/companies",
     *     summary="Créer une nouvelle entreprise",
     *     tags={"Companies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","cfe_number","address"},
     *             @OA\Property(property="name", type="string", example="Ma Société SARL"),
     *             @OA\Property(property="cfe_number", type="string", example="123456789"),
     *             @OA\Property(property="address", type="string", example="456 Avenue des Entreprises, 75008 Paris")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Entreprise créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="company", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function store(CompanyRequest $request): JsonResponse
    {
        $company = $request->user()->companies()->create($request->validated());

        return response()->json(['company' => $company], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/companies/{company}",
     *     summary="Modifier une entreprise",
     *     tags={"Companies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         required=true,
     *         description="ID de l'entreprise",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","cfe_number","address"},
     *             @OA\Property(property="name", type="string", example="Ma Société SARL"),
     *             @OA\Property(property="cfe_number", type="string", example="123456789"),
     *             @OA\Property(property="address", type="string", example="456 Avenue Modifiée")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entreprise modifiée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="company", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(CompanyRequest $request, Company $company): JsonResponse
    {
        $this->ensureOwner($request->user(), $company);

        $company->update($request->validated());

        return response()->json(['company' => $company]);
    }

    /**
     * @OA\Delete(
     *     path="/api/companies/{company}",
     *     summary="Supprimer une entreprise",
     *     tags={"Companies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         required=true,
     *         description="ID de l'entreprise",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entreprise supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Entreprise supprimée avec succès.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function destroy(Request $request, Company $company): JsonResponse
    {
        $this->ensureOwner($request->user(), $company);

        $company->delete();

        return response()->json(['message' => 'Entreprise supprimée avec succès.']);
    }

    protected function ensureOwner($user, Company $company): void
    {
        abort_if($company->user_id !== $user->id, 403, 'Accès refusé.');
    }
}
