<?php
namespace humhub\modules\humhubchat;

use Yii;
use yii\helpers\Url;

use humhub\models\Setting;
use humhub\modules\humhubchat\Assets;
use humhub\modules\humhubchat\models\UserChatMessage;

class Events extends \yii\base\Object
{

    public static function onTopMenuInit($event)
    {
        $event->sender->addItem([
            'label' => 'Chat',
            'sortOrder' => 500,
            'url' => Url::to(['/humhub-chat/chat']),
            'icon' => '<i class="fa fa-weixin"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'humhub-chat' && Yii::$app->controller->id == 'view')
        ]);
    }

    public static function addChatFrame($event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $event->sender->view->registerAssetBundle(Assets::className());

        $event->sender->view->registerjsVar('chat_ListUsers', Url::to([
            '/humhub-chat/chat/users'
        ]));

        $event->sender->view->registerjsVar('chat_Submit', Url::to([
            '/humhub-chat/chat/submit'
        ]));

        $event->sender->view->registerjsVar('chat_GetChats', Url::to([
            '/humhub-chat/chat/chats'
        ]));
    }

    public static function onDailyCron($event)
    {
        $controller = $event->sender;
        $controller->stdout("Deleting old chat_messages... ");
        
        $timeout = Setting::Get('timeout', 'humhubchat');

        if (!$timeout || $timeout == null || $timeout <= 0) {
            $controller->stdout('skipped! no timeout set.' . PHP_EOL, \yii\helpers\Console::FG_YELLOW);
            return;
        }
        
        // delete old chats
        UserChatMessage::deleteAll([
            '<',
            'created_at',
            Yii::$app->formatter->asDatetime(strtotime("- $timeout day"), 'php:Y-m-d H:i:s')
        ]);
        
        $controller->stdout('done.' . PHP_EOL, \yii\helpers\Console::FG_GREEN);
    }

    public static function onAdminMenuInit(\yii\base\Event $event)
    {
        $event->sender->addItem([
            'sortOrder' => 650,
            'group' => 'manage',
            'label' => 'Pony Chat',
            'icon' => '<i class="fa fa-weixin"></i>',
            'url' => Url::toRoute('/humhub-chat/admin/index'),
            'isActive' => Yii::$app->controller->module && Yii::$app->controller->module->id == 'humhub-chat' && Yii::$app->controller->id == 'admin'
        ]);
    }
}
