<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan_category".
 *
 * @property int $id
 * @property float|null $start_amount
 * @property float|null $end_amount
 * @property float|null $fee
 */
class LoanCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_amount', 'end_amount', 'fee'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_amount' => 'Start Amount',
            'end_amount' => 'End Amount',
            'fee' => 'Fee',
        ];
    }
}
