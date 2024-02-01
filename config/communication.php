<?php

use NextDeveloper\Communication\Services\Delivery\UzmanPosta;

return [
    'from'  =>  [
        'name'  =>  'PlusClouds LEO',
        'email' =>  'leo@plusclouds.com',
        'reply_to'  =>  'support@plusclouds.com',
        'reply_to_name' =>  'PlusClouds Support'
    ],
    'defaults'  =>  [
        'mailer'    =>  env('COMMUNICATION_DEFAULT_MAILER', UzmanPosta::class),
        'view'      =>  env('COMMUNICATION_DEFAULT_VIEW', 'Communication::emails.generic'),
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
            'api_test_url'  =>  env('UZMAN_POSTA_API_TEST_URL'),
        ],
    ],
];
