<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Company;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Liste des publications",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche dans titre et contenu",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="mine",
     *         in="query",
     *         description="Afficher uniquement mes publications (true/false)",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des publications récupérée",
     *         @OA\JsonContent(
     *             @OA\Property(property="posts", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Mon titre"),
     *                 @OA\Property(property="content", type="string", example="Contenu de la publication"),
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="company", type="object"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $onlyMine = $request->boolean('mine');

        $posts = Post::query()
            ->with(['user:id,name', 'company:id,name'])
            ->when($onlyMine, fn ($query) => $query->where('user_id', $request->user()->id))
            ->when($search, function ($query, $term) {
                $likeTerm = '%'.Str::lower($term).'%';
                $query->where(function ($subQuery) use ($likeTerm) {
                    $subQuery->whereRaw('LOWER(title) LIKE ?', [$likeTerm])
                        ->orWhereRaw('LOWER(content) LIKE ?', [$likeTerm]);
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['posts' => $posts]);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Créer une nouvelle publication",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string", example="Ma nouvelle publication"),
     *             @OA\Property(property="content", type="string", example="Contenu détaillé de la publication..."),
     *             @OA\Property(property="company_id", type="integer", example=1, description="ID entreprise (optionnel, doit appartenir à l'utilisateur)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Publication créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="post", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function store(PostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->ensureCompanyOwnership($request, $data['company_id'] ?? null);

        $post = $request->user()->posts()->create($data);

        return response()->json(['post' => $post->load('company')], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{post}",
     *     summary="Modifier une publication",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         description="ID de la publication",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string", example="Titre modifié"),
     *             @OA\Property(property="content", type="string", example="Contenu modifié"),
     *             @OA\Property(property="company_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Publication modifiée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="post", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $this->ensurePostOwner($request->user()->id, $post);

        $data = $request->validated();
        $this->ensureCompanyOwnership($request, $data['company_id'] ?? null);

        $post->update($data);

        return response()->json(['post' => $post->load('company')]);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{post}",
     *     summary="Supprimer une publication",
     *     tags={"Posts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         description="ID de la publication",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Publication supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Publication supprimée avec succès.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->ensurePostOwner($request->user()->id, $post);

        $post->delete();

        return response()->json(['message' => 'Publication supprimée avec succès.']);
    }

    protected function ensureCompanyOwnership(Request $request, ?int $companyId): void
    {
        if (! $companyId) {
            return;
        }

        $ownedCompany = Company::where('id', $companyId)
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_unless($ownedCompany, 422, 'Cette entreprise ne vous appartient pas.');
    }

    protected function ensurePostOwner(int $userId, Post $post): void
    {
        abort_if($post->user_id !== $userId, 403, 'Accès refusé.');
    }
}
