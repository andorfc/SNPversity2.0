<?php

header('Content-Type: application/json');

$start = $_POST['start'];
$end = $_POST['end'];
$chr = $_POST['chr'];
$dataset = $_POST['dataSet'];
$genotypesJson = $_POST['genotypes'];
$filename = $_POST['outName'];

// Validate input
if (!is_numeric($start) || !is_numeric($end)) {
    echo json_encode(array("status" => "error", "message" => "Invalid input"));
    exit;
}

//$db = escapeshellarg("./hdf5/chr10_test.h5");

$ds_part1 = $chr;
$ds_part2 = "HQ";

if($dataset == "mgdb2024_hq")
{
        $ds_part2 = "HQ";
} else {
        $ds_part2 = "HC";
}

$db_filename = "./hdf5/" . $ds_part1 . "_" . $ds_part2 . ".h5";
//$db_filename = "./hdf5/" . $ds_part1 . "_" . $ds_part2 . ".h5";

//$db = escapeshellarg("./hdf5/chr10_HQ_small.h5");
$db = escapeshellarg($db_filename);
//$vcf = escapeshellarg("./vcf/chr10_small_v3.vcf");
$vcf = escapeshellarg($filename);
$startEscaped = escapeshellarg($start);
$endEscaped = escapeshellarg($end);

$genotypesArray = json_decode($genotypesJson);
$jsonArray = escapeshellarg(json_encode($genotypesArray));

#$arrayOfStrings = ['1_SRR17046119', '10_SRR17045932', '1003_SRR17045852', '1004_SRR17045851', '1005_SRR17045850','GEMS55_SRR8906658','Liao6082_CRX446062','MC9274_CRX444643'];
#$jsonArray = escapeshellarg(json_encode($arrayOfStrings));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Execute Python script
    #$command = '/Users/carsonandorf/opt/miniconda3/bin/python h5_to_vcf.py ' . $db . " " . $vcf . " " . $startEscaped . " " . $endEscaped;
    $command = '/Users/carsonandorf/opt/miniconda3/bin/python h5_to_vcf.py ' . $db . " " . $vcf . " " . $startEscaped . " " . $endEscaped . " " . $jsonArray;
    $output = shell_exec($command);

    if ($output === null) {
        echo json_encode(array("status" => "error", "message" => "Python script execution failed"));
    } else {
        echo json_encode(array("status" => "success", "message" => "Python script executed", "output" => $output));
    }
}
?>
