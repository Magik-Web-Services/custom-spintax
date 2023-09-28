<?php
$path = preg_replace('/wp-content(?!.*wp-content).*/', '', __DIR__);
require_once($path . 'wp-load.php');
require_once(SPINTAX_INC . 'functions.php');
// (A) HELPER FUNCTION - SERVER RESPONSE
function verbose($ok = 1, $info = "")
{
    if ($ok == 0) {
        http_response_code(400);
    }
    exit(json_encode(["ok" => $ok, "info" => $info]));
}


// (B) INVALID UPLOAD
if (empty($_FILES) || $_FILES["file"]["error"]) {
    verbose(0, "Failed to move uploaded file.");
}

$upload = wp_upload_dir();
$upload_dir = $upload['basedir'];
$filePath = $upload_dir . '/spintax/';

// (C) UPLOAD DESTINATION - CHANGE FOLDER IF REQUIRED!
if (!file_exists($filePath)) {
    if (!mkdir($filePath, 0777, true)) {
        verbose(0, "Failed to create $filePath");
    }
}



$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
$filePath = $filePath . $fileName;

if (file_exists($filePath)) {
    verbose(0, "File already exists");
}
// (D) DEAL WITH CHUNKS
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
$out = @fopen("{$filePath}", $chunk == 0 ? "wb" : "ab");
if ($out) {
    $in = @fopen($_FILES["file"]["tmp_name"], "rb");
    if ($in) {
        while ($buff = fread($in, 100000)) {
            fwrite($out, $buff);
        }
    } else {
        verbose(0, "Failed to open input stream");
    }
    @fclose($in);
    @fclose($out);

    $file = fopen($_FILES["file"]["tmp_name"], "r");
    $data     = fgetcsv($file, 100000, ",", "'");
    $line     = 0;
    $flag = true;
    do {
        $item_id                   = $data[0];
        $product                   = $data[1];
        $main_cat                  = $data[2];
        $subcat                    = $data[3];
        $tag                       = $data[4];
        $manufacturer              = $data[5];
        $model_name                = $data[6];
        $mpn                       = $data[7];
        $price                     = $data[8];
        $outputvoltage1            = $data[9];
        $outputcurrent1            = $data[10];
        $outputpower1              = $data[11];
        $ratedtotalbatteryvoltage  = $data[12];
        $ratedtotalbatterycapacity = $data[13];
        $ratedtotalbatteryenergy   = $data[14];
        $batterymodelnumber        = $data[15];
        $batteryprice              = (!empty($data[16])) ? $data[16] : '';

        if (($line !== 0)) {
            $success = spintax_csv_file_import_to_database($item_id, $product, $main_cat, $subcat, $tag, $manufacturer, $model_name, $mpn, $price, $outputvoltage1, $outputcurrent1, $outputpower1, $ratedtotalbatteryvoltage, $ratedtotalbatterycapacity, $ratedtotalbatteryenergy, $batterymodelnumber, $batteryprice, $fileName);
        }
        $line++;
    } while ($data = fgetcsv($file, 100000, ",", "'"));

    @unlink($_FILES["file"]["tmp_name"]);
} else {
    verbose(0, "Failed to open output stream");
}

// (E) CHECK IF FILE HAS BEEN UPLOADED
if (!$chunks || $chunk == $chunks - 1) {
    rename("{$filePath}", $filePath);
}
verbose(1, "Upload OK");
