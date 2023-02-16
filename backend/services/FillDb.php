<?php

namespace app\services;

use app\models\Month;
use Exception;

class FillDb
{
    public function fillDbMonth()
    {
        $connection = \Yii::$app->db;

        $sqlExistsTableMonth = "SELECT *
            FROM information_schema.tables
                WHERE table_name = 'month'
            LIMIT 1; ";

        $isTable = $connection->createCommand($sqlExistsTableMonth)->execute();

        $result = "Table month already filled";

        if ($isTable) {
            $sqlNoBlankTable = "SELECT * FROM month;";
            $isNoBlankTable = $connection->createCommand($sqlNoBlankTable)->execute();

            if (!$isNoBlankTable) {
                $months = $this->listMonth();

                $transaction = $connection->beginTransaction();

                try {
                    foreach ($months as $key => $month) {
                        $m = new Month();
                        $m->id = ((int)$key) + 1;
                        $m->name = $month;
                        $m->save();
                    }
                    $transaction->commit();
                    $result = "Table month filled";
                } catch (Exception $e) {
                    $transaction->rollback();
                }
            }
        } else {
            $result = "You need to do migrations";
        }

        return $result;
    }

    public function listMonth()
    {
        return [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
    }
}