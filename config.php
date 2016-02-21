<?php

namespace humhub\modules\ponychat;

use humhub\widgets\TopMenu;
use humhub\modules\admin\widgets\AdminMenu;

return [
    'id' => 'ponychat',
    'class' => 'humhub\modules\ponychat\Module',
    'namespace' => 'humhub\modules\ponychat',
    'events' => [
        [
            'class' => AdminMenu::className(),
            'event' => AdminMenu::EVENT_INIT,
            'callback' => [
                'humhub\modules\ponychat\Events',
                'onAdminMenuInit'
            ]
        ],
        [
            'class' => TopMenu::className(),
            'event' => TopMenu::EVENT_INIT,
            'callback' => [
                'humhub\modules\ponychat\Events',
                'addChatFrame'
            ]
        ],
        [
            'class' => TopMenu::className(),
            'event' => TopMenu::EVENT_INIT,
            'callback' => [
                'humhub\modules\ponychat\Events',
                'onTopMenuInit'
            ]
        ]
    ]
];
