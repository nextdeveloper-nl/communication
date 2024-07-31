<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\BotCodes;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractBotCodesTransformer;

/**
 * Class BotCodesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class BotCodesTransformer extends AbstractBotCodesTransformer
{

    /**
     * @param BotCodes $model
     *
     * @return array
     */
    public function transform(BotCodes $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('BotCodes', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('BotCodes', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
