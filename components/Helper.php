<?php

namespace app\components;


use Yii;
use yii\base\Component;
use yii\httpclient\XmlParser;
use \Firebase\JWT\JWT;
use app\models\User;
use app\models\BankAccountLink;

 
class Helper extends Component
{
    public function recordLoan($title, $amount, $plan, $member_id, $partner_id, $created_by)
    {
        $client = Yii::$app->webService;
        $response = $client->NewLoan([
            'title' => $title, 
            'amount' => $amount, 
            'plan' => $plan, 
            'member_id' => $member_id, 
            'partner_id' => $partner_id, 
            'created_by' => $created_by
        ]);
         //var_dump($response); die();
        $responseString = $response->NewLoanResult;

        $xmlresponse =  simplexml_load_string($responseString);

        $result = (array)$xmlresponse;
        return $result;
    }
}