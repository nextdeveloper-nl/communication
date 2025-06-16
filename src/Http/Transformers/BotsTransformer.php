<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Bots;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractBotsTransformer;

/**
 * Class BotsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class BotsTransformer extends AbstractBotsTransformer
{

    /**
     * @param Bots $model
     *
     * @return array
     */
    public function transform(Bots $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Bots', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Bots', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
