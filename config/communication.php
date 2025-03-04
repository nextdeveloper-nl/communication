<?php

use NextDeveloper\Communication\Services\Delivery\Twillio;
use NextDeveloper\Communication\Services\CrossPlatformNotification\Mattermost;

return [
    'from'  =>  [
        'name'  =>  'PlusClouds LEO',
        'email' =>  'leo@plusclouds.com',
        'reply_to'  =>  'support@plusclouds.com',
        'reply_to_name' =>  'PlusClouds Support'
    ],
    'defaults'  =>  [
        'mailer'    =>  env('COMMUNICATION_DEFAULT_MAILER', 'uzman_posta'),
        'view'      =>  env('COMMUNICATION_DEFAULT_VIEW', 'Communication::emails.generic'),
        'sms'       =>  env('COMMUNICATION_DEFAULT_SMS', Twillio::class),
        'platforms' =>  [
            Mattermost::class,
        ]
    ],
    'labeling'  =>  [
        'logo'      =>  'https://plusclouds.com.tr/assets/frontend/images/logos/plusclouds-logo.png',
        'unsubscribe'    =>  'You dont want to get these emails ? <a href="https://plusclouds.com.tr/unsubscribe">Unsubscribe</a>'
    ],
    'scopes'    =>  [
        'global' => [
            //  Dont do this here because it makes infinite loop with user object.
            '\NextDeveloper\IAM\Database\Scopes\AuthorizationScope',
            '\NextDeveloper\Commons\Database\GlobalScopes\LimitScope',
        ]
    ],
    'services'   =>  [
        'uzman_posta'   =>  [
            'api_token'     =>  env('UZMAN_POSTA_API_TOKEN'),
            'api_url'       =>  env('UZMAN_POSTA_API_URL'),
            'class'         =>  \NextDeveloper\Communication\Services\EmailDelivery\UzmanPosta::class,
        ],
        'mailgun'   =>  [
            'api_key'   =>  env('MAILGUN_API_KEY'),
            'api_url'   =>  env('MAILGUN_API_URL'),
            'domain'    =>  env('MAILGUN_DOMAIN'),
            'class'     =>  \NextDeveloper\Communication\Services\EmailDelivery\Mailgun::class,
        ],
        'twilio'    =>  [
            'sid'   =>  env('TWILIO_SID'),
            'token' =>  env('TWILIO_TOKEN'),
            'from'  =>  env('TWILIO_FROM'),
            'class'     =>  \NextDeveloper\Communication\Services\SmsDelivery\Twillio::class,
        ],
        'mattermost'    =>  [
            'webhook_url'   =>  env('MATTERMOST_WEBHOOK_URL'),
            'class'         =>  \NextDeveloper\Communication\Services\CrossPlatformNotification\Mattermost::class,
        ],
    ],
];
