<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Remindables;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractRemindablesTransformer;

/**
 * Class RemindablesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class RemindablesTransformer extends AbstractRemindablesTransformer
{

    /**
     * @param Remindables $model
     *
     * @return array
     */
    public function transform(Remindables $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Remindables', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        // object_id is stored as the record's internal integer id; the API exposes ids as
        // uuids, so swap it for the record's uuid (null if the record no longer exists).
        $transformed['object_id'] = $this->objectUuid($model);

        Cache::set(
            CacheHelper::getKey('Remindables', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }

    /**
     * Resolves the uuid of the record the reminder is about.
     */
    private function objectUuid(Remindables $model): ?string
    {
        $class = $model->object_type ? '\\'.ltrim($model->object_type, '\\') : null;

        if (! $class || ! class_exists($class) || ! $model->object_id) {
            return null;
        }

        return $class::withoutGlobalScopes()->where('id', $model->object_id)->value('uuid');
    }
}
