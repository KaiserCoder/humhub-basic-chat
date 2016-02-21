<?php
namespace humhub\modules\ponychat;

use Yii;
use yii\helpers\Url;
use yii\base\Object;

use humhub\models\Setting;
use humhub\modules\ponychat\models\UserChatMessage;

class Events extends Object
{

    public static function onTopMenuInit($event)
    {
        $event->sender->addItem([
            'label' => 'Chat',
            'sortOrder' => 500,
            'url' => Url::to(['/ponychat/chat']),
            'icon' => '<i class="fa fa-weixin"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'ponychat' && Yii::$app->controller->id == 'view')
        ]);
    }

    public static function addChatFrame($event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $event->sender->view->registerAssetBundle(Assets::className());

        $event->sender->view->registerjsVar('chat_ListUsers', Url::to([
            '/ponychat/chat/users'
        ]));

        $event->sender->view->registerjsVar('chat_Submit', Url::to([
            '/ponychat/chat/submit'
        ]));

        $event->sender->view->registerjsVar('chat_GetChats', Url::to([
            '/ponychat/chat/chats'
        ]));
    }

    public static function onDailyCron($event)
    {
        $controller = $event->sender;
        $controller->stdout("Deleting old chat_messages... ");
        
        $timeout = Setting::Get('timeout', 'ponychat');

        if (!$timeout || $timeout == null || $timeout <= 0) {
            $controller->stdout('skipped! no timeout set.' . PHP_EOL, \yii\helpers\Console::FG_YELLOW);
            return;
        }

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
            'sortOrder' => 400,
            'group' => 'manage',
            'label' => 'PonyChat',
            'icon' => '<i class="fa fa-weixin"></i>',
            'url' => Url::toRoute('/ponychat/admin/index'),
            'isActive' => Yii::$app->controller->module && Yii::$app->controller->module->id == 'ponychat' && Yii::$app->controller->id == 'admin'
        ]);
    }
}
