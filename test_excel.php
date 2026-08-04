<?php
require 'vendor/autoload.php';
$spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load('_bahan-skripsi/cleardata.xlsx');
// Try with formatData = true first to see how PhpSpreadsheet natively sees it
$rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
echo "FORMATTED:\n";
echo json_encode(array_slice($rows, 0, 5), JSON_PRETTY_PRINT);

$rowsRaw = $spreadsheet->getActiveSheet()->toArray(null, true, false, false);
echo "\n\nRAW:\n";
echo json_encode(array_slice($rowsRaw, 0, 5), JSON_PRETTY_PRINT);
