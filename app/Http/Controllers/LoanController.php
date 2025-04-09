<?php

namespace App\Http\Controllers;

use App\Exceptions\BookNotAvailableException;
use App\Http\Requests\LoanStoreRequest;
use App\Http\Requests\LoanUpdateRequest;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanController extends Controller
{
    /**
     * @var LoanService
     */
    protected LoanService $loanService;

    /**
     * LoanController constructor.
     *
     * @param LoanService $loanService
     */
    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/loans",
     *     summary="Lista todos os empréstimos",
     *     tags={"Empréstimos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empréstimos",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Loan")
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
        $loans = $this->loanService->getAllLoans();

        return response()->json([
            'data' => $loans,
            'message' => 'Empréstimos obtidos com sucesso',
            'success' => true
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/loans/{id}",
     *     summary="Obter detalhes de um empréstimo específico",
     *     tags={"Empréstimos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do empréstimo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Loan"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empréstimo não encontrado"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $loan = $this->loanService->getLoanById($id);

        return response()->json([
            'data' => $loan,
            'message' => 'Empréstimo obtido com sucesso',
            'success' => true
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/loans",
     *     summary="Criar novo empréstimo",
     *     tags={"Empréstimos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoanCreateRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empréstimo criado com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Loan"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Livro não disponível para empréstimo",
     *         @OA\JsonContent(
     *             type="object",
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
    public function store(LoanStoreRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $dueDate = null;
            if (isset($data['due_date'])) {
                $dueDate = Carbon::parse($data['due_date']);
            }

            $loan = $this->loanService->borrowBook(
                $data['user_id'],
                $data['book_id'],
                $dueDate
            );

            return response()->json([
                'data' => $loan,
                'message' => 'Empréstimo criado com sucesso',
                'success' => true
            ], Response::HTTP_CREATED);

        } catch (BookNotAvailableException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], Response::HTTP_CONFLICT);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/v1/loans/{id}/return",
     *     summary="Devolver livro",
     *     tags={"Empréstimos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livro devolvido com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empréstimo não encontrado"
     *     )
     * )
     */
    public function returnBook(int $id): JsonResponse
    {
        $this->loanService->returnBook($id);

        return response()->json([
            'message' => 'Livro devolvido com sucesso',
            'success' => true
        ]);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/loans/{id}/mark-as-delayed",
     *     summary="Marcar empréstimo como atrasado",
     *     tags={"Empréstimos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empréstimo marcado como atrasado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empréstimo não encontrado"
     *     )
     * )
     */
    public function markAsDelayed(int $id): JsonResponse
    {
        $this->loanService->markAsDelayed($id);

        return response()->json([
            'message' => 'Empréstimo marcado como atrasado',
            'success' => true
        ]);
    }
}
