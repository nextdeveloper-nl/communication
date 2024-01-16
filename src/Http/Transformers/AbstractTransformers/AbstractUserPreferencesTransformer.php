<?php

namespace NextDeveloper\Communication\Http\Transformers\AbstractTransformers;

use NextDeveloper\Communication\Database\Models\UserPreferences;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class UserPreferencesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class AbstractUserPreferencesTransformer extends AbstractTransformer
{

    /**
     * @param UserPreferences $model
     *
     * @return array
     */
    public function transform(UserPreferences $model)
    {
                        $iamUserId = \NextDeveloper\IAM\Database\Models\Users::where('id', $model->iam_user_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'is_system_email_optout'  =>  $model->is_system_email_optout == 1 ? true : false,
            'is_phone_optout'  =>  $model->is_phone_optout == 1 ? true : false,
            'is_marketing_email_optout'  =>  $model->is_marketing_email_optout == 1 ? true : false,
            'iam_user_id'  =>  $iamUserId ? $iamUserId->uuid : null,
            'created_at'  =>  $model->created_at ? $model->created_at->toIso8601String() : null,
            'updated_at'  =>  $model->updated_at ? $model->updated_at->toIso8601String() : null,
            'deleted_at'  =>  $model->deleted_at ? $model->deleted_at->toIso8601String() : null,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
