<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\ThreadAssignments;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractThreadAssignmentsTransformer;

/**
 * Class ThreadAssignmentsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class ThreadAssignmentsTransformer extends AbstractThreadAssignmentsTransformer
{

    /**
     * @param ThreadAssignments $model
     *
     * @return array
     */
    public function transform(ThreadAssignments $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('ThreadAssignments', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('ThreadAssignments', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
