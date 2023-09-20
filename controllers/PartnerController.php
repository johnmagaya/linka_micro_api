<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\base\Event;
use yii\web\Response;
use yii\web\HttpException;


class PartnerController extends Controller
{
  public function actionIndex()
  {
    return $msg = "Welcome";
  }
  public function actionNewLoan()
  {
    $title = Yii::$app->request->post('title');
    if (empty($title)) throw new HttpException(255, 'Loan title is required', 01);
    $member_id = Yii::$app->request->post('member_id');
    if (empty($member_id)) throw new HttpException(255, 'member_id is required', 02);
    $amount = Yii::$app->request->post('amount');
    if (empty($amount)) throw new HttpException(255, 'Loan amount is required', 03);
    $plan = Yii::$app->request->post('plan');
    if (empty($plan)) throw new HttpException(255, 'payment plan is required', 04);
    $created_by = Yii::$app->request->post('created_by');
    if (empty($created_by)) throw new HttpException(255, 'Creator is required', 05);
    $partner_id = Yii::$app->request->post('partner_id');
    if (empty($partner_id)) throw new HttpException(255, 'partner_id is required', 06);

    //sending data to web service for recording new loan
    $record_loan = Yii::$app->helper->recordLoan($title, $amount, $plan, $member_id, $partner_id, $created_by);
    
    if($record_loan['code']== "100") {
      
      $response = [
        'success' => true,
        'response_code' => $record_loan['code'],
        'message' => $record_loan['message'],
      ];
    }
    else{
      $response = [
        'success' => false,
        'response_code' => $record_loan['code'],
        'message' => $record_loan['message'],
      ];
    }
    return $response;
  }
}