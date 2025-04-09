<?php

namespace App\Exceptions;

use App\Models\Book;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookNotAvailableException extends Exception
{
    /**
     * O livro que não está disponível
     *
     * @var Book|null
     */
    protected ?Book $book;

    /**
     * BookNotAvailableException constructor.
     *
     * @param string $message
     * @param Book|null $book
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = "O livro solicitado não está disponível para empréstimo",
        ?Book $book = null,
        int $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->book = $book;
    }

    /**
     * Obter o livro associado à exceção
     *
     * @return Book|null
     */
    public function getBook(): ?Book
    {
        return $this->book;
    }

    /**
     * Renderizar a exceção como uma resposta HTTP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        $response = [
            'message' => $this->getMessage(),
            'success' => false
        ];

        // Adicionar informações do livro se disponíveis
        if ($this->book) {
            $response['book'] = [
                'id' => $this->book->id,
                'title' => $this->book->title,
                'author' => $this->book->author,
                'status' => $this->book->status->value
            ];
        }

        return response()->json($response, Response::HTTP_CONFLICT);
    }
}
