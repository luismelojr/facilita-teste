<?php

namespace App\Http\Controllers;

use App\DTO\BookDTO;
use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BookController extends Controller
{
    /**
     * @var BookService
     */
    protected BookService $bookService;

    /**
     * BookController constructor.
     *
     * @param BookService $bookService
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/books",
     *     summary="Lista todos os livros",
     *     tags={"Livros"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de livros",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Book")
     *             ),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $books = $this->bookService->getAllBooks();

        return response()->json([
            'data' => $books,
            'message' => 'Livros obtidos com sucesso',
            'success' => true
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/books/{id}",
     *     summary="Obter detalhes de um livro específico",
     *     tags={"Livros"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do livro",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Book"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livro não encontrado"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $book = $this->bookService->getBookById($id);

        return response()->json([
            'data' => $book,
            'message' => 'Livro obtido com sucesso',
            'success' => true
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/books",
     *     summary="Criar novo livro",
     *     tags={"Livros"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BookCreateRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Livro criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Book"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(BookStoreRequest $request): JsonResponse
    {
        $bookDTO = BookDTO::fromArray($request->validated());
        $book = $this->bookService->createBook($bookDTO->toArray());

        return response()->json([
            'data' => $book,
            'message' => 'Livro criado com sucesso',
            'success' => true
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/books/{id}",
     *     summary="Atualizar livro existente",
     *     tags={"Livros"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BookUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livro atualizado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Book"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livro não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function update(BookUpdateRequest $request, int $id): JsonResponse
    {
        $bookData = $request->validated();
        $this->bookService->updateBook($id, $bookData);

        $book = $this->bookService->getBookById($id);

        return response()->json([
            'data' => $book,
            'message' => 'Livro atualizado com sucesso',
            'success' => true
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/books/{id}",
     *     summary="Remover livro",
     *     tags={"Livros"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livro removido com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livro não encontrado"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->bookService->deleteBook($id);

        return response()->json([
            'message' => 'Livro removido com sucesso',
            'success' => true
        ]);
    }
}
