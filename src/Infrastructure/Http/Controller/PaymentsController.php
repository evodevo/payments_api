<?php

declare(strict_types=1);

namespace PaymentsAPI\Infrastructure\Http\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Tests\Functional\Controller\FOSRestController;
use PaymentsAPI\Application\Query\GetTransaction;
use PaymentsAPI\Infrastructure\Http\Exception\ValidationException;
use Swagger\Annotations as SWG;
use PaymentsAPI\Application\Command\ConfirmTransaction;
use PaymentsAPI\Application\Command\ConfirmTransactionHandler;
use PaymentsAPI\Application\Command\CreateTransaction;
use PaymentsAPI\Application\Command\CreateTransactionHandler;
use PaymentsAPI\Application\Query\GetTransactionHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class PaymentsController
 * @package PaymentsAPI\Infrastructure\Http\Controller
 */
class PaymentsController extends FOSRestController
{
    /**
     * Creates a new transaction
     * @Rest\Post("/api/transactions")
     * @ParamConverter("createTransaction", converter="fos_rest.request_body")
     * @SWG\Parameter(
     *     name="transaction",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="user_id", type="integer", example="1"),
     *         @SWG\Property(property="details", type="string", example="Transaction number one"),
     *         @SWG\Property(property="recipient_account", type="string", example="12345"),
     *         @SWG\Property(property="recipient_name", type="string", example="John Doe"),
     *         @SWG\Property(property="amount", type="float", example=20.00),
     *         @SWG\Property(property="currency", type="string", example="eur")
     *     )
     * )
     * @SWG\Response(
     *     response=429,
     *     description="Transactions limit exceeded"
     * )
     * @SWG\Response(
     *     response=403,
     *     description="User is forbidden to create a transaction"
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Transaction is invalid"
     * )
     * @SWG\Tag(name="payments")
     * @param CreateTransaction $createTransaction
     * @param CreateTransactionHandler $createTransactionHandler
     * @param ConstraintViolationListInterface $validationErrors
     * @throws \Exception
     * @return JsonResponse
     */
    public function createTransaction(
        CreateTransaction $createTransaction,
        CreateTransactionHandler $createTransactionHandler,
        ConstraintViolationListInterface $validationErrors
    ): JsonResponse {
        if (count($validationErrors) > 0) {
            return $this->errorResponse($validationErrors);
        }

        $transactionData = $createTransactionHandler->handle($createTransaction);

        return new JsonResponse($transactionData, Response::HTTP_CREATED);
    }

    /**
     * Confirms transaction
     * @Rest\Put("/api/transactions/{transactionId<\d+>}/confirmation")
     * @SWG\Parameter(
     *     name="transactionId",
     *     in="path",
     *     required=true,
     *     type="integer",
     *     description="Transaction ID"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="JSON Payload",
     *     required=true,
     *     format="application/json",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="code", type="integer", example="111"),
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not found"
     * )
     * @SWG\Response(
     *     response=409,
     *     description="Transaction already confirmed"
     * )
     * @SWG\Tag(name="payments")
     *
     * @param int $transactionId
     * @param Request $request
     * @param ConfirmTransactionHandler $confirmTransactionHandler
     *
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function confirmTransaction(
        int $transactionId,
        Request $request,
        ConfirmTransactionHandler $confirmTransactionHandler
    ): JsonResponse {
        $code = $request->get('code');
        if (!$code) {
            throw new ValidationException('Missing confirmation code parameter');
        }

        $confirmTransactionHandler->handle(
            new ConfirmTransaction($transactionId, (int)$code)
        );

        return new JsonResponse(['success' => true], Response::HTTP_ACCEPTED);
    }

    /**
     * Gets transaction info
     * @Rest\Get("/api/transactions/{transactionId<\d+>}")
     * @SWG\Parameter(
     *     name="transactionId",
     *     in="path",
     *     type="integer",
     *     required=true,
     *     description="Transaction ID"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns transaction with the given id",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="userId", type="integer", example="1"),
     *         @SWG\Property(property="transactionId", type="integer"),
     *         @SWG\Property(property="details", type="string", example="Transaction number one"),
     *         @SWG\Property(property="recipient_account", type="string", example="12345"),
     *         @SWG\Property(property="recipient_name", type="string", example="John Doe"),
     *         @SWG\Property(property="amount", type="decimal", example="20.00"),
     *         @SWG\Property(property="currency", type="string", example="eur")
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Not found"
     * )
     * @SWG\Tag(name="payments")
     * @param $transactionId
     * @param GetTransactionHandler $getTransactionHandler
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return JsonResponse
     */
    public function getTransaction(int $transactionId, GetTransactionHandler $getTransactionHandler): JsonResponse
    {
        $transactionDto = $getTransactionHandler->handle(new GetTransaction($transactionId));

        return new JsonResponse($transactionDto);
    }

    /**
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    private function errorResponse(ConstraintViolationListInterface $validationErrors)
    {
        $messages = [];

        /** @var ConstraintViolationListInterface $validationError */
        foreach($validationErrors as $validationError) {
            $messages[$validationError->getPropertyPath()] = $validationError->getMessage();
        }

        return new JsonResponse(
            [
                'code' => 400,
                'message' => 'Validation failed',
                'errors' => $messages
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}