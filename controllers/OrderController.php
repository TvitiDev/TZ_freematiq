<?php

namespace app\controllers;

use Yii;
use app\models\Orders;
use app\models\Account;
use app\models\Order;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Orders model.
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Orders models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new Account();
        $dataProvider = $searchModel->getAccounts(Yii::$app->request->queryParams);
        Yii::warning($dataProvider);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $searchModel = new Orders();
        $searchModel = $searchModel->getOrders(Yii::$app->request->queryParams);
        if($searchModel) {

        }
        return $this->render('view', [
            'id' => $id,
            'model' => $searchModel,
            'dataProvider' => $searchModel,
        ]);
    }


    /**
     * Метод генерации счетов
     */
    public function actionGenerate()
    {
        set_time_limit(0);
        for ($i=0; $i < 300000; $i++) {
            Yii::$app->db->createCommand()->insert('account', [
                'balance' => rand(1000, 1000000) // знаю, что при работе с деньгами лучше работать в копейках, но так как валюта и денежные еденыцы не указаны, то делаю по заданию
            ])->execute();
        }
    }


    /**
     * Метод транзакции денег
     */
    public function actionTransaction()
    {
        Orders::Transaction(2, 3, 1000);

        // set_time_limit(0);
        // for ($i=0; $i < 10; $i++) {
        //
        //
        //     // списать со счета А
        //     // зачислить на счет 1
        //     // списать со счета 1
        //     // зачислить на счет Б
        //
        //     Yii::$app->db->createCommand()->insert('account', [
        //         'balance' => rand(1000, 1000000) // знаю, что при работе с деньгами лучше работать в копейках, но так как валюта и денежные еденыцы не указаны, то делаю по заданию
        //     ])->execute();
        // }
    }

}
