<?php

namespace humhub\modules\ponychat;

use humhub\widgets\TopMenu;
use humhub\commands\CronController;
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
                'onTopMenuInit'
            ]
        ],
        [
            'class' => CronController::className(),
            'event' => CronController::EVENT_ON_DAILY_RUN,
            'callback' => [
                'humhub\modules\ponychat\Events',
                'onDailyCron'
            ]
        ]
    ]
];
