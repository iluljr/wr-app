<?php
require __DIR__.'/../vendor/autoload.php';
$path = __DIR__.'/../storage/app/private/templates/WR_template.xlsx';
$spread = PhpOffice\PhpSpreadsheet\IOFactory::load($path);
$sheet=$spread->getActiveSheet();
$breaks=$sheet->getBreaks();
foreach($breaks as $coord=>$type){
    echo $coord.'='.( $type==PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW ? 'ROW' : 'COL').PHP_EOL;
}
$setup=$sheet->getPageSetup();
echo 'Orientation='.$setup->getOrientation().PHP_EOL;
echo 'PaperSize='.$setup->getPaperSize().PHP_EOL;
echo 'Scale='.$setup->getScale().PHP_EOL;
?>
