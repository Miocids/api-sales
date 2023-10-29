<?php namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreCustomerRequest, UpdateCustomerRequest};
use App\Http\Resources\{CustomerCollection, CustomerResource};
use Illuminate\Http\{JsonResponse, Response};
use App\Services\{CustomerService};

/**
 * @property CustomerService $customerService
 */
class CustomerController extends Controller
{
    /**
     * CustomerController Constructor
     *
     * @param CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/v1/customers",
     *   tags={"v1/customers"},
     *   summary="Customer Display",
     *   description="Display a listing of the resource.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\Parameter(
     *      name="status",
     *      in="query",
     *      required=false,
     *      description="This flag is to show the Customer by status",
     *      @OA\Schema(type="boolean")
     *   ),
     * 
     *  @OA\Parameter(
     *      name="text",
     *      in="query",
     *      required=false,
     *      description="Filtrado de campos name y email",
     *      @OA\Schema(type="string")
     *   ),
     *
     *  @OA\Parameter(
     *      name="page",
     *      in="query",
     *      required=false,
     *      description="Indicates the page to display in the pager",
     *      @OA\Schema(type="integer")
     *   ),
     *
     *  @OA\Parameter(
     *      name="to",
     *      in="query",
     *      required=false,
     *      description="Indicates the limit of information to display per page by default showed 15 register",
     *      @OA\Schema(type="integer")
     *   ),
     *
     *   @OA\Response( response=201, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=200, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=422, description="Unprocessable Entity", @OA\JsonContent() ),
     *   @OA\Response( response=401, description="Unauthenticated"),
     *   @OA\Response( response=400, description="Bad Request"),
     *   @OA\Response( response=404, description="Not found"),
     *   @OA\Response( response=403, description="Forbidden")
     * )
     *
     * @return CustomerCollection
     */
    public function index(): CustomerCollection
    {
        return new CustomerCollection($this->customerService->getAllQuery());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/customers",
     *   tags={"v1/customers"},
     *   summary="Customer Store",
     *   description="Store a newly created resource in storage.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"name","email","address"},
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="email",type="string"),
     *          @OA\Property(property="address",type="string"),
     *
     *       ),
     *  ),
     *
     *   @OA\Response( response=201, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=200, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=422, description="Unprocessable Entity", @OA\JsonContent() ),
     *   @OA\Response( response=401, description="Unauthenticated"),
     *   @OA\Response( response=400, description="Bad Request"),
     *   @OA\Response( response=404, description="Not found"),
     *   @OA\Response( response=403, description="Forbidden")
     *)
     * @param StoreCustomerRequest $storeCustomerRequest
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(StoreCustomerRequest $storeCustomerRequest): JsonResponse
    {
        return (new CustomerResource($this->customerService->store()))->response()->setStatusCode(
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/v1/customers/{id}",
     *   tags={"v1/customers"},
     *   summary="Customer Spedified",
     *   description="Display the specified resource",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      description="Display the specified resource",
     *      @OA\Schema(type="string")
     *   ),
     *   @OA\Response( response=201, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=200, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=422, description="Unprocessable Entity", @OA\JsonContent() ),
     *   @OA\Response( response=401, description="Unauthenticated"),
     *   @OA\Response( response=400, description="Bad Request"),
     *   @OA\Response( response=404, description="Not found"),
     *   @OA\Response( response=403, description="Forbidden")
     *)
     *
     * @param string $id
     * @return CustomerResource
     */
    public function show(string $id): CustomerResource
    {
        return new CustomerResource($this->customerService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/v1/customers/{id}",
     *   tags={"v1/customers"},
     *   summary="Customer Update",
     *   description="Update the specified resource in storage.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(type="string")
     *   ),
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"fields1","fields2","fields3"},
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="email",type="string"),
     *          @OA\Property(property="address",type="string"),
     *
     *       ),
     *  ),
     *
     *   @OA\Response( response=201, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=200, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=422, description="Unprocessable Entity", @OA\JsonContent() ),
     *   @OA\Response( response=401, description="Unauthenticated"),
     *   @OA\Response( response=400, description="Bad Request"),
     *   @OA\Response( response=404, description="Not found"),
     *   @OA\Response( response=403, description="Forbidden")
     *)
     *
     * @param UpdateCustomerRequest $updateCustomerRequest
     * @param string $id
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function update(UpdateCustomerRequest $updateCustomerRequest, string $id): ?JsonResponse
    {
        return (new CustomerResource($this->customerService->update($id)))->response()->setStatusCode(
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/v1/customers/{id}",
     *   tags={"v1/customers"},
     *   summary="Customer Delete",
     *   description="Remove the specified resource from storage",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(type="string")
     *   ),
     *   @OA\Response( response=201, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=200, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=422, description="Unprocessable Entity", @OA\JsonContent() ),
     *   @OA\Response( response=401, description="Unauthenticated"),
     *   @OA\Response( response=400, description="Bad Request"),
     *   @OA\Response( response=404, description="Not found"),
     *   @OA\Response( response=403, description="Forbidden")
     *)
     *
     * @param string $id
     * @return JsonResponse|null
     */
    public function destroy(string $id): ?JsonResponse
    {
        return (new CustomerResource($this->customerService->delete($id)))->response()->setStatusCode(
            Response::HTTP_NO_CONTENT
        );
    }
}