<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/users",
     *     summary="Recherche d'utilisateurs (admin)",
     *     tags={"Admin - Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche par nom, email ou adresse",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste paginée des utilisateurs",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé - Admin uniquement")
     * )
     */
    public function users(Request $request): JsonResponse
    {
        $paginator = User::query()
            ->where('role', '!=', 'admin')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = mb_strtolower($request->string('search')->value());
                $query->where(function ($inner) use ($term) {
                    $inner
                        ->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(address) LIKE ?', ["%{$term}%"]);
                });
            })
            ->withCount(['companies', 'posts'])
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10));

        return $this->paginatedResponse($paginator, fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'account_type' => $user->account_type,
            'address' => $user->address,
            'companies_count' => $user->companies_count,
            'posts_count' => $user->posts_count,
            'created_at' => $user->created_at,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/companies",
     *     summary="Recherche d'entreprises (admin)",
     *     tags={"Admin - Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche par nom, CFE ou adresse",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste paginée des entreprises",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé - Admin uniquement")
     * )
     */
    public function companies(Request $request): JsonResponse
    {
        $paginator = Company::query()
            ->with('user:id,name,email')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = mb_strtolower($request->string('search')->value());
                $query->where(function ($inner) use ($term) {
                    $inner
                        ->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(cfe_number) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(address) LIKE ?', ["%{$term}%"]);
                });
            })
            ->withCount('posts')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10));

        return $this->paginatedResponse($paginator, fn ($company) => [
            'id' => $company->id,
            'name' => $company->name,
            'cfe_number' => $company->cfe_number,
            'address' => $company->address,
            'owner' => $company->user,
            'posts_count' => $company->posts_count,
            'created_at' => $company->created_at,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/posts",
     *     summary="Recherche de publications (admin)",
     *     tags={"Admin - Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche par titre ou contenu",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste paginée des publications",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé - Admin uniquement")
     * )
     */
    public function posts(Request $request): JsonResponse
    {
        $paginator = Post::query()
            ->with([
                'user:id,name,email',
                'company:id,name',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = mb_strtolower($request->string('search')->value());
                $query->where(function ($inner) use ($term) {
                    $inner
                        ->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(content) LIKE ?', ["%{$term}%"]);
                });
            })
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10));

        return $this->paginatedResponse($paginator, fn ($post) => [
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'author' => $post->user,
            'company' => $post->company,
            'created_at' => $post->created_at,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/users/{user}",
     *     summary="Supprimer un utilisateur (admin)",
     *     tags={"Admin - Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur supprimé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Impossible de supprimer un administrateur")
     * )
     */
    public function deleteUser(User $user): JsonResponse
    {
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Impossible de supprimer un administrateur.',
            ], 403);
        }

        $user->companies()->delete();
        $user->posts()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/companies/all",
     *     summary="Liste de toutes les entreprises (admin)",
     *     tags={"Admin - Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste complète des entreprises",
     *         @OA\JsonContent(
     *             @OA\Property(property="companies", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function allCompanies(): JsonResponse
    {
        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return response()->json([
            'companies' => $companies,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/companies/{company}",
     *     summary="Supprimer une entreprise (admin)",
     *     tags={"Admin - Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entreprise supprimée"
     *     )
     * )
     */
    public function deleteCompany(Company $company): JsonResponse
    {
        $company->posts()->delete();
        $company->delete();

        return response()->json([
            'message' => 'Entreprise supprimée avec succès.',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/posts",
     *     summary="Créer une publication (admin)",
     *     tags={"Admin - Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="company_id", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Publication créée"
     *     )
     * )
     */
    public function createPost(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);

        $post = $request->user()->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'company_id' => $validated['company_id'] ?? null,
        ]);

        $post->load(['user:id,name,email', 'company:id,name']);

        return response()->json([
            'message' => 'Publication créée avec succès.',
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'author' => $post->user,
                'company' => $post->company,
                'created_at' => $post->created_at,
            ],
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/posts/{post}",
     *     summary="Modifier une publication (admin)",
     *     tags={"Admin - Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="company_id", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Publication modifiée"
     *     ),
     *     @OA\Response(response=403, description="Vous ne pouvez modifier que vos propres publications")
     * )
     */
    public function updatePost(Request $request, Post $post): JsonResponse
    {
        // Vérifier que l'admin est le propriétaire de la publication
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Vous ne pouvez modifier que vos propres publications.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'company_id' => $validated['company_id'] ?? null,
        ]);

        $post->load(['user:id,name,email', 'company:id,name']);

        return response()->json([
            'message' => 'Publication modifiée avec succès.',
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'author' => $post->user,
                'company' => $post->company,
                'created_at' => $post->created_at,
            ],
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/posts/{post}",
     *     summary="Supprimer une publication (admin)",
     *     tags={"Admin - Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Publication supprimée"
     *     )
     * )
     */
    public function deletePost(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json([
            'message' => 'Publication supprimée avec succès.',
        ]);
    }

    protected function paginatedResponse($paginator, callable $transformer): JsonResponse
    {
        $data = $paginator->getCollection()->map($transformer)->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }
}
