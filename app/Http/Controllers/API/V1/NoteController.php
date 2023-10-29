<?php namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreNoteRequest, UpdateNoteRequest};
use App\Http\Resources\{NoteCollection, NoteResource};
use Illuminate\Http\{JsonResponse, Response};
use App\Services\{NoteService};

/**
 * @property NoteService $noteService
 */
class NoteController extends Controller
{
    /**
     * NoteController Constructor
     *
     * @param NoteService $noteService
     */
    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/v1/notes",
     *   tags={"v1/notes"},
     *   summary="Note Display",
     *   description="Display a listing of the resource.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\Parameter(
     *      name="status",
     *      in="query",
     *      required=false,
     *      description="This flag is to show the Note by status",
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
     * @return NoteCollection
     */
    public function index(): NoteCollection
    {
        return new NoteCollection($this->noteService->getAllQuery());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/notes",
     *   tags={"v1/notes"},
     *   summary="Note Store",
     *   description="Store a newly created resource in storage.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"customer","date","total"},
     *          @OA\Property(
     *              property="customer",
     *              type="object",
     *              @OA\Property(property="id", type="string")
     *          ),
     *          @OA\Property(property="date",type="date"),
     *          @OA\Property(property="total",type="double"),
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
     * @param StoreNoteRequest $storeNoteRequest
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(StoreNoteRequest $storeNoteRequest): JsonResponse
    {
        return (new NoteResource($this->noteService->store()))->response()->setStatusCode(
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/v1/notes/{id}",
     *   tags={"v1/notes"},
     *   summary="Note Spedified",
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
     * @return NoteResource
     */
    public function show(string $id): NoteResource
    {
        return new NoteResource($this->noteService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/v1/notes/{id}",
     *   tags={"v1/notes"},
     *   summary="Note Update",
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
     *          required={"customer","date","total"},
     *          @OA\Property(
     *              property="customer",
     *              type="object",
     *              @OA\Property(property="id", type="string")
     *          ),
     *          @OA\Property(property="date",type="date"),
     *          @OA\Property(property="total",type="double"),
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
     * @param UpdateNoteRequest $updateNoteRequest
     * @param string $id
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function update(UpdateNoteRequest $updateNoteRequest, string $id): ?JsonResponse
    {
        return (new NoteResource($this->noteService->update($id)))->response()->setStatusCode(
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/v1/notes/{id}",
     *   tags={"v1/notes"},
     *   summary="Note Delete",
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
        return (new NoteResource($this->noteService->delete($id)))->response()->setStatusCode(
            Response::HTTP_NO_CONTENT
        );
    }
}