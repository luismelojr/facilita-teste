<?php

namespace App\Virtual\Schemas;

/**
 * @OA\Schema(
 *     schema="Book",
 *     title="Livro",
 *     description="Modelo de livro",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         example="Dom Quixote"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         example="Miguel de Cervantes"
 *     ),
 *     @OA\Property(
 *         property="registration_number",
 *         type="string",
 *         example="LIVRO001"
 *     ),
 *     @OA\Property(
 *         property="genre",
 *         type="string",
 *         example="Ficção"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         example="disponível"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="BookCreateRequest",
 *     title="Requisição de Criação de Livro",
 *     required={"title", "author", "registration_number", "genre"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         example="Dom Quixote",
 *         description="Título do livro"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         example="Miguel de Cervantes",
 *         description="Autor do livro"
 *     ),
 *     @OA\Property(
 *         property="registration_number",
 *         type="string",
 *         example="LIVRO001",
 *         description="Número de registro único do livro"
 *     ),
 *     @OA\Property(
 *         property="genre",
 *         type="string",
 *         example="Ficção",
 *         description="Gênero do livro"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="BookUpdateRequest",
 *     title="Requisição de Atualização de Livro",
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         example="Dom Quixote",
 *         description="Título do livro"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         example="Miguel de Cervantes",
 *         description="Autor do livro"
 *     ),
 *     @OA\Property(
 *         property="registration_number",
 *         type="string",
 *         example="LIVRO001",
 *         description="Número de registro único do livro"
 *     ),
 *     @OA\Property(
 *         property="genre",
 *         type="string",
 *         example="Ficção",
 *         description="Gênero do livro"
 *     )
 * )
 */
class BookSchema
{
}
