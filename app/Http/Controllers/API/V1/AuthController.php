<?php namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreAuthRequest, StoreSignInRequest, UpdateAuthRequest, ResetAuthRequest};
use App\Http\Resources\{AuthResource};
use App\Services\{UserService};
use Illuminate\Http\{JsonResponse, Response};

/**
 * @property UserService $authService
 */
class AuthController extends Controller
{

   /**
    * @property UserService $authService
    */
    private UserService $authService;

    /**
     * AuthController Constructor
     *
     * @param UserService $authService
     */
    public function __construct(UserService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Post(
     *   path="/api/v1/auth/sign-in",
     *   tags={"v1/auths"},
     *   summary="Auth Login Display",
     *   description="Genera y accede al inicio de sesion del sistema",
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"email","password"},
     *          @OA\Property(property="email",type="string"),
     *          @OA\Property(property="password",type="string"),
     *          @OA\Property(property="remember_me",type="boolean"),
     *       ),
     *  ),
     *   @OA\Response( response=201, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=200, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=422, description="Unprocessable Entity", @OA\JsonContent() ),
     *   @OA\Response( response=401, description="Unauthenticated"),
     *   @OA\Response( response=400, description="Bad Request"),
     *   @OA\Response( response=404, description="Not found"),
     *   @OA\Response( response=403, description="Forbidden")
     * )
     *
     * @param StoreSignInRequest $signInRequest
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function signIn(StoreSignInRequest $signInRequest): ?JsonResponse
    {
        return (new AuthResource($this->authService->signIn()))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/auth/sign-up",
     *   tags={"v1/auths"},
     *   summary="Auth Sign Up",
     *   description="Store a newly created resource in storage.",
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"name","email","password"},
     *          @OA\Property(property="name",type="string"),
     *          @OA\Property(property="email",type="string"),
     *          @OA\Property(property="password",type="string"),
     *      ),
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
     * @param StoreAuthRequest $storeAuthRequest
     * @return JsonResponse
     * @throws \Exception
     */
    public function signUp(StoreAuthRequest $storeAuthRequest): JsonResponse
    {
        return (new AuthResource($this->authService->signUp()))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/v1/auth/logout",
     *   tags={"v1/auths"},
     *   summary="Auth Logout",
     *   description="Logged out user",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
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
     * @return AuthResource
     * @throws \Exception
     */
    public function logout(): AuthResource
    {
        return new AuthResource($this->authService->logout());
    }

    /**
     * Reset the specified resource in storage password by email to user.
     *
     * @OA\Post(
     *   path="/api/v1/auth/reset-password",
     *   tags={"v1/auths"},
     *   summary="Auth reset Password",
     *   description="Resetea la contraseña ingresando unicamente el email, codigo y la nueva contraseña",
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"token","password"},
     *          @OA\Property(property="token",type="string", description="Este token se genera y se envia por correo"),
     *          @OA\Property(property="password",type="string", description="Se genera la contraseña nueva"),
     *          @OA\Property(property="confirm_password",type="string", description="Se confirma la contraseña ingresada"),
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
     * @param ResetAuthRequest $resetAuthRequest
     * @return JsonResponse|null
     */
    public function reset(ResetAuthRequest $resetAuthRequest): ?JsonResponse
    {
        return (new AuthResource($this->authService->reset()))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @OA\Post(
     *   path="/api/v1/auth/forgot-password",
     *   tags={"v1/auths"},
     *   summary="Auth forgot Password",
     *   description="Recuperar contraseña ingresando unicamente el email",
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"email"},
     *          @OA\Property(property="email",type="string"),
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
     * @param UpdateAuthRequest $updateAuthRequest
     * @return JsonResponse|null
     */
    public function forgot(UpdateAuthRequest $updateAuthRequest): ?JsonResponse
    {
        return (new AuthResource($this->authService->forgot($updateAuthRequest)))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }
}
