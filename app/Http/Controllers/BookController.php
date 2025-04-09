<?php

namespace App\Http\Controllers;

use App\DTO\BookDTO;
use App\Enums\BookGenreEnum;
use App\Http\Requests\BookStoreRequest;
use App\Http\Requests\BookUpdateRequest;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * Listar todos os livros
     *
     * @return JsonResponse
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
     * Obter livro específico
     *
     * @param int $id
     * @return JsonResponse
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
     * Criar novo livro
     *
     * @param BookStoreRequest $request
     * @return JsonResponse
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
     * Atualizar livro existente
     *
     * @param BookUpdateRequest $request
     * @param int $id
     * @return JsonResponse
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
     * Remover livro
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->bookService->deleteBook($id);

        return response()->json([
            'message' => 'Livro removido com sucesso',
            'success' => true
        ]);
    }

    /**
     * Obter livros disponíveis
     *
     * @return JsonResponse
     */
    public function available(): JsonResponse
    {
        $books = $this->bookService->getAvailableBooks();

        return response()->json([
            'data' => $books,
            'message' => 'Livros disponíveis obtidos com sucesso',
            'success' => true
        ]);
    }

    /**
     * Obter livros por gênero
     *
     * @param string $genre
     * @return JsonResponse
     */
    public function byGenre(string $genre): JsonResponse
    {
        $books = $this->bookService->getBooksByGenre(BookGenreEnum::from($genre));

        return response()->json([
            'data' => $books,
            'message' => 'Livros por gênero obtidos com sucesso',
            'success' => true
        ]);
    }

    /**
     * Pesquisar livros
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:3'
        ]);

        $books = $this->bookService->searchBooks($request->input('q'));

        return response()->json([
            'data' => $books,
            'message' => 'Busca realizada com sucesso',
            'success' => true
        ]);
    }
}
