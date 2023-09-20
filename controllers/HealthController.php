<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\base\Event;
use yii\web\Response;


class HealthController extends Controller
{
  public function init()
  {
      parent::init();
  }
  public function actionTrial()
  {
    return $msg = "EveryThing is okay";
  }
}

