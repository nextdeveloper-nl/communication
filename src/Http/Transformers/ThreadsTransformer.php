<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Threads;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractThreadsTransformer;

/**
 * Class ThreadsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class ThreadsTransformer extends AbstractThreadsTransformer
{

    /**
     * @param Threads $model
     *
     * @return array
     */
    public function transform(Threads $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Threads', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Threads', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
