<?php

namespace NextDeveloper\Communication\Http\Controllers\Remindables;

use Illuminate\Http\Request;
use NextDeveloper\Communication\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Communication\Http\Requests\Remindables\RemindablesUpdateRequest;
use NextDeveloper\Communication\Database\Filters\RemindablesQueryFilter;
use NextDeveloper\Communication\Database\Models\Remindables;
use NextDeveloper\Communication\Services\RemindablesService;
use NextDeveloper\Communication\Http\Requests\Remindables\RemindablesCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags;use NextDeveloper\Commons\Http\Traits\Addresses;
class RemindablesController extends AbstractController
{
    private $model = Remindables::class;

    use Tags;
    use Addresses;
    /**
     * This method returns the list of remindables.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  RemindablesQueryFilter $filter  An object that builds search query
     * @param  Request                $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(RemindablesQueryFilter $filter, Request $request)
    {
        $data = RemindablesService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $remindablesId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = RemindablesService::getByRef($ref);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method returns the list of sub objects the related object. Sub object means an object which is preowned by
     * this object.
     *
     * It can be tags, addresses, states etc.
     *
     * @param  $ref
     * @param  $subObject
     * @return void
     */
    public function relatedObjects($ref, $subObject)
    {
        $objects = RemindablesService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created Remindables object on database.
     *
     * @param  RemindablesCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(RemindablesCreateRequest $request)
    {
        $model = RemindablesService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates Remindables object on database.
     *
     * @param  $remindablesId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($remindablesId, RemindablesUpdateRequest $request)
    {
        $model = RemindablesService::update($remindablesId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates Remindables object on database.
     *
     * @param  $remindablesId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($remindablesId)
    {
        $model = RemindablesService::delete($remindablesId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
