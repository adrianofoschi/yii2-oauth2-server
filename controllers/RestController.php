<?php

namespace filsh\yii2\oauth2server\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;
use filsh\yii2\oauth2server\models\OauthAccessTokens;

class RestController extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'exceptionFilter' => [
                'class' => ErrorToExceptionFilter::className()
            ],
        ]);
    }

    public function actionToken()
    {
        /** @var $response \OAuth2\Response */
        $response = $this->module->getServer()->handleTokenRequest();
        $params = $response->getParameters();
        
        //add user_id to response
        if (isset($params['access_token'])) {
            $accessToken = OauthAccessTokens::findOne($params['access_token']);
            if ($accessToken) {
                $params['user_id'] = $accessToken->user_id;
            }
        }

        return $params;
    }
    
    public function actionRevoke()
    {
        /** @var $response \OAuth2\Response */
        $response = $this->module->getServer()->handleRevokeRequest();
        return $response->getParameters();
    }

    public function actionUserInfo()
    {
        $response = $this->module->getServer()->handleUserInfoRequest();
        return $response->getParameters();
    }
}
