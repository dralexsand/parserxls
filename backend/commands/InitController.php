<?php

namespace app\commands;

use app\services\FillDb;
use yii\console\Controller;
use yii\console\ExitCode;


class InitController extends Controller
{
    /**
     * This command init db
     * @return int Exit code
     */
    public function actionIndex()
    {
        $init = new FillDb();
        $isTable = $init->fillDbMonth();

        echo $isTable . "\n";

        return ExitCode::OK;
    }
}
