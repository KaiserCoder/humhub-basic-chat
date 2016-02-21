<?php

namespace humhub\modules\ponychat\models;

use Yii;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;

class UserChatMessage extends ActiveRecord
{

    public static function tableName()
    {
        return 'user_chat_message';
    }

    public function rules()
    {
        return [
            [
                [
                    'message'
                ],
                'required'
            ],
            [
                [
                    'message'
                ],
                'safe'
            ]
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), [
            'id' => 'created_by'
        ]);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'message' => 'message',
            'created_at' => 'created_at',
            'created_by' => 'created_by'
        ];
    }
}
