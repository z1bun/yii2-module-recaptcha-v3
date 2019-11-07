<?php

namespace z1bun\recaptcha3\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class RecaptchaAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/z1bun/yii2-module-recaptcha-v3/assets';

    /**
     * @var array
     */
    public $js = [
        'js/recaptcha.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        YiiAsset::class,
    ];

}
