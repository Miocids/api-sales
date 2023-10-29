<?php namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreNoteItemRequest, UpdateNoteItemRequest};
use App\Http\Resources\{NoteItemCollection, NoteItemResource};
use Illuminate\Http\{JsonResponse, Response};
use App\Services\{NoteItemService};

/**
 * @property NoteItemService $noteItemService
 */
class NoteItemController extends Controller
{
    /**
     * NoteItemController Constructor
     *
     * @param NoteItemService $noteItemService
     */
    public function __construct(NoteItemService $noteItemService)
    {
        $this->noteItemService = $noteItemService;
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/v1/note-items",
     *   tags={"v1/note-items"},
     *   summary="NoteItem Display",
     *   description="Display a listing of the resource.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\Parameter(
     *      name="status",
     *      in="query",
     *      required=false,
     *      description="This flag is to show the NoteItem by status",
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
     * @return NoteItemCollection
     */
    public function index(): NoteItemCollection
    {
        return new NoteItemCollection($this->noteItemService->getAllQuery());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/note-items",
     *   tags={"v1/note-items"},
     *   summary="NoteItem Store",
     *   description="Store a newly created resource in storage.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *               required={"file","customer","item","quantity"},
     *               @OA\Property(
     *                   description="File to upload",
     *                   property="file",
     *                   type="string",
     *                   format="binary",
     *               ),
     *              @OA\Property(property="customer",type="string", description="Se envía el id del cliente, este se obtiene del endpoint v2/customers"),
     *              @OA\Property(property="item",type="string",description="Se envía el id del item, este se obtiene del endpoint v2/items"),
     *              @OA\Property(property="quantity", type="string"),
     *           )
     *       )
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
     * @param StoreNoteItemRequest $storeNoteItemRequest
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(StoreNoteItemRequest $storeNoteItemRequest): JsonResponse
    {
        return (new NoteItemResource($this->noteItemService->store()))->response()->setStatusCode(
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/v1/note-items/{id}",
     *   tags={"v1/note-items"},
     *   summary="NoteItem Spedified",
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
     * @return NoteItemResource
     */
    public function show(string $id): NoteItemResource
    {
        return new NoteItemResource($this->noteItemService->getById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/v1/note-items/{id}",
     *   tags={"v1/note-items"},
     *   summary="NoteItem Update",
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
     *          required={"customer","item","quantity"},
     *          @OA\Property(
     *              property="customer",
     *              type="object",
     *              @OA\Property(property="id", type="string", description="Se envía el id del cliente, este se obtiene del endpoint v2/customers")
     *          ),
     *          @OA\Property(
     *              property="item",
     *              type="object",
     *              @OA\Property(property="id", type="string", description="Se envía el id del item, este se obtiene del endpoint v2/items")
     *          ),
     *          @OA\Property(property="quantity", type="string"),
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
     * @param UpdateNoteItemRequest $updateNoteItemRequest
     * @param string $id
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function update(UpdateNoteItemRequest $updateNoteItemRequest, string $id): ?JsonResponse
    {
        return (new NoteItemResource($this->noteItemService->update($id)))->response()->setStatusCode(
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/v1/note-items/{id}",
     *   tags={"v1/note-items"},
     *   summary="NoteItem Delete",
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
        return (new NoteItemResource($this->noteItemService->delete($id)))->response()->setStatusCode(
            Response::HTTP_NO_CONTENT
        );
    }
}