<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="ProConnect API",
 *     description="API de la plateforme ProConnect - Application mobile et portail d'administration",
 *     @OA\Contact(
 *         email="contact@proconnect.test"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum",
 *     description="Entrez le token Sanctum obtenu après connexion"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints d'authentification pour les utilisateurs mobiles"
 * )
 *
 * @OA\Tag(
 *     name="Profile",
 *     description="Gestion du profil utilisateur"
 * )
 *
 * @OA\Tag(
 *     name="Companies",
 *     description="CRUD des entreprises"
 * )
 *
 * @OA\Tag(
 *     name="Posts",
 *     description="CRUD des publications"
 * )
 *
 * @OA\Tag(
 *     name="Admin - Authentication",
 *     description="Authentification des administrateurs"
 * )
 *
 * @OA\Tag(
 *     name="Admin - Stats",
 *     description="Statistiques globales"
 * )
 *
 * @OA\Tag(
 *     name="Admin - Search",
 *     description="Recherche d'utilisateurs, entreprises et publications"
 * )
 *
 * @OA\Tag(
 *     name="Admin - Users",
 *     description="Gestion des administrateurs"
 * )
 */
class SwaggerController extends Controller
{
    //
}
