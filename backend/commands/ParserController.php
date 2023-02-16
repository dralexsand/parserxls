<?php

namespace app\commands;

use app\services\ParseService;
use yii\console\Controller;
use yii\console\ExitCode;

class ParserController extends Controller
{
    /**
     * This command parsed xml file
     * @param string $fileName
     * @return int Exit code
     */
    public function actionIndex($fileName)
    {
        if (trim($fileName) === '') {
            echo "Argument is blank";
            return ExitCode::OK;
        }

        echo $fileName . "parsing...\n";

        $service = new ParseService();

        $service->process($fileName);

        echo "done\n";

        return ExitCode::OK;
    }
}
