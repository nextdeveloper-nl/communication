<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Conversations;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractConversationsTransformer;

/**
 * Class ConversationsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class ConversationsTransformer extends AbstractConversationsTransformer
{

    /**
     * @param Conversations $model
     *
     * @return array
     */
    public function transform(Conversations $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Conversations', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Conversations', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
