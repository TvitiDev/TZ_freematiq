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
        $searchModel = $searchModel->getOrders(Yii::$app->request->queryParams, $id);
        return $this->render('view', [
            'id' => $id,
            'model' => $searchModel,
            'dataProvider' => $searchModel,
        ]);
    }


    /**
     * Метод генерации счетов
     * Сваливается по недостатку памяти, при генерации большого числа запросов
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
     * Требует более 256МБ оперативной памяти для выполнения
     */
    public function actionTransaction()
    {

        set_time_limit(0);
        for ($i=0; $i < 100000; $i++) {

            $from = rand(1, 100000);
            $to = rand(1, 100000);
            if($from == $to) {
                continue;
            }
            $summ = rand(1000, 100000);

            Orders::transaction($from, $to, $summ);
            unset($from);
            unset($to);
            unset($summ);
            // списать со счета А
            // зачислить на счет 1
            // списать со счета 1
            // зачислить на счет Б
        }
    }

}
