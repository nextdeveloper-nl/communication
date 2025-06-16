<?php

namespace NextDeveloper\Communication\Services\AbstractServices;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use NextDeveloper\Commons\Database\Models\AvailableActions;
use NextDeveloper\Commons\Exceptions\ModelNotFoundException;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Communication\Database\Filters\BotsQueryFilter;
use NextDeveloper\Communication\Database\Models\Bots;
use NextDeveloper\Events\Services\Events;

/**
 * This class is responsible from managing the data for Bots
 *
 * Class BotsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class AbstractBotsService
{
    public static function get(BotsQueryFilter $filter = null, array $params = []) : Collection|LengthAwarePaginator
    {
        $enablePaginate = array_key_exists('paginate', $params);

        $request = new Request();

        /**
        * Here we are adding null request since if filter is null, this means that this function is called from
        * non http application. This is actually not I think its a correct way to handle this problem but it's a workaround.
        *
        * Please let me know if you have any other idea about this; baris.bulut@nextdeveloper.com
        */
        if($filter == null) {
            $filter = new BotsQueryFilter($request);
        }

        $perPage = config('commons.pagination.per_page');

        if($perPage == null) {
            $perPage = 20;
        }

        if(array_key_exists('per_page', $params)) {
            $perPage = intval($params['per_page']);

            if($perPage == 0) {
                $perPage = 20;
            }
        }

        if(array_key_exists('orderBy', $params)) {
            $filter->orderBy($params['orderBy']);
        }

        $model = Bots::filter($filter);

        if($enablePaginate) {
            //  We are using this because we have been experiencing huge security problem when we use the paginate method.
            //  The reason was, when the pagination method was using, somehow paginate was discarding all the filters.
            $modelCount = $model->count();
            $page = array_key_exists('page', $params) ? $params['page'] : 1;
            $items = $model->skip(($page - 1) * $perPage)->take($perPage)->get();

            return new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $modelCount,
                $perPage,
                $page
            );
        }

        return $model->get();
    }

    public static function getAll()
    {
        return Bots::all();
    }

    /**
     * This method returns the model by looking at reference id
     *
     * @param  $ref
     * @return mixed
     */
    public static function getByRef($ref) : ?Bots
    {
        return Bots::findByRef($ref);
    }

    public static function getActions()
    {
        $model = Bots::class;

        $model = Str::remove('Database\\Models\\', $model);

        $actions = AvailableActions::where('input', $model)
            ->get();

        return $actions;
    }

    /**
     * This method initiates the related action with the given parameters.
     */
    public static function doAction($objectId, $action, ...$params)
    {
        $object = Bots::where('uuid', $objectId)->first();

        $action = AvailableActions::where('name', $action)->first();
        $class = $action->class;

        if(class_exists($class)) {
            $action = new $class($object, $params);
            dispatch($action);

            return $action->getActionId();
        }

        return null;
    }

    /**
     * This method returns the model by lookint at its id
     *
     * @param  $id
     * @return Bots|null
     */
    public static function getById($id) : ?Bots
    {
        return Bots::where('id', $id)->first();
    }

    /**
     * This method returns the sub objects of the related models
     *
     * @param  $uuid
     * @param  $object
     * @return void
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public static function relatedObjects($uuid, $object)
    {
        try {
            $obj = Bots::where('uuid', $uuid)->first();

            if(!$obj) {
                throw new ModelNotFoundException('Cannot find the related model');
            }

            if($obj) {
                return $obj->$object;
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * This method created the model from an array.
     *
     * Throws an exception if stuck with any problem.
     *
     * @param  array $data
     * @return mixed
     * @throw  Exception
     */
    public static function create(array $data)
    {

        try {
            $model = Bots::create($data);
        } catch(\Exception $e) {
            throw $e;
        }

        Events::fire('created:NextDeveloper\Communication\Bots', $model);

        return $model->fresh();
    }

    /**
     * This function expects the ID inside the object.
     *
     * @param  array $data
     * @return Bots
     */
    public static function updateRaw(array $data) : ?Bots
    {
        if(array_key_exists('id', $data)) {
            return self::update($data['id'], $data);
        }

        return null;
    }

    /**
     * This method updated the model from an array.
     *
     * Throws an exception if stuck with any problem.
     *
     * @param
     * @param  array $data
     * @return mixed
     * @throw  Exception
     */
    public static function update($id, array $data)
    {
        $model = Bots::where('uuid', $id)->first();

        if(!$model) {
            throw new NotAllowedException(
                'We cannot find the related object to update. ' .
                'Maybe you dont have the permission to update this object?'
            );
        }


        Events::fire('updating:NextDeveloper\Communication\Bots', $model);

        try {
            $isUpdated = $model->update($data);
            $model = $model->fresh();
        } catch(\Exception $e) {
            throw $e;
        }

        Events::fire('updated:NextDeveloper\Communication\Bots', $model);

        return $model->fresh();
    }

    /**
     * This method updated the model from an array.
     *
     * Throws an exception if stuck with any problem.
     *
     * @param
     * @param  array $data
     * @return mixed
     * @throw  Exception
     */
    public static function delete($id)
    {
        $model = Bots::where('uuid', $id)->first();

        if(!$model) {
            throw new NotAllowedException(
                'We cannot find the related object to delete. ' .
                'Maybe you dont have the permission to update this object?'
            );
        }

        Events::fire('deleted:NextDeveloper\Communication\Bots', $model);

        try {
            $model = $model->delete();
        } catch(\Exception $e) {
            throw $e;
        }

        return $model;
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
