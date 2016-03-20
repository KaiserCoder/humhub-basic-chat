<?php

namespace humhub\modules\ponychat\controllers;

use Yii;
use yii\caching\ApcCache;
use yii\helpers\Url;
use yii\helpers\FileHelper;

use humhub\modules\ponychat\Assets;
use humhub\modules\ponychat\libs\PonyCode;

use humhub\components\Controller;
use humhub\components\behaviors\AccessControl;

use humhub\modules\user\components\Session;
use humhub\modules\ponychat\models\UserChatMessage;
use yii\web\View;

class ChatController extends Controller
{

    private static $markups = [
        '[rainbow][/rainbow]',
        '[video][/video]',
        '[img][/img]',
        '[url][/url]',
        '[spoiler][/spoiler]',
        '[b][/b]',
        '[i][/i]',
        '[u][/u]',
        '[pre][/pre]',
        '[color=red][/color]',
        '[mirror][/mirror]'
    ];

    public function behaviors()
    {
        return [
            'acl' => [
                'class' => AccessControl::className()
            ]
        ];
    }

    /**
     * Main view
     */
    public function actionIndex()
    {
        $this->view->registerAssetBundle(Assets::className());

        $this->registerJsVar('chat_ListUsers', Url::to([
            '/ponychat/chat/users'
        ]));

        $this->registerJsVar('chat_Submit', Url::to([
            '/ponychat/chat/submit'
        ]));

        $this->registerJsVar('chat_GetChats', Url::to([
            '/ponychat/chat/chats'
        ]));

        $this->registerJsVar('chat_Ping', Url::to([
            '/ponychat/chat/ping'
        ]));

        $list = null;

        $cache = new ApcCache();
        $cache->useApcu = true;

        if ($cache->exists('list')) {
            $list = $cache->get('list');
        } else {
            $list = '[';

            foreach (self::$markups as $markup) {
                $list .= '"' . $markup . '",';
            }

            $smileys = FileHelper::findFiles(Yii::getAlias('@webroot') . '/img/smiley');

            foreach ($smileys as $smiley) {
                $list .= '":' . str_replace('.png', ':', basename($smiley)) . '",';
            }

            $list .= ']';

            $cache->set('list', $list, 300);
        }

        return $this->render('chatFrame', [
            'list' => $list
        ]);
    }

    private function registerJsVar($name, $value)
    {
        $jsCode = "var " . $name . " = '" . addslashes($value) . "';\n";
        $this->view->registerJs($jsCode, View::POS_HEAD, $name);
    }

    /**
     * Overview of all messages
     */
    public function actionChats()
    {
        $last_id = Yii::$app->request->get('lastID', 0);
        $query = UserChatMessage::find()->where([
            '>',
            'id',
            $last_id
        ]);
        
        $response = [];
        foreach ($query->all() as $entry) {
            $response[] = [
                'id' => $entry->id,
                'message' => $entry->message,
                'author' => [
                    'name' => $entry->user->displayName,
                    'gravatar' => $entry->user->getProfileImage()->getUrl(),
                    'profile' => Url::toRoute([
                        '/',
                        'uguid' => $entry->user->guid
                    ])
                ],
                'time' => [
                    'hours' => Yii::$app->formatter->asTime($entry->created_at, 'php:H'),
                    'minutes' => Yii::$app->formatter->asTime($entry->created_at, 'php:i')
                ]
            ];
        }
        
        Yii::$app->response->format = 'json';

        return $response;
    }

    public function actionSubmit()
    {
        if (($message_text = Yii::$app->request->post('chatText', null)) == null) {
            return;
        }

        $clean = filter_var($message_text, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $chat = new UserChatMessage();
        $chat->message = PonyCode::clean($clean);
        $chat->save();
    }

    public function actionUsers()
    {
        $query = Session::getOnlineUsers();

        $response = [];
        foreach ($query->all() as $user) {
            if ($user->guid)

            $response[] = [
                'name' => $user->displayName,
                'gravatar' => $user->getProfileImage()->getUrl(),
                'profile' => Url::toRoute([
                    '/',
                    'uguid' => $user->guid
                ])
            ];
        }
        
        Yii::$app->response->format = 'json';

        return [
            'online' => count($response) . ' personnes connectée(s)',
            'users' => $response
        ];
    }

}
