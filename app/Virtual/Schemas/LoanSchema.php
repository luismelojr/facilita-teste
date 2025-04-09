<?php

namespace App\Virtual\Schemas;

/**
 * @OA\Schema(
 *     schema="Loan",
 *     title="Empréstimo",
 *     description="Modelo de empréstimo de livro",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="book_id",
 *         type="integer",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date",
 *         example="2024-12-31"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         example="active",
 *         enum={"active", "delayed", "returned"}
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
 *     schema="LoanCreateRequest",
 *     title="Requisição de Criação de Empréstimo",
 *     required={"user_id", "book_id"},
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         example=1,
 *         description="ID do usuário que está fazendo o empréstimo"
 *     ),
 *     @OA\Property(
 *         property="book_id",
 *         type="integer",
 *         example=1,
 *         description="ID do livro a ser emprestado"
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date",
 *         example="2024-12-31",
 *         description="Data de devolução (opcional)"
 *     )
 * )
 */
class LoanSchema
{
}
