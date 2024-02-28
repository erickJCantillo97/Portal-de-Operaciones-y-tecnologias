<?php

require 'UploadException.php';

$dir      = dirname(__FILE__);
$jar_path = $dir . '/../projectreader/target/bryntum-project-reader-6.1.22.jar';
$tmp_dir  = $dir . '/tmp/';

// Force UTF-8 encoding to make sure multi-byte characters returned by bryntum-project-reader-xxx.jar
// are not corrupted
// putenv('LANG=es_ES.UTF-8');

$result = ['success' => true];

try {

    if (!isset($_FILES['mpp-file'])) {
        throw new Exception('Error al subir!<br> Probablemente el archivo es muy grande.');
    }

    if ($_FILES['mpp-file']['error'] !== UPLOAD_ERR_OK) {
        throw new Bryntum\UploadException($_FILES['mpp-file']['error']);
    }

    $tmp_file = $_FILES['mpp-file']['tmp_name'];

    if (!is_uploaded_file($tmp_file)) {
        throw new Exception('Error al subir, verifica conexion a internet e intenta denuevo.');
    }

    if (!is_dir($tmp_dir)) {
        throw new Exception('No existe directorio temporal');
    }

    $move_path = $tmp_dir . uniqid();

    if (!move_uploaded_file($tmp_file, $move_path)) {
        unlink($move_path);
        throw new Exception('No se pudo subir el archivo!<br>No tienes permiso de escritura en el servidor');
    }

    // Checking if java is installed
    exec('java -version', $json, $exec_result);

    if ($exec_result > 0) {
        unlink($move_path);
        throw new Exception('<br>No se pudo procesar el archivo subido!<br>Servidor no tiene java instalado');
    }

    // launch JAR file to extract the uploaded MPP-file contents
    $shell_command = 'java -jar ' . escapeshellarg($jar_path) . ' ' . escapeshellarg($move_path) . ' 1';

    $json = shell_exec($shell_command);
    // echo 'llega a json'.$json.' fin json';
    // $jsonencode = mb_convert_encoding($json, 'UTF-8', 'ISO-8859-15');

    // dd();

    // ensure the output is actually a JSON string
    $decoded = json_decode($json, false);
    // echo 'llega a decoded ' . $decoded . ' fin decoded';
    if (!$json || !$decoded) {
        unlink($move_path);
        throw new Exception('Could n ot process uploaded file!<br>Command: ' . $shell_command);
    }
    // cleanup copied file
    unlink($move_path);

    $result['data'] = $decoded;
} catch (Exception $e) {

    $result = [
        'success' => false,
        'msg'     => $e->getMessage()
    ];
}

die(json_encode($result));
