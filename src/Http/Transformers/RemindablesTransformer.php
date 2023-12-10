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

        Cache::set(
            CacheHelper::getKey('Remindables', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
