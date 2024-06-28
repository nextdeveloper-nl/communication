<?php

namespace NextDeveloper\Communication\Services;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use NextDeveloper\Commons\Database\Models\Validatables;
use NextDeveloper\Communication\Database\Models\Conversations;
use NextDeveloper\Communication\Database\Models\Users;
use NextDeveloper\Communication\Services\AbstractServices\AbstractConversationsService;
use NextDeveloper\Communication\Services\Bots\TelegramBot;
use NextDeveloper\I18n\Helpers\i18n;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

/**
 * This class manages the data for Conversations.
 *
 * Class ConversationsService.
 *
 * @package NextDeveloper\Communication\Database\Models
 */
class ConversationsService extends AbstractConversationsService
{

}
