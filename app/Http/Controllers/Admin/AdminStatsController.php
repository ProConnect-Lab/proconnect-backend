<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminStatsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/stats",
     *     summary="Statistiques globales de la plateforme",
     *     tags={"Admin - Stats"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées",
     *         @OA\JsonContent(
     *             @OA\Property(property="totals", type="object",
     *                 @OA\Property(property="users", type="integer", example=150),
     *                 @OA\Property(property="companies", type="integer", example=45),
     *                 @OA\Property(property="posts", type="integer", example=230),
     *                 @OA\Property(property="admins", type="integer", example=3)
     *             ),
     *             @OA\Property(property="latest_users", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="latest_posts", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé - Admin uniquement")
     * )
     */
    public function __invoke(): JsonResponse
    {
        $totals = [
            'users' => User::where('role', '!=', 'admin')->count(),
            'companies' => Company::count(),
            'posts' => Post::count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        $latestUsers = User::where('role', '!=', 'admin')
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'account_type', 'created_at']);

        $latestPosts = Post::with(['user:id,name', 'company:id,name'])
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'user_id', 'company_id', 'created_at']);

        return response()->json([
            'totals' => $totals,
            'latest_users' => $latestUsers,
            'latest_posts' => $latestPosts,
        ]);
    }
}
