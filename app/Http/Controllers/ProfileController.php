<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Récupérer le profil de l'utilisateur connecté",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profil récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Jean Dupont"),
     *                 @OA\Property(property="email", type="string", example="jean@example.com"),
     *                 @OA\Property(property="address", type="string", example="123 Rue de Paris"),
     *                 @OA\Property(property="account_type", type="string", example="pro"),
     *                 @OA\Property(property="companies", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="posts", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['companies', 'posts.company']);

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/profile",
     *     summary="Mettre à jour le profil de l'utilisateur",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","address"},
     *             @OA\Property(property="name", type="string", example="Jean Dupont"),
     *             @OA\Property(property="email", type="string", format="email", example="jean@example.com"),
     *             @OA\Property(property="address", type="string", example="123 Rue de Paris, 75001")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profil mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'address' => ['required', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $user->update($validated);
        $user->load(['companies', 'posts.company']);

        return response()->json([
            'user' => $user,
        ]);
    }
}
