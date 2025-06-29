<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Emails;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractEmailsTransformer;

/**
 * Class EmailsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class EmailsTransformer extends AbstractEmailsTransformer
{

    /**
     * @param Emails $model
     *
     * @return array
     */
    public function transform(Emails $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Emails', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Emails', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
