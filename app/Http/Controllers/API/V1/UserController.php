<?php namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreUserRequest, UpdateUserRequest};
use App\Http\Resources\{UserCollection, UserResource};
use Illuminate\Http\{JsonResponse, Response};
use App\Services\{UserService};

/**
 * @property UserService $userService
 */
class UserController extends Controller
{
    /**
     * UserController Constructor
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/v1/users",
     *   tags={"v1/users"},
     *   summary="User Display",
     *   description="Display a listing of the resource.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     * 
     *  @OA\Parameter(
     *      name="text",
     *      in="query",
     *      required=false,
     *      description="Texto para realizar la busqueda de este modulo por nombre y correo de usuario",
     *      @OA\Schema(type="string")
     *   ),
     *
     *  @OA\Parameter(
     *      name="status",
     *      in="query",
     *      required=false,
     *      description="This flag is to show the User by status",
     *      @OA\Schema(type="boolean")
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
     * @return UserCollection
     */
    public function index(): UserCollection
    {
        return new UserCollection($this->userService->getAllQuery());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/users",
     *   tags={"v1/users"},
     *   summary="User Store",
     *   description="Store a newly created resource in storage.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"email","name","username","password","status"},
     *          @OA\Property(property="email",type="string"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="username",type="string"),
     *          @OA\Property(property="password",type="string"),
     *          @OA\Property(property="replay_password",type="string"),
     *          @OA\Property(property="status",type="boolean"),
     *          @OA\Property(property="is_notify",type="boolean"),
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
     * @param StoreUserRequest $storeUserRequest
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(StoreUserRequest $storeUserRequest): JsonResponse
    {
        return (new UserResource($this->userService->store()))->response()->setStatusCode(
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/v1/users/{id}",
     *   tags={"v1/users"},
     *   summary="User Spedified",
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
     * @return UserResource
     */
    public function show(string $id): UserResource
    {
        return new UserResource($this->userService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/v1/users/{id}",
     *   tags={"v1/users"},
     *   summary="User Update",
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
     *          required={"email","name","username","status"},
     *          @OA\Property(property="email",type="string"),
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="username",type="string"),
     *          @OA\Property(property="password",type="string"),
     *          @OA\Property(property="reply_password",type="string"),
     *          @OA\Property(property="status",type="boolean"),
     *          @OA\Property(property="is_notify",type="boolean"),
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
     * @param UpdateUserRequest $updateUserRequest
     * @param string $id
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function update(UpdateUserRequest $updateUserRequest, string $id): ?JsonResponse
    {
        return (new UserResource($this->userService->update($id)))->response()->setStatusCode(
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/v1/users/{id}",
     *   tags={"v1/users"},
     *   summary="User Delete",
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
        return (new UserResource($this->userService->delete($id)))->response()->setStatusCode(
            Response::HTTP_NO_CONTENT
        );
    }
}