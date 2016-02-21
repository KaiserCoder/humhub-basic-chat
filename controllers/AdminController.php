<?php

namespace humhub\modules\humhubchat\controllers;

use Yii;
use humhub\models\Setting;
use humhub\components\behaviors\AccessControl;
use humhub\modules\admin\components\Controller;
use humhub\modules\humhubchat\forms\SettingsForm;

class AdminController extends Controller
{

    public function behaviors()
    {
        return [
            'acl' => [
                'class' => AccessControl::className(),
                'adminOnly' => true
            ]
        ];
    }

    public function actionIndex()
    {
        $form = new SettingsForm();
        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                Setting::Set('theme', $form->theme, 'humhubchat');
                Setting::Set('timeout', $form->timeout, 'humhubchat');
                
                Yii::$app->session->setFlash('data-saved', 'Sauvegardé');
            }
        } else {
            $form->theme = Setting::Get('theme', 'humhubchat');
            $form->timeout = Setting::Get('timeout', 'humhubchat');
        }
        
        return $this->render('index', [
            'model' => $form
        ]);
    }

    public static function getThemes()
    {
        return [
            'chat_bright.css' => 'Light theme',
            'chat_dark.css' => 'Dark theme'
        ];
    }
}
