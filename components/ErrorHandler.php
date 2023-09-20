<?php

namespace app\components;

use Yii;
use yii\web\Response;
use yii\web\HttpException;

class ErrorHandler extends \yii\base\ErrorHandler
//class ErrorHandler extends Component
{
    /**
     * Renders the exception.
     * @param \Exception $exception the exception to be rendered.
     */
    protected function renderException($exception)
    {
        $response = \Yii::$app->has('response') ? \Yii::$app->response : new Response();
        $response->format = Response::FORMAT_JSON;
        
        # case exception has not http code
        if (empty($exception->statusCode)){
            $response->setStatusCode(500,"General failure / Internal server error");
            $message = empty($exception->getMessage()) ? "General failure" : $exception->getMessage(); 
            $response->data = [
                'success'   =>  false,
                'message'   =>  $message,
                'code'      =>  'err50', 
            ];
            $response->send();
        }
        
        # statusCode (255) custom for kivopay api
        if ($exception->statusCode == 255) 
        {
            $response->setStatusCode(255,"System defined error");
            $response->data =  [
                'success'   => false,
                'message'   => $exception->getMessage(),
                'code'    => 'err' . $exception->getCode(), 
            ];
        }   else if ($exception->statusCode == 403) {
            $response->setStatusCode(403,"Authentication required");
            $response->data = [
                'success'   => false,
                'message'   => $exception->getMessage(),
                'code'    =>  'err52', 
            ];
        }   else if ($exception->statusCode == 404) {
            $response->setStatusCode(404,"Resource not found");
            $response->data = [
                'success'   => false,
                'message'   => $exception->getMessage(),
                'code'    =>  'err44', 
            ];
        }   else {
            $response->setStatusCode($exception->statusCode,"General failure / Internal server error");
            $response->data = [
                'success'   => false,
                'message'   => $exception->getMessage(),
                'code'    =>   'err50', 
            ];
        }
        $response->send();
    }
}

