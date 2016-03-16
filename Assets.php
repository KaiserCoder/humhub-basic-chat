<?php

namespace humhub\modules\ponychat;

use yii\web\AssetBundle;
use humhub\models\Setting;

class Assets extends AssetBundle
{

    public $css = [
        'chat_bright.css'
    ];

    public $js = [
        'script.js',
        'ponycode.js',
        'spoiler.js'
    ];

    public function init()
    {
        if (\Yii::$app->controller->id === 'chat') {
            $theme = Setting::Get('theme', 'ponychat');

            if ($theme) $this->css = [$theme];

            $this->sourcePath = dirname(__FILE__) . '/assets';
            parent::init();
        }
    }
}
