<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Dictionary API",
 *     description="A RESTful API for dictionary management with user authentication and word lookup",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     description="Dictionary API Server",
 *     url=L5_SWAGGER_CONST_HOST
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 *     schema="Pagination",
 *     type="object",
 *     @OA\Property(property="totalDocs", type="integer", example=100),
 *     @OA\Property(property="page", type="integer", example=1),
 *     @OA\Property(property="totalPages", type="integer", example=10),
 *     @OA\Property(property="hasNext", type="boolean", example=true),
 *     @OA\Property(property="hasPrev", type="boolean", example=false)
 * )
 */
class OpenApi {}
