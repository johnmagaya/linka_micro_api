<?php

namespace app\models;

use Yii;
use \Firebase\JWT\JWT;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "user".
 *
 * @property int $user_id
 * @property string $user_name
 * @property string $cus_code
 * @property string $cus_mob
 * @property string $email
 * @property string $mob_num1
 * @property string $mob_num2
 * @property int $createdy_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 * @property string $secret_word
 * @property string $password
 * @property int $status
 * @property string $last_login
 * @property string $login_ip
 * @property string $device_id
 * @property string $access_token
 * @property string $login_otp
 * @property string $login_otp_status // 0 - default, 1-sent to user, 2-activated
 * @property string pin_reset_otp
 * @property string pin_reset_otp_status // 0 - default, 1-sent to user, 2-activated
 * @property string device_name
 * @property string device_unique_id
 * @property string profile_pic
 * @property string biometric_enabled_at
 * @property string biometric_enabled
 * 
 * this class and databases should be customized to customer
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * {@inheritdoc}
     */
     const STATUS_ACTIVE = 10;
     const STATUS_BLOCKED = 20;
     const STATUS_DORMANT = 30;

    public $username;

    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_name', 'cus_code', 'cus_mob', 'createdy_by', 'created_at', 'updated_by', 'updated_at', 'secret_word', 'password', 'status'], 'required'],
            [['user_name','username', 'cus_code', 'cus_mob', 'email', 'mob_num1', 'mob_num2', 'secret_word', 'password', 
            'login_ip', 'device_id', 'access_token','login_otp','pin_reset_otp','device_name','device_unique_id','profile_pic','biometric_enabled'], 'string'],
            [['createdy_by', 'updated_by', 'status','login_otp_status', 'pin_reset_otp_status', 'account_notify'], 'integer'],
            [['created_at', 'updated_at', 'last_login','biometric_enabled_at'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                'value' => new Expression('GETDATE()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'cus_code' => 'Cus Code',
            'cus_mob' => 'Cus Mob',
            'email' => 'Email',
            'mob_num1' => 'Mob Num1',
            'mob_num2' => 'Mob Num2',
            'createdy_by' => 'Createdy By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'secret_word' => 'Secret Word',
            'password' => 'Password',
            'status' => 'Status',
            'last_login' => 'Last Login',
            'login_ip' => 'Login Ip',
            'device_id' => 'Device ID',
            'access_token' => 'Access Token',
            'login_otp' => 'Login Otp'
        ];
    }

    public static function findIdentity($id)
    {
        //return static::findOne($id);
        return static::findOne(['user_id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public static function findIdentityByMobileNumber($cus_mob)
    {
        return static::findOne([
            'cus_mob' => $cus_mob,
            'status' => static::STATUS_ACTIVE // ADDED TO ENSURE ONLY ACTIVE CAAN LOGIN
        ]);
    }

    public function getId()
    {
        return $this->user_id;
    }

    public function getAuthKey()
    {
        //return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        //return $this->authKey === $authKey;
    }

    public function validatePin($pin)
    {
        //existing hash string
        $hash = $this->password;
        return Yii::$app->getSecurity()->validatePassword($pin, $hash);
    }

    public function validateOtp($otp)
    {
        //compare inserted otp with the one stored in db
        $otp = base64_encode($otp);

        return $this->login_otp === $otp;
    }

    public function validateToken()
    {
        $headers = Yii::$app->request->headers;
        $auth_header = $headers->get('Authorization');
        $jwt_key = Yii::$app->params['app_jwt_key'];

        $auth_header = explode(" ", $auth_header);
  
        $token = empty($auth_header[1]) ? null : $auth_header[1];
        $bearer = empty($auth_header[0]) ? null : $auth_header[0];

        if(!empty($token) && $bearer == "Kivopay")
        {
            try{
            $valid_data = JWT::decode($token, $jwt_key, array('HS512'));
            
            $not_before = date('Y-m-d h:i:s',$valid_data->nbf);
            //var_dump($not_before);

            $valid_data = $valid_data->data;

            $auth_result = [
                'status'    => true,
                'data'      => $valid_data,
            ];
    
            }catch(Exception $e){
                $valid_data = $e->getMessage();
                $auth_result = [
                    'status'    => false,
                    'data'      => $valid_data,
                ];
            }
        }
        else
        {
            $valid_data = 'Required Authentication';
            $auth_result = [
                'status'    => false,
                'data'      => $valid_data,
            ];
        }
    
        return $auth_result;
    }

    public function validateTransactionPin($pin,$userId){
        $user = static::findOne(['user_id' => $userId]);

        if (!empty($user)){
            //compare the existing hash with user entered pin
            $hash = $user->password;
            $result =  Yii::$app->getSecurity()->validatePassword($pin, $hash);

            // if invalid pin log and update on customer deny 
            if (!$result){
                Yii::error('Invalid pin has applied to complete transaction');
            }

            return $result;
        }

        return false;
    }

    /**
     * update login information after successfully login
     * applied to app user
     */
    public function updateLoginDetail(){
        $clientIp = Yii::$app->request->remoteIp;
        //Yii::error($clientIp);
        $this->last_login = new Expression('GETDATE()');
        $this->login_ip = $clientIp;

        return $this->update(false);
    }

    /**
     * update user and set device information
     */
    public function updateDeviceInformation($device_name, $device_id){
        $this->device_name = $device_name;
        $this->device_unique_id = $device_id;
        
        //everytime when updating device information also disable 
        // Biometric login
        $this->biometric_enabled = "NO";

        return $this->update(false);
    }

    public function setInitialProfilePhoto($nida) {
        $nida_photo = Yii::getAlias('@webroot/uploads/photo/').$nida.'.png';
        $profile_dest =  Yii::getAlias('@webroot/uploads/customer_profile/').$nida.'.png';

        if (is_file($nida_photo)) {
            copy($nida_photo, $profile_dest);

            //profile column in the database
            $this->profile_pic = '/uploads/customer_profile/'.$nida.'.png';
            return $this->update(false);
        }
        return false;
    }

    public function changeUserPin($newPin) {
        $encryPin = Yii::$app->getSecurity()->generatePasswordHash($newPin);
        $this->password = $encryPin;
        return $this->update(false);
    }
}
