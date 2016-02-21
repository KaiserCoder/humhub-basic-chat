<?php

namespace humhub\modules\humhubchat;

use humhub\widgets\TopMenu;
use humhub\modules\admin\widgets\AdminMenu;

return [
    'id' => 'humhub-chat',
    'class' => 'humhub\modules\humhubchat\Module',
    'namespace' => 'humhub\modules\humhubchat',
    'events' => [
        [
            'class' => AdminMenu::className(),
            'event' => AdminMenu::EVENT_INIT,
            'callback' => [
                'humhub\modules\humhubchat\Events',
                'onAdminMenuInit'
            ]
        ],
        [
            'class' => TopMenu::className(),
            'event' => TopMenu::EVENT_INIT,
            'callback' => [
                'humhub\modules\humhubchat\Events',
                'addChatFrame'
            ]
        ],
        [
            'class' => TopMenu::className(),
            'event' => TopMenu::EVENT_INIT,
            'callback' => [
                'humhub\modules\humhubchat\Events',
                'onTopMenuInit'
            ]
        ]
    ]
];
