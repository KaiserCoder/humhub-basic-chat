<?php

namespace humhub\modules\humhubchat\models;

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
            'id' => Yii::t('Humhub-chatModule.base', 'ID'),
            'message' => Yii::t('Humhub-chatModule.base', 'message'),
            'created_at' => Yii::t('Humhub-chatModule.base', 'created'),
            'created_by' => Yii::t('Humhub-chatModule.base', 'author')
        ];
    }
}
