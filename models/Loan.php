<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "loan".
 *
 * @property int $id
 * @property int|null $member_id
 * @property string|null $partner_code
 * @property float|null $principal_amount
 * @property int|null $loan_category
 * @property float|null $fee_amount
 * @property string|null $initiated_date
 * @property int|null $status
 * @property string|null $contract_document
 * @property string|null $updated_at
 * @property int|null $loan_term
 * @property int|null $delay_counter
 * @property int|null $date_diff
 * @property int|null $repayment_plan_id
 * @property string|null $title
 * @property int|null $created_by
 */
class Loan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'loan_category', 'status', 'loan_term', 'delay_counter', 'date_diff', 'repayment_plan_id'], 'integer'],
            [['principal_amount', 'fee_amount'], 'number'],
            [['initiated_date', 'updated_at', 'created_by'], 'safe'],
            [['partner_code'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
            [['contract_document'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member',
            'partner_code' => 'Partner Code',
            'principal_amount' => 'Principal Amount',
            'loan_category' => 'Loan Category',
            'fee_amount' => 'Fee Amount',
            'initiated_date' => 'Initiated Date',
            'status' => 'Status',
            'contract_document' => 'Contract Document',
            'updated_at' => 'Updated At',
            'loan_term' => 'Loan Term',
            'delay_counter' => 'Delay Counter',
            'date_diff' => 'Date Diff',
            'title' => 'Loan Title',
            'repayment_plan_id' => 'Payment Plan ',
            'created_by' => 'Created By',
        ];
    }

    public function getMember($member_id)
    {
        $get_member = Members::find()->where(['id' => $member_id])->one();
        return $get_member;
    }
    public function getPlan($plan_id){
        $get_plan = RepaymentPlan::find()->where(['id' => $plan_id])->one();
        return $get_plan;
    }
}
