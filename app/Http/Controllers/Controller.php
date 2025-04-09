<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="API de Biblioteca",
 *     version="1.0.0",
 *     description="Sistema de Gerenciamento de Biblioteca",
 *     @OA\Contact(
 *         email="suporte@biblioteca.com",
 *         name="Equipe de Suporte"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api/v1",
 *     description="Servidor de API da Biblioteca"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
