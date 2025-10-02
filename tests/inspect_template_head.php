<?php
require __DIR__.'/../vendor/autoload.php';
$path = __DIR__.'/../storage/app/private/templates/WR_template.xlsx';
$spread = PhpOffice\PhpSpreadsheet\IOFactory::load($path);
$sheet=$spread->getActiveSheet();
$cells=['B3','D3','G4','B7','B8','E8','G8'];
foreach($cells as $cell){
 echo $cell.'='.$sheet->getCell($cell)->getFormattedValue().PHP_EOL;
}
?>
