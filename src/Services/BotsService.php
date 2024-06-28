<?php

namespace NextDeveloper\Communication\Services;

use NextDeveloper\Commons\Services\ValidatablesService;
use NextDeveloper\Communication\Database\Models\Bots;
use NextDeveloper\Communication\Services\AbstractServices\AbstractBotsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is responsible from managing the data for Bots
 *
 * Class BotsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class BotsService extends AbstractBotsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    public static function generateCode()
    {

        $code = rand(100000, 999999);

        $data = [
            'validation_code'   => $code,
            'object_id'         => UserHelper::me()->id,
            'object_type'       => UserHelper::me()->getMorphClass()
        ];

        return ValidatablesService::create($data);
    }

    public static function telegramWebhook($token)
    {
        $model = Bots::withoutGlobalScope(AuthorizationScope::class)
            ->where('token', $token)
            ->first();

        if (!$model) {
            abort_if(true, 404, 'Bot not found');
        }

        $botClass = $model->class;

        if (!class_exists($botClass)) {
            abort_if(true, 404, 'Bot class not found');
        }

        $botClass::getInstance($model->token);
    }
}
