<?php namespace App\Services;

use Illuminate\Mail\Message;
use App\Repositories\{UserRepository};
use Illuminate\Support\Facades\{Auth, Cache, DB, Mail, Password};
use Illuminate\Support\{Arr, Collection, Str};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\{Response};
use App\Http\Requests\{UpdateAuthRequest};
use App\Mail\{PasswordResetMail, PasswordChangedMail};

class UserService extends UserRepository
{
    /**
     * Store a newly created resource in storage.
     *
     * @return array
     * @throws \Exception
     */
    public function signIn(): array
    {
        DB::beginTransaction();
        try {
            if (!Auth::attempt(\request()->merge(["status" => true])->except("remember_me")))
                throw new \Exception(
                    "No coiciden las credenciales ingresadas, favor de verificar", Response::HTTP_UNPROCESSABLE_ENTITY
                );
            $user        = \request()->user();
            $tokenResult = $user->createToken(
                \request()->user()->password.\now()->toDateTimeString()
            );
            $token       = $tokenResult->token;
            if (\request("remember_me"))
                $token->expires_at = now()->addWeeks(1);
            $token->save();

            DB::commit();

            return [
                'id'           => $user->getKey(),
                'email'        => $user->email,
                'name'         => $user->name,
                'token'        => $tokenResult->accessToken,
                'token_type'   => 'Bearer',
                'expires_at'   => $token->expires_at->toDateTimeString()
            ];

        } catch (\Throwable $e) {
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            \Log::error($error);
            DB::rollback();

            throw new \Exception($e->getMessage(),$e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Model|null
     * @throws \Exception
     */
    public function signUp(): ?array
    {
        DB::beginTransaction();
        try {
            \request()->merge([
                "password" => bcrypt(\request("password")),
                "username" => request()->string("name")->replace(" ",".")->lower(),
                "status"   => true
            ]);
            $response  = $this->saveRepository(\request()->only("name","email","password","status"));

            DB::commit();

            return [
                "id"        => $response->getKey(),
                "name"      => $response->name,
                "email"     => $response->email
            ];

        } catch (\Throwable $e) {
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            \Log::error($error);
            DB::rollback();

            throw new \Exception($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return array|null
     */
    public function logout(): array|null
    {
       \request()->user()->token()->revoke();

       return [
         "message" => "Desconectado con exito"
       ];
    }

    /**
     * Is responsible for resetting the password to be able to add a new password
     *
     * @return array
     */
    public function reset(): array
    {
        $passwordReset = (new PasswordResetService())->getBy(["token" => \request("token")]);
        if ((new PasswordResetService)->isExpire($passwordReset))
            throw new \Exception("El codigo ingresado expiro, se debe generar otro", Response::HTTP_UNPROCESSABLE_ENTITY);

        $user = $this->getBy(["email" => $passwordReset->email]);        
        $user->update(\request()->merge(["password" => \bcrypt(\request("password"))])->only('password'));
        $passwordReset->query()->delete();
        
        Mail::to($passwordReset->email)->send(new PasswordChangedMail($user));

        return [
            "message" => "Se reestablecio con exito su contraseña, puede iniciar sesion"
        ];

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UpdateAuthRequest $request
     *
     * @return array
     */
    public function forgot(UpdateAuthRequest $request): array
    {
        (new PasswordResetService)->deleteByEmail(\request("email"));
        $user     = $this->getBy(["email" => \request("email")]);
        $codeData =(new PasswordResetService)->saveRepository($request->data());
        Mail::to(\request("email"))->send(new PasswordResetMail($user,$codeData->token));

        return [
            "message" => "Se envio un codigo para reestablecer la contraseña al correo ingresado"
        ];

    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $id
     * @return Model|null
     * @throws \Exception
     */
    public function update(string $id): ?Model
    {
        DB::beginTransaction();
        try {
            $response = $this->updateRepository(
                \request()->except(["info","password","role"]),
                $id
            );

            if (\request()->has("password") && \request("password")){
                if (\request('password') !== \request('reply_password')){
                    throw new \Exception(
                        "No concuerdan las contraseñas ingresadas, favor de verificar",
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                \request()->merge(["password" => bcrypt(\request()->string("password"))]);

                $response->update(\request()->only("password"));
            }

            DB::commit();

            return $response;

        } catch (\Throwable $e) {
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            \Log::error($error);
            DB::rollback();

            throw new \Exception($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Model|null
     * @throws \Exception
     */
    public function store(): ?Model
    {
        DB::beginTransaction();
        try {

            if (\request()->has("password") && \request("password")){
                if (\request('password') !== \request('replay_password')){
                    throw new \Exception(
                        "No concuerdan las contraseñas ingresadas, favor de verificar",
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
                \request()->merge(["password" => bcrypt(\request()->string("password"))]);
            }
            $response = $this->saveRepository(\request()->except("replay_password"));

            DB::commit();

            return $response;

        } catch (\Throwable $e) {
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            \Log::error($error);
            DB::rollback();

            throw new \Exception($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }
}
