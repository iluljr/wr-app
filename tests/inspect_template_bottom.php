<?php
require __DIR__.'/../vendor/autoload.php';
$path = __DIR__.'/../storage/app/private/templates/WR_template.xlsx';
$spread = PhpOffice\PhpSpreadsheet\IOFactory::load($path);
$sheet=$spread->getActiveSheet();
for($row=220;$row<=240;$row++){
    $vals=[];
    foreach(['B','C','D','E','F','G'] as $col){
        $vals[$col]=$sheet->getCell($col.$row)->getFormattedValue();
    }
    echo $row.' '.json_encode($vals, JSON_UNESCAPED_UNICODE).PHP_EOL;
}
?>
