<?php

namespace humhub\modules\ponychat\controllers;

use Yii;
use humhub\models\Setting;
use humhub\components\behaviors\AccessControl;
use humhub\modules\admin\components\Controller;
use humhub\modules\ponychat\forms\SettingsForm;

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
                Setting::Set('banned', $form->banned, 'ponychat');
                
                Yii::$app->session->setFlash('data-saved', 'Sauvegardé');
            }
        } else {
            $form->banned = Setting::Get('banned', 'ponychat');
        }
        
        return $this->render('index', [
            'model' => $form
        ]);
    }

}
