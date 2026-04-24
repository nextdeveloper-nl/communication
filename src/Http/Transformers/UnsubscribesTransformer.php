<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Unsubscribes;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractUnsubscribesTransformer;

/**
 * Class UnsubscribesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class UnsubscribesTransformer extends AbstractUnsubscribesTransformer
{

    /**
     * @param Unsubscribes $model
     *
     * @return array
     */
    public function transform(Unsubscribes $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Unsubscribes', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Unsubscribes', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
