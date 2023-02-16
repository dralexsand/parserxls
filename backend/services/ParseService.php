<?php

namespace app\services;

use app\models\Category;
use app\models\ModelInterface;
use app\models\Month;
use app\models\Product;
use app\models\Summary;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParseService
{
    /**
     * @param ModelInterface $model
     * @return array
     */
    protected function getItems(ModelInterface $model)
    {
        $items = $model::find()->select(['id', 'name'])->all();

        $list = [];

        if ($items) {
            foreach ($items as $item) {
                $list[$item->id] = $item->name;
            }
        }

        return $list;
    }

    /**
     * @param string $fileName
     * @param array $ext
     * @return bool
     */
    public function checkExtension(string $fileName, array $ext = ['xls', 'xlsx']): bool
    {
        $fileParts = explode('.', $fileName);

        $extFile = $fileParts[count($fileParts) - 1];

        return in_array($extFile, $ext);
    }

    /**
     * @param string $fileName
     * @return string|void
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function process(string $fileName)
    {
        if (!file_exists($fileName)) {
            return "File not found";
        }

        if (!$this->checkExtension($fileName)) {
            return "Not a valid file type";
        }

        $listCategories = $this->getItems(new Category());
        $listProducts = $this->getItems(new Product());
        $listMonths = $this->getItems(new Month());

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($fileName);

        $sheet = $spreadsheet->getSheet(0);

        $itemsList = $this->getItemsList($sheet);

        foreach ($itemsList as $category => $productListItems) {
            $categoryId = array_search($category, $listCategories);

            if (!$categoryId) {
                // insert new category
                $newCategory = new Category();
                $newCategory->name = $category;
                $newCategory->save();
                $categoryId = $newCategory->id;
            }

            foreach ($productListItems as $itemCategory) {
                $isProductExist = in_array($itemCategory['content'], $listProducts);

                if (!$isProductExist) {
                    // insert new product
                    $newProduct = new Product();
                    $newProduct->name = $itemCategory['content'];
                    $newProduct->category_id = $categoryId;
                    $newProduct->save();
                    $productId = $newProduct->id;
                } else {
                    $productId = array_search($itemCategory['content'], $listProducts);
                }

                $rowId = (int)$itemCategory['row'];

                // get costs
                $costs = $this->getCost($sheet, $rowId);

                foreach ($listMonths as $key => $month) {
                    if ($isProductExist) {
                        // update
                        $summary = Summary::find()->where(['product_id' => $productId])->one();
                    } else {
                        // create
                        $summary = new Summary();
                    }

                    $summary->product_id = $productId;
                    $summary->month_id = $key;
                    $summary->cost = $costs[$key];
                    $summary->save();
                }
            }
        }

        $this->clearDb($itemsList);
    }

    /**
     * @param Worksheet $sheet
     * @param int $rowId
     * @return array
     */
    protected function getCost(Worksheet $sheet, int $rowId): array
    {
        $range = $this->getRangeColumns();

        $data = [];

        $key = 1;

        foreach ($range as $rangeColumn) {
            $cell = "$rangeColumn$rowId";

            $content = $sheet->getCell($cell)->getCalculatedValue();

            if ($content === null) {
                $content = 0.00;
            }

            if (!is_numeric($content)) {
                $content = 0.00;
            }

            $data[$key] = round((float)$content, 2);

            $key++;
        }

        return $data;
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    protected function getItemsList(Worksheet $sheet): array
    {
        $breakWorld = "CO-OP";

        $data = [];

        $markerCategory = 1;

        $row = 1;
        while ($row < 1000) {
            $cell = "A$row";

            $content = $sheet->getCell($cell)->getValue();

            if ($content !== null) {
                $content = str_replace('/', '_', $content);

                if (str_contains($content, $breakWorld)) {
                    break;
                }

                if (strtolower(trim($content)) === 'total') {
                    $markerCategory = 1;
                } else {
                    if ($markerCategory === 1) {
                        $category = trim($content);
                        $markerCategory = 0;
                    } else {
                        $data[$category][] = [
                            'row' => $row,
                            'content' => $content,
                        ];
                    }
                }
            }

            $row++;
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getRangeColumns(): array
    {
        return range('B', 'M');
    }

    /**
     * Clear Db from unlinked categories, products
     *
     * @param array $itemsList
     * @return void
     */
    protected function clearDb(array $itemsList)
    {
        //
    }

}