<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

use app\models\Account;

/**
 * This is the model class for table "orders".
 *
 * @property string $id
 * @property string $account_from
 * @property string $account_to
 * @property string $summ
 * @property string $created
 * @property string $updated
 * @property integer $status
 */
class Orders extends \yii\db\ActiveRecord
{
    const STATUS_SUCCESS = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_from', 'account_to', 'summ', 'status'], 'required'],
            [['account_from', 'account_to', 'summ', 'status'], 'integer'],
            [['created', 'updated'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_from' => 'Account From',
            'account_to' => 'Account To',
            'summ' => 'Summ',
            'created' => 'Created',
            'updated' => 'Updated',
            'status' => 'Status',
        ];
    }

    public function getOrders($params=null, $id)
    {
		$query = new Query;

		// дописать запрос, на удаление предыдущих запросов на модерацию
		$dataProvider = new ActiveDataProvider([
            'query' => $query->select('
			*')
			->from('orders')
            ->where(['account_from' => $id])
            ->orWhere(['account_to' => $id]),
			'pagination' => ['pageSize' => 10,],
			]);

		//если не требуется фильтрация по параметрам пользователя
		if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		// $query->andFilterWhere(['like', 'list.name', $this->name])
		// ->andFilterWhere(['like', 'list.description', $this->description]);
		//$query->addOrderBy( $params['sort'] );

		return $dataProvider;
    }

    public function addHistory($from, $to, $summ)
    {
        $model = new Orders([
            'account_from' => $from,
            'account_to' => $to,
            'summ' => $summ,
            'status' => self::STATUS_SUCCESS,
        ]);
        if(! $model->save()) {
            throw new \yii\db\Exception('Fail save');
        }
    }

    /**
     * Перевод денежных средств
     * @param $from int
     * @param $to int
     * @param $summ int
     * @return bool
     */
    public function transaction($from, $to, $summ)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $query = new Query;

            $orderFrom = Account::findOne($from);
            $orderOne = Account::findOne(['id' => 1]);
            $orderTo = Account::findOne(['id' => $to]);

            if(! ($orderFrom && $orderOne && $orderTo)) {
                throw new \yii\web\NotFoundHttpException('Счет не найден');
            }

            // if($orderFrom->balance >= $summ)

            // списать со счета А
            $result = Yii::$app->db->createCommand('UPDATE `account` SET `balance`= `balance` - :balance WHERE balance >= :balance and id = :id')
            ->bindValue(':balance', $summ)
            ->bindValue(':id', $from)
            ->execute();

            if(!$result) {
                throw new \yii\db\Exception('Fail update');
            }

            // зачислить на счет 1
            $result = Yii::$app->db->createCommand('UPDATE `account` SET `balance`= `balance` + :balance WHERE id = 1')
            ->bindValue(':balance', $summ)
            ->execute();

            if(!$result) {
                throw new \yii\db\Exception('Fail update');
            }

            self::addHistory($from, 1, $summ);

            // списать со счета 1
            $result = Yii::$app->db->createCommand('UPDATE `account` SET `balance`= `balance` - :balance WHERE balance >= :balance and id = 1')
            ->bindValue(':balance', $summ)
            ->execute();

            if(!$result) {
                throw new \yii\db\Exception('Fail update');
            }

            self::addHistory(1, $to, $summ);

            // зачислить на счет Б
            $result = Yii::$app->db->createCommand('UPDATE `account` SET `balance`= `balance` + :balance WHERE id = :id')
            ->bindValue(':balance', $summ)
            ->bindValue(':id', $to)
            ->execute();

            if(!$result) {
                throw new \yii\db\Exception('Fail update');
            }

            self::addHistory($from, $to, $summ);


            $transaction->commit();
        } catch (\Exception $e) {
            // Yii::error($e);
            $transaction->rollBack();
        }

    }
}
