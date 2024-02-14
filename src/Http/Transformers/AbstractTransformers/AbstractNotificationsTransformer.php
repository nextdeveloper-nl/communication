<?php

namespace NextDeveloper\Communication\Http\Transformers\AbstractTransformers;

use NextDeveloper\Communication\Database\Models\Notifications;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class NotificationsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class AbstractNotificationsTransformer extends AbstractTransformer
{

    /**
     * @param Notifications $model
     *
     * @return array
     */
    public function transform(Notifications $model)
    {
                        $notifiableId = \NextDeveloper\\Database\Models\Notifiables::where('id', $model->notifiable_id)->first();
                    $iamUserId = \NextDeveloper\IAM\Database\Models\Users::where('id', $model->iam_user_id)->first();
                    $iamAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->iam_account_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'is_info'  =>  $model->is_info,
            'is_warning'  =>  $model->is_warning,
            'is_error'  =>  $model->is_error,
            'notifiable_id'  =>  $notifiableId ? $notifiableId->uuid : null,
            'notifiable_type'  =>  $model->notifiable_type,
            'data'  =>  $model->data,
            'read_at'  =>  $model->read_at,
            'iam_user_id'  =>  $iamUserId ? $iamUserId->uuid : null,
            'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE




















}
