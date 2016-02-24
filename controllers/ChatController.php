<?php

namespace humhub\modules\ponychat\controllers;

use humhub\modules\user\components\User;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use humhub\modules\ponychat\parser\PonyCode;

use humhub\components\Controller;
use humhub\components\behaviors\AccessControl;

use humhub\modules\user\components\Session;
use humhub\modules\ponychat\models\UserChatMessage;

class ChatController extends Controller
{

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
        return $this->render('chatFrame', ['smileys' => FileHelper::findFiles(Yii::getAlias('@webroot') . '/img/smiley')]);
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
