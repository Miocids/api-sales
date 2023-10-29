<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {crud}
                            {--w|--web : Generates default sintaxis WEB in the controller}
                            {--c|--catalog : Generates default sintaxis Catalogs in the views resources}
                            {--a|--api : Generates default sintaxis API in the controller}
                            {--s|--service= : Enter the name of the SERVICE to connect to API}
                            {--g|--generate : Generates default documentation in the controller}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class with my schema';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('crud');
        $path    = "";
        if ($this->option('web')){
            $this->line("Creating CRUD {$name} ...");
            if (!File::exists("resources/views/pages"))
                File::makeDirectory("resources/views/pages",0775,true);

            $strName     = Str::of($name)->replace("Controller","")->snake()->plural()->replace("_","-");
            $path = "resources/views/pages/{$strName}";

            if ($this->option("catalog")){
                if (!File::exists("resources/views/pages/catalogs"))
                    File::makeDirectory("resources/view/pages/catalogs",0775,true);

                if (!File::exists("resources/views/pages/catalogs/{$strName}"))
                    File::makeDirectory("resources/views/pages/catalogs/{$strName}",0775,true);

                $path = "resources/views/pages/catalogs/{$strName}";
            }else{
                if (!File::exists("resources/views/pages/{$strName}"))
                    File::makeDirectory("resources/views/pages/{$strName}",0775,true);
            }

            $schema = $this->schemaFileWebCrud($name);
            $this->schemaFileBladeCrud($path);
            $this->_makeSchemaResources($name);
            #$this->_makeSchemaRequests($name);

            File::put("app/Http/Controllers/{$name}.php",$schema);

            $path = "[App/Http/Controllers/{$name}]";
        }

        if ($this->option('api')){
            $version = Str::of($this->anticipate('¿Ingrese version del API?',["v1","v2"]))->upper();
            $this->line("Creating ApiRest {$name} ...");
            if (!File::exists("app/Http/Controllers/API/{$version}"))
                File::makeDirectory("app/Http/Controllers/API/{$version}",0775,true);

            $schema = $this->schemaFileApiRest($name,$version);
            if ($this->option("generate"))
                $schema = $this->schemaFileRestGenerate($name,$version);

            $this->_makeSchemaResources($name);
            $this->_makeSchemaRequests($name);
            $this->makeTraitRelation($name);

            File::put("app/Http/Controllers/API/{$version}/{$name}.php",$schema);
            $path = "[App/Http/Controllers/API/{$version}/{$name}]";
        }

        if ($this->option('service')){
            $version = Str::of($this->anticipate('¿Ingrese version del Client Service?',["v1","v2"]))->upper();
            $name    = Str::of($name)->replace("Controller","ClientController");
            $this->line("Creating Connection to Client to Service {$name} ...");
            $dirClient = Str::of($this->option('service'))->singular();
            if (!File::exists("app/Http/Controllers/API/{$dirClient}"))
                File::makeDirectory("app/Http/Controllers/API/{$dirClient}",0775,true);

            $schema = $this->schemaFileClientservice($name,$dirClient);
            if ($this->option("generate"))
                $schema = $this->schemaFileClientGenerate($name,$dirClient,$version);

            File::put("app/Http/Controllers/API/{$dirClient}/{$name}.php",$schema);

            $path = "[App/Http/Controllers/API/{$dirClient}/{$name}]";
        }

        $this->info("The rest controller with name {$path} was created successfully");
    }

    /**
     * @param string $name
     * @return void
     */
    public function makeTraitRelation(string $name ): void
    {
        if (!File::exists("app/Traits"))
            File::makeDirectory("app/Traits",0775,true);

        $traitRelations = $this->_schemaTraitHasRelation();
        $trait          = $this->_schemaTrait($name);
        $strName        = Str::of($name)->replace("Controller","");

        File::put("app/Traits/HasRelation.php",$traitRelations);
        File::put("app/Traits/Has{$strName}Relations.php",$trait);
    }
    /**
     *
     * @return string
     */
    private function _schemaTraitHasRelation(): string
    {
        return '<?php namespace App\Traits;

use App\Models\{Company, User};
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

trait HasRelation
{
    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,"created_by","id"
        )->select("id","email","name","created_at","updated_at");
    }

    public function getClientAttribute(): mixed
    {
        if(isset($this->attributes["client"]) && $this->attributes["client"] !== "[]")
            return json_decode($this->attributes["client"],true);

        return null;
    }

    public function getStatusAttribute(): mixed
    {
        if(isset($this->attributes["status"]))
            return !!$this->attributes["status"];

        return false;
    }

}';

    }

    /**
     * @param string $name
     * @return string
     */
    private function _schemaTrait(string $name): string
    {
        $strName            = Str::of($name)->replace("Controller","");
        $relationName       = Str::of($strName)->camel();
        return '<?php namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, MorphOne};

trait Has'.$strName.'Relations
{

    /**
     * @return HasMany
     */
    public function '.$relationName.'hasMany(): HasMany
    {
        return $this->hasMany('.$relationName.'::class);
    }
    /**
     * @return HasOne
     */
    public function '.$relationName.'hasOne(): HasOne
    {
        return $this->hasOne('.$relationName.'::class);
    }
    /**
     * @return BelongsTo
     */
    public function '.$relationName.'BelongsTo(): BelongsTo
    {
        return $this->belongsTo('.$relationName.'::class);
    }

    /**
     * @return MorphOne
     */
    public function '.$relationName.'MorphOne(): MorphOne
    {
        return $this->morphOne(
            '.$relationName.'::class,"morphable"
        );
    }

}';

    }

    /**
     * @param string $name
     * @param string $version
     * @return string
     */
    private function schemaFileRestGenerate(string $name, string $version): string
    {
        $strName            = Str::of($name)->replace("Controller","");
        $serviceName        = "{$strName}Service";
        $requestStoreName   = "Store{$strName}Request";
        $requestUpdateName  = "Update{$strName}Request";
        $resourceName       = "{$strName}Resource";
        $collectionName     = "{$strName}Collection";
        $object             = Str::of($serviceName)->camel();
        $namespace          = "App\\Http\\Controllers\\API\\{$version}";

        return '<?php namespace '.$namespace.';

use App\Http\Controllers\Controller;
use App\Http\Requests\{'.$requestStoreName.', '.$requestUpdateName.'};
use App\Http\Resources\{'.$collectionName.', '.$resourceName.'};
use Illuminate\Http\{JsonResponse, Response};
use App\Services\{'.$serviceName.'};

/**
 * @property '.$serviceName.' $'.$object.'
 */
class '.$name.' extends Controller
{
    /**
     * '.$name.' Constructor
     *
     * @param '.$serviceName.' $'.$object.'
     */
    public function __construct('.$serviceName.' $'.$object.')
    {
        $this->'.$object.' = $'.$object.';
    }

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'",
     *   tags={"'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'"},
     *   summary="'.$strName.' Display",
     *   description="Display a listing of the resource.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\Parameter(
     *      name="status",
     *      in="query",
     *      required=false,
     *      description="This flag is to show the '.$strName.' by status",
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
     * @return '.$collectionName.'
     */
    public function index(): '.$collectionName.'
    {
        return new '.$collectionName.'($this->'.$object.'->getAllQuery());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'",
     *   tags={"'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'"},
     *   summary="'.$strName.' Store",
     *   description="Store a newly created resource in storage.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"fields1","fields2","fields3"},
     *          @OA\Property(property="fields1",type="string"),
     *          @OA\Property(property="fields2",type="boolean"),
     *          @OA\Property(
     *              property="fields3",
     *              type="object",
     *              @OA\Property(property="id", type="string")
     *          ),
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
     * @param '.$requestStoreName.' $'.Str::of($requestStoreName)->camel().'
     * @return JsonResponse
     * @throws \Exception
     */
    public function store('.$requestStoreName.' $'.Str::of($requestStoreName)->camel().'): JsonResponse
    {
        return (new '.$resourceName.'($this->'.$object.'->store()))->response()->setStatusCode(
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'/{id}",
     *   tags={"'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'"},
     *   summary="'.$strName.' Spedified",
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
     * @return '.$resourceName.'
     */
    public function show(string $id): '.$resourceName.'
    {
        return new '.$resourceName.'($this->'.$object.'->getById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'/{id}",
     *   tags={"'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'"},
     *   summary="'.$strName.' Update",
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
     *          @OA\Property(property="fields1",type="string"),
     *          @OA\Property(property="fields2",type="boolean"),
     *          @OA\Property(
     *              property="fields3",
     *              type="object",
     *              @OA\Property(property="id", type="string")
     *          ),
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
     * @param '.$requestUpdateName.' $'.Str::of($requestUpdateName)->camel().'
     * @param string $id
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function update('.$requestUpdateName.' $'.Str::of($requestUpdateName)->camel().', string $id): ?JsonResponse
    {
        return (new '.$resourceName.'($this->'.$object.'->update($id)))->response()->setStatusCode(
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'/{id}",
     *   tags={"'.Str::of($version)->lower().'/'.Str::of($strName)->lower()->plural().'"},
     *   summary="'.$strName.' Delete",
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
        return (new '.$resourceName.'($this->'.$object.'->delete($id)))->response()->setStatusCode(
            Response::HTTP_NO_CONTENT
        );
    }
}';

    }

    /**
     * @param string $name
     * @param string $version
     * @return string
     */
    private function schemaFileApiRest(string $name, string $version): string
    {
        $strName            = Str::of($name)->replace("Controller","");
        $serviceName        = "{$strName}Service";
        $requestStoreName   = "Store{$strName}Request";
        $requestUpdateName  = "Update{$strName}Request";
        $resourceName       = "{$strName}Resource";
        $collectionName     = "{$strName}Collection";
        $object             = Str::of($serviceName)->camel();
        $namespace          = "App\\Http\\Controllers\\API\\{$version}";

        return '<?php namespace '.$namespace.';

use App\Http\Controllers\Controller;
use App\Http\Requests\{'.$requestStoreName.', '.$requestUpdateName.'};
use App\Http\Resources\{'.$collectionName.', '.$resourceName.'};
use Illuminate\Http\{JsonResponse, Response};
use App\Services\{'.$serviceName.'};

/**
 * @property '.$serviceName.' $'.$object.'
 */
class '.$name.' extends Controller
{
    /**
     * '.$name.' Constructor
     *
     * @param '.$serviceName.' $'.$object.'
     */
    public function __construct('.$serviceName.' $'.$object.')
    {
        $this->'.$object.' = $'.$object.';
    }

    /**
     * Display a listing of the resource.
     *
     * @return '.$collectionName.'
     * @throws \Exception
     */
    public function index(): '.$collectionName.'
    {
        return new '.$collectionName.'($this->'.$object.'->getAllQuery());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param '.$requestStoreName.' $'.Str::of($requestStoreName)->camel().'
     * @return JsonResponse
     * @throws \Exception
     */
    public function store('.$requestStoreName.' $'.Str::of($requestStoreName)->camel().'): JsonResponse
    {
        return (new '.$resourceName.'($this->'.$object.'->store()))->response()->setStatusCode(
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return '.$resourceName.'
     */
    public function show(string $id): '.$resourceName.'
    {
        return new '.$resourceName.'($this->'.$object.'->getById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param '.$requestUpdateName.' $'.Str::of($requestUpdateName)->camel().'
     * @param string $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function update('.$requestUpdateName.' $'.Str::of($requestUpdateName)->camel().', string $id): ?JsonResponse
    {
        return (new '.$resourceName.'($this->'.$object.'->update($id)))->response()->setStatusCode(
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse|null
     */
    public function destroy(string $id): ?JsonResponse
    {
        return (new '.$resourceName.'($this->'.$object.'->delete($id)))->response()->setStatusCode(
            Response::HTTP_NO_CONTENT
        );
    }
}';

    }
    /**
     * @param string $name
     * @return void
     */
    private function _makeSchemaRequests(string $name): void
    {
        if (!File::exists("app/Http/Requests"))
            File::makeDirectory("app/Http/Requests",0775,true);

        $nameRequest = Str::of($name)->replace("Controller","");
        if (!File::exists("app/Http/Requests/Store{$nameRequest}Request.php")){
            $schemaStore = $this->_schemaFileRequestStore($nameRequest);
            File::put("app/Http/Requests/Store{$nameRequest}Request.php",$schemaStore);
        }
        if (!File::exists("app/Http/Requests/Update{$nameRequest}Request.php")){
            $schemaUpdate = $this->_schemaFileRequestUpdate($nameRequest);
            File::put("app/Http/Requests/Update{$nameRequest}Request.php",$schemaUpdate);
        }
    }

    /**
     * @param string $name
     * @return void
     */
    private function _makeSchemaResources(string $name): void
    {
        if (!File::exists("app/Http/Resources"))
            File::makeDirectory("app/Http/Resources",0775,true);

        $nameResource = Str::of($name)->replace("Controller","");
        if (!File::exists("app/Http/Resources/{$nameResource}Resource.php")){
            $schemaResource = $this->schemaFileResource($nameResource);
            File::put("app/Http/Resources/{$nameResource}Resource.php",$schemaResource);
        }
        if (!File::exists("app/Http/Resources/{$nameResource}Collection.php")){
            $schemaCollection = $this->schemaFileCollection($nameResource);
            File::put("app/Http/Resources/{$nameResource}Collection.php",$schemaCollection);
        }
    }
    /**
     * @param string $name
     * @return string
     */
    private function schemaFileWebCrud(string $name): string
    {
        $strName            = Str::of($name)->replace("Controller","");
        #$view               = Str::of($strName)->lower();
        $route              = Str::of($strName)->snake()->plural()->replace("_","-");
        $catalogs           = ($this->option("catalog"))? "catalogs.{$route}" : "{$route}";
        $serviceName        = "{$strName}Service";
        $requestStoreName   = "Store{$strName}Request";
        $requestUpdateName  = "Update{$strName}Request";
        $resourceName       = "{$strName}Resource";
        $collectionName     = "{$strName}Collection";
        $object             = Str::of($serviceName)->camel();

        return '<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\{'.$requestStoreName.', '.$requestUpdateName.'};
use App\Http\Resources\{'.$collectionName.', '.$resourceName.'};
use Illuminate\Http\{JsonResponse, Response, RedirectResponse};
use App\Services\{'.$serviceName.'};
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Contracts\Foundation\Application;

/**
 * @property '.$serviceName.' $'.$object.'
 */
class '.$name.' extends Controller
{
    /**
     * '.$name.' Constructor
     *
     * @param '.$serviceName.' $'.$object.'
     */
    public function __construct('.$serviceName.' $'.$object.')
    {
        $this->'.$object.' = $'.$object.';
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View|Application|'.$collectionName.'
     */
    public function index(): Factory|View|Application|'.$collectionName.'
    {
        $response = $this->'.$object.'->getAllQuery();
            if (\request()->ajax())
                return new '.$collectionName.'($response);

        return view("pages.'.$catalogs.'.index",["responses" => $response]);
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view("pages.'.$catalogs.'.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param '.$requestStoreName.' $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store('.$requestStoreName.' $request): RedirectResponse
    {
        $this->'.$object.'->store();

        return \redirect()->route("'.$route.'.index");
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return Response|null
     */
    public function show(string $id): ?Response
    {
        return null;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $id
     * @return Application|Factory|View
     */
    public function edit(string $id): View|Factory|Application
    {
        return view("pages.'.$catalogs.'.edit",[
            "response"  => $this->'.$object.'->getById($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param '.$requestUpdateName.' $request
     * @param string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update('.$requestUpdateName.' $request, string $id): RedirectResponse
    {
        $this->'.$object.'->update($id);

        return \redirect()->route("'.$route.'.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id): RedirectResponse
    {
        $this->'.$object.'->delete($id);

        return \redirect()->route("'.$route.'.index");
    }
}';

    }

    /**
     * @param string $path
     * @return void
     */
    private function schemaFileBladeCrud(string $path): void
    {
        File::put("{$path}/index.blade.php",'');
        File::put("{$path}/create.blade.php",'');
        File::put("{$path}/edit.blade.php",'');
    }

    /**
     * @param string $name
     * @return string
     */
    private function schemaFileResource(string $name): string
    {
        $strName            = Str::of($name)->replace("Controller","");

        return '<?php namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class '.$strName.'Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}';

    }

    /**
     * @param string $name
     * @return string
     */
    private function schemaFileCollection(string $name): string
    {
        $strName            = Str::of($name)->replace("Controller","");

        return '<?php namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class '.$strName.'Collection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
';

    }

    /**
     * @param string $name
     * @return string
     */
    private function _schemaFileRequestStore(string $name): string
    {
        $strName            = Str::of($name)->replace("Controller","");

        return '<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store'.$strName.'Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function attributes(): array
    {
        return [
            //
        ];
    }
}';

    }

    /**
     * @param string $name
     * @return string
     */
    private function _schemaFileRequestUpdate(string $name): string
    {
        $strName            = Str::of($name)->replace("Controller","");

        return '<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update'.$strName.'Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function attributes(): array
    {
        return [
            //
        ];
    }
}';

    }

    /**
     *
     * @param string $name
     * @param string $dirMicro
     * @return string
     */
    private function schemaFileClientService(string $name, string $dirMicro): string
    {
        $strPlural          = Str::of($name)->replace("ClientController","")
            ->plural()
            ->snake()
            ->lower()
            ->slug("-");
        $serviceName        = "{$dirMicro}Service";
        $object             = Str::of($serviceName)->camel();
        $namespace = "App\\Http\Controllers\\API\\{$dirMicro};";

        return '<?php namespace '.$namespace.'

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Response};
use App\Services\{'.$serviceName.'};

/**
 * @property '.$serviceName.' $'.$object.'
 */
class '.$name.' extends Controller
{
    /**
     * @var string
     */
    private string $_url;
    /**
     * @var '.$serviceName.'
     */
    private '.$serviceName.' $_service;

    /**
     * '.$name.' Constructor
     *
     * @param '.$serviceName.' $'.$object.'
     */
    public function __construct('.$serviceName.' $'.$object.')
    {
        $this->_service = $'.$object.';
        $this->_url     = "api/v1/'.$strPlural.'";
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function index(): ?JsonResponse
    {
        return \response()->json($this->_service->showing("{$this->_url}"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(): ?JsonResponse
    {
        return \response()->json($this->_service->store($this->_url),Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function show(string $id): ?JsonResponse
    {
        return \response()->json($this->_service->showing("{$this->_url}/{$id}"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $id
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function update(string $id): ?JsonResponse
    {
        return \response()->json($this->_service->update("{$this->_url}/{$id}"),Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function destroy(string $id): ?JsonResponse
    {
        return \response()->json($this->_service->destroy("{$this->_url}/{$id}"), Response::HTTP_NO_CONTENT);
    }
}';

    }

    /**
     *
     * @param string $name
     * @param string $dirMicro
     * @param string $version
     * @return string
     */
    private function schemaFileClientGenerate(string $name, string $dirMicro, string $version): string
    {
        $strPlural          = Str::of($name)->replace("ClientController","")
            ->plural()
            ->snake()
            ->lower()
            ->slug("-");
        $strCamel           = Str::of($name)->replace("ClientController","")->plural()->snake()->slug("-");
        $serviceName        = "{$dirMicro}Service";
        $object             = Str::of($serviceName)->camel();
        $namespace          = "App\\Http\Controllers\\API\\{$dirMicro};";

        return '<?php namespace '.$namespace.'

use App\Http\Controllers\Controller;
use Illuminate\Http\{JsonResponse, Response};
use App\Services\{'.$serviceName.'};

/**
 * @property '.$serviceName.' $'.$object.'
 */
class '.$name.' extends Controller
{
    /**
     * @var string
     */
    private string $_url;
    /**
     * @var '.$serviceName.'
     */
    private '.$serviceName.' $_service;

    /**
     * '.$name.' Constructor
     *
     * @param '.$serviceName.' $'.$object.'
     */
    public function __construct('.$serviceName.' $'.$object.')
    {
        $this->_service = $'.$object.';
        $this->_url     = "api/'.Str::of($version)->lower().'/'.$strPlural.'";
    }
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *   path="/api/'.Str::of($version)->lower().'/'.$strPlural.'",
     *   tags={"'.Str::of($version)->lower().'/'.$strCamel.'"},
     *   summary="'.$strCamel.' Display",
     *   description="Display a listing of the resource.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\Parameter(
     *      name="status",
     *      in="query",
     *      required=false,
     *      description="This flag is to show the '.$strPlural.' by status",
     *      @OA\Schema(type="boolean")
     *   ),
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
     *   @OA\Response( response=201, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=200, description="Successfully", @OA\JsonContent() ),
     *   @OA\Response( response=422, description="Unprocessable Entity", @OA\JsonContent() ),
     *   @OA\Response( response=401, description="Unauthenticated"),
     *   @OA\Response( response=400, description="Bad Request"),
     *   @OA\Response( response=404, description="Not found"),
     *   @OA\Response( response=403, description="Forbidden")
     * )
     *
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function index(): ?JsonResponse
    {
        return \response()->json($this->_service->showing("{$this->_url}"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *   path="/api/'.Str::of($version)->lower().'/'.$strPlural.'",
     *   tags={"'.Str::of($version)->lower().'/'.$strCamel.'"},
     *   summary="'.$strCamel.' Store",
     *   description="Store a newly created resource in storage.",
     *   security={{"bearer_token":{},"company":{},"user":{},"domain":{} }},
     *
     *  @OA\RequestBody(
     *       @OA\JsonContent(
     *          type="object",
     *          required={"fields1","fields2","fields3"},
     *          @OA\Property(property="fields1",type="string"),
     *          @OA\Property(property="fields2",type="boolean"),
     *          @OA\Property(
     *              property="fields3",
     *              type="object",
     *              @OA\Property(property="id", type="string")
     *          ),
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
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function store(): ?JsonResponse
    {
        return \response()->json($this->_service->store($this->_url),Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *   path="/api/'.Str::of($version)->lower().'/'.$strPlural.'/{id}",
     *   tags={"'.Str::of($version)->lower().'/'.$strCamel.'"},
     *   summary="'.$strCamel.' Specified",
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
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function show(string $id): ?JsonResponse
    {
         return \response()->json($this->_service->showing("{$this->_url}/{$id}"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *   path="/api/'.Str::of($version)->lower().'/'.$strPlural.'/{id}",
     *   tags={"'.Str::of($version)->lower().'/'.$strCamel.'"},
     *   summary="'.$strCamel.' Update",
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
     *          @OA\Property(property="fields1",type="string"),
     *          @OA\Property(property="fields2",type="boolean"),
     *          @OA\Property(
     *              property="fields3",
     *              type="object",
     *              @OA\Property(property="id", type="string")
     *          ),
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
     * @param string $id
     * @return JsonResponse|null
     * @throws \Exception
     */
    public function update(string $id): ?JsonResponse
    {
        return \response()->json($this->_service->update("{$this->_url}/{$id}"),Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *   path="/api/'.Str::of($version)->lower().'/'.$strPlural.'/{id}",
     *   tags={"'.Str::of($version)->lower().'/'.$strCamel.'"},
     *   summary="'.$strCamel.' Delete",
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
     * @throws \Exception
     */
    public function destroy(string $id): ?JsonResponse
    {
       return \response()->json($this->_service->destroy("{$this->_url}/{$id}"), Response::HTTP_NO_CONTENT);
    }
}';

    }

}
