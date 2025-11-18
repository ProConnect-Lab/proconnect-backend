<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     summary="Connexion administrateur",
     *     tags={"Admin - Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@proconnect.test"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion admin réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="3|admintoken123"),
     *             @OA\Property(property="admin", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Identifiants invalides ou accès refusé")
     * )
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $admin = User::where('email', $request->input('email'))->first();

        if (! $admin || ! $admin->isAdmin() || ! Hash::check($request->input('password'), $admin->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $token = $admin->createToken('admin', ['admin'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => $admin,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/logout",
     *     summary="Déconnexion administrateur",
     *     tags={"Admin - Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Session administrateur fermée.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Session administrateur fermée.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/me",
     *     summary="Profil de l'admin connecté",
     *     tags={"Admin - Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profil récupéré",
     *         @OA\JsonContent(
     *             @OA\Property(property="admin", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'admin' => $request->user(),
        ]);
    }
}
