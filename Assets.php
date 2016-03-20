<?php

namespace humhub\modules\ponychat;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{

    public $css = [
        'chat.css',
        'awesomplete.css'
    ];

    public $js = [
        'awesomplete.min.js',
        'script.js',
        'ponycode.js',
        'spoiler.js'
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . '/assets';
        parent::init();
    }
}
