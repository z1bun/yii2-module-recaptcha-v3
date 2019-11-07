<?php

namespace z1bun\recaptcha3;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

/**
 * Class ReCaptcha
 * @package z1bun\recaptcha3
 */
class ReCaptcha extends Component
{
    /**
     * @var string Recaptcha site key
     * @see https://developers.google.com/recaptcha/docs/v3
     */
    public $siteKey;

    /**
     * @var string Recaptcha secret key
     * @see https://developers.google.com/recaptcha/docs/verify
     */
    public $secretKey;

    /**
     * @var string Recaptcha verify action url
     * @see https://developers.google.com/recaptcha/docs/verify
     */
    private $verifyEndpoint = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->siteKey)) {
            throw new \Exception('site key cant be null');
        }

        if (empty($this->secretKey)) {
            throw new \Exception('secret key cant be null');
        }
    }

    /**
     * @param $view
     * @throws \yii\base\InvalidConfigException
     */
    public function registerScript($view)
    {
        /** @var yii\web\View $view */
        $view->registerJsFile('https://www.google.com/recaptcha/api.js?render=' . $this->siteKey, [
            'position' => $view::POS_HEAD,
        ], 'recaptcha-v3-script');
    }


    /**
     * @param $value
     *
     * @return bool|int
     */
    public function validateValue($value)
    {
        if (YII_ENV_TEST) {
            return 1;
        }

        try {
            $response = $this->curl([
                'secret'   => $this->secretKey,
                'response' => $value,
                'remoteip' => Yii::$app->has('request') ? Yii::$app->request->userIP : null,
            ]);
            if (!empty($response) && $response['success']) {
                return $response['score'];
            }
        } catch (\Exception $e) {

        }

        return false;
    }

    /**
     * Sends User post data with curl to google and receives 'success' if valid
     *
     * @param array $params
     *
     * @return mixed
     */
    protected function curl(array $params)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->verifyEndpoint);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $curlData = curl_exec($curl);
        curl_close($curl);

        return Json::decode($curlData, true);
    }

}