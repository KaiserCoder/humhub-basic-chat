<?php
namespace humhub\modules\ponychat\forms;

use Yii;
use yii\base\Model;

class SettingsForm extends Model
{
    public $banned;

    public function rules()
    {
        return [
            [
                [
                    'banned'
                ],
                'safe'
            ],
            [
                [
                    'banned'
                ],
                'string',
                'max' => 255
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'banned' => 'Utilisateurs bannis',
        ];
    }
}
