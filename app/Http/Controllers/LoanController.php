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
     * Listar todos os empréstimos
     *
     * @return JsonResponse
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
     * Obter empréstimo específico
     *
     * @param int $id
     * @return JsonResponse
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
     * Criar novo empréstimo
     *
     * @param LoanStoreRequest $request
     * @return JsonResponse
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
     * Atualizar status do empréstimo
     *
     * @param LoanUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(LoanUpdateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        // Se o request inclui uma data de devolução
        if (isset($data['due_date'])) {
            $dueDate = Carbon::parse($data['due_date']);
            $this->loanService->extendLoan($id, $dueDate->diffInDays(Carbon::now()));
        }

        $loan = $this->loanService->getLoanById($id);

        return response()->json([
            'data' => $loan,
            'message' => 'Empréstimo atualizado com sucesso',
            'success' => true
        ]);
    }

    /**
     * Devolver livro
     *
     * @param int $id
     * @return JsonResponse
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
     * Obter empréstimos atrasados
     *
     * @return JsonResponse
     */
    public function delayed(): JsonResponse
    {
        $loans = $this->loanService->getDelayedLoans();

        return response()->json([
            'data' => $loans,
            'message' => 'Empréstimos atrasados obtidos com sucesso',
            'success' => true
        ]);
    }

    /**
     * Prorrogar empréstimo
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function extend(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:30'
        ]);

        $this->loanService->extendLoan($id, $request->input('days'));
        $loan = $this->loanService->getLoanById($id);

        return response()->json([
            'data' => $loan,
            'message' => 'Empréstimo prorrogado com sucesso',
            'success' => true
        ]);
    }
}
