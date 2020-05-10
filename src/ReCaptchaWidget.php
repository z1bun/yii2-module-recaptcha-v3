<?php

namespace z1bun\recaptcha3;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\Html;
use yii\web\AssetBundle;
use yii\web\View;
use yii\widgets\InputWidget;
use z1bun\recaptcha3\assets\RecaptchaAsset;

/**
 * Class ReCaptchaWidget
 * @package kekaadrenalin\recaptcha3
 */
class ReCaptchaWidget extends InputWidget
{
    /**
     * Recaptcha component
     * @var string|array|ReCaptcha
     */
    public $component = 'reCaptcha3';

    /** @var string Recaptcha input class */
    public $inputClass = 'jsCpt';

    /** @var bool Check if field id must be empty */
    protected $nullFieldId = false;

    /**
     * @var string
     */
    public $actionName = 'contact';

    /**
     * @var \z1bun\recaptcha3\ReCaptcha
     */
    private $_component = null;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        /** FIX for id=null options */
        if (array_key_exists('id', $this->options) && null === $this->options['id']) {
            $this->nullFieldId = true;
        }

        parent::init();
        $component = Instance::ensure($this->component, ReCaptcha::class);
        if (null === $component) {
            throw new InvalidConfigException('component is required.');
        }
        $this->_component = $component;

        if ($this->nullFieldId) {
            $this->options['id'] = null;
        }
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $this->registerAssets();
        $this->field->template = "{input}\n{error}";

        if (isset($this->options['class']) && !empty($this->options['class'])) {
            $this->options['class'] .= ' ' . $this->inputClass;
        }

        return Html::activeHiddenInput(
            $this->model,
            $this->attribute,
            array_merge(
                $this->options,
                [
                    'value' => '',
                    'data-cpt-action' => $this->actionName,
                    'data-cpt-key' => $this->_component->siteKey,
                ]
            )
        );
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    protected function registerAssets(): void
    {
        $this->_component->registerScript($this->view);
        RecaptchaAsset::register($this->view);

        $formId = $this->field->form->id;
        $inputId = Html::getInputId($this->model, $this->attribute);

        $jsCode = <<<JS
            $('#{$formId}').on('beforeSubmit', function () {
                if (!$('#{$inputId}').val()) {
                    grecaptcha.ready(function () {
                        grecaptcha.execute('{$this->_component->siteKey}', {action: '{$this->actionName}'}).then(function (token) {
                            $('#{$inputId}').val(token);
                            $('#{$formId}').submit();
                        });
                    });
                    return false;
                } 
                
                return true;
            });
JS;

        $this->view->registerJs($jsCode, View::POS_READY);
    }

}