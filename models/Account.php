<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "account".
 *
 * @property string $id
 * @property string $created
 * @property string $updated
 * @property integer $balance
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created', 'updated'], 'safe'],
            [['balance'], 'required'],
            [['balance'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Created',
            'updated' => 'Updated',
            'balance' => 'Balance',
        ];
    }

    public function getAccounts($params=null)
    {
		$query = new Query;

		// дописать запрос, на удаление предыдущих запросов на модерацию
		$dataProvider = new ActiveDataProvider([
            'query' => $query->select('
			*')
			->from('account'),
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
}
