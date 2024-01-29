<?php

use NextDeveloper\Communication\Services\Delivery\Mail;

return [
    'from'  =>  [
        'name'  =>  'PlusClouds LEO',
        'email' =>  'leo@plusclouds.com',
        'reply_to'  =>  'support@plusclouds.com',
        'reply_to_name' =>  'PlusClouds Support'
    ],
    'defaults'  =>  [
        'mailer'    =>  env('COMMUNICATION_DEFAULT_MAILER', Mail::class),
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
];
