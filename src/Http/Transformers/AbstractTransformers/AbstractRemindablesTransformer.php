<?php

namespace NextDeveloper\Communication\Http\Transformers\AbstractTransformers;

use NextDeveloper\Communication\Database\Models\Remindables;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class RemindablesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class AbstractRemindablesTransformer extends AbstractTransformer
{

    /**
     * @param Remindables $model
     *
     * @return array
     */
    public function transform(Remindables $model)
    {
                        $remindableId = \NextDeveloper\\Database\Models\Remindables::where('id', $model->remindable_id)->first();
                    $iamUserId = \NextDeveloper\IAM\Database\Models\Users::where('id', $model->iam_user_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'remindable_id'  =>  $remindableId ? $remindableId->uuid : null,
            'remindable_object_type'  =>  $model->remindable_object_type,
            'remind_datetime'  =>  $model->remind_datetime,
            'snooze_datetime'  =>  $model->snooze_datetime,
            'iam_user_id'  =>  $iamUserId ? $iamUserId->uuid : null,
            'note'  =>  $model->note,
            'is_reminded'  =>  $model->is_reminded,
            'is_acknowledged'  =>  $model->is_acknowledged,
            'is_cancelled'  =>  $model->is_cancelled,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE






















}
