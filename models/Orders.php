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

    public function getOrders($params=null)
    {
		$query = new Query;

		// дописать запрос, на удаление предыдущих запросов на модерацию
		$dataProvider = new ActiveDataProvider([
            'query' => $query->select('
			*')
			->from('orders')
            ->where(['account_from' => $this->id])
            ->orWhere(['account_to' => $this->id]),
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

    public function Transaction($from, $to, $summ)
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

            if($orderFrom->balance >= $summ) {

                Yii::$app->db->createCommand('UPDATE `account` SET `balance`= `balance` - :balance WHERE balance >= :balance and id = :id')
                ->bindValue(':balance', $summ)
                ->bindValue(':id', $from)
                ->execute();

                Yii::$app->db->createCommand('UPDATE `account` SET `balance`= `balance` + :balance WHERE id = :id')
                ->bindValue(':balance', $summ)
                ->bindValue(':id', $to)
                ->execute();

            }

            $transaction->commit();
        } catch (Exception $e) {
            Yii::error($e);
            $transaction->rollBack();
        }

    }
}
