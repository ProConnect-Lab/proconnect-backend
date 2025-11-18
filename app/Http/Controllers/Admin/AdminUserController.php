<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/admins",
     *     summary="Liste des administrateurs",
     *     tags={"Admin - Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des administrateurs",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé - Admin uniquement")
     * )
     */
    public function index(): JsonResponse
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->latest()
            ->get(['id', 'name', 'email', 'address', 'account_type', 'created_at']);

        return response()->json([
            'data' => $admins,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/admins",
     *     summary="Créer un nouvel administrateur",
     *     tags={"Admin - Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation","address"},
     *             @OA\Property(property="name", type="string", example="Admin Nouveau"),
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=8),
     *             @OA\Property(property="address", type="string", example="123 Rue Admin"),
     *             @OA\Property(property="account_type", type="string", enum={"private", "pro"}, example="pro")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Administrateur créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="admin", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé - Admin uniquement"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function store(StoreAdminRequest $request): JsonResponse
    {
        $admin = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'account_type' => $request->input('account_type', 'pro'),
            'role' => 'admin',
            'password' => $request->input('password'),
        ]);

        return response()->json([
            'message' => 'Administrateur créé avec succès.',
            'admin' => $admin,
        ], 201);
    }
}
