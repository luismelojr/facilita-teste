<?php

namespace App\Virtual\Schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="Usuário",
 *     description="Modelo de usuário da biblioteca",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Luis Henrique"),
 *     @OA\Property(property="email", type="string", example="luis@example.com"),
 *     @OA\Property(property="registration_number", type="string", example="REG123"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
/**
 * @OA\Schema(
 *     schema="UserCreateRequest",
 *     title="Requisição de Criação de Usuário",
 *     required={"name", "email", "registration_number", "password"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Luis Henrique",
 *         description="Nome completo do usuário"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="luis@example.com",
 *         description="Endereço de email do usuário"
 *     ),
 *     @OA\Property(
 *         property="registration_number",
 *         type="string",
 *         example="REG12345",
 *         description="Número de registro único do usuário"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         example="senha123",
 *         description="Senha do usuário"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UserUpdateRequest",
 *     title="Requisição de Atualização de Usuário",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Luis Henrique",
 *         description="Nome completo do usuário"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="luis@example.com",
 *         description="Endereço de email do usuário"
 *     ),
 *     @OA\Property(
 *         property="registration_number",
 *         type="string",
 *         example="REG12345",
 *         description="Número de registro único do usuário"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         example="novaSenha123",
 *         description="Nova senha do usuário (opcional)"
 *     )
 * )
 */

class UserSchema
{
}
