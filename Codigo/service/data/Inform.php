<?php

// Requerir librería PHPSpreadsheet
require '../lib/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alcaldiaformulario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Contar registros
$sql = "SELECT COUNT(*) as total FROM registros";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total = $row['total'];

// Verificar si hay 50 o más registros
if ($total >= 50) {
    // Obtener los últimos 50 registros
    $sql = "SELECT * FROM registros ORDER BY id DESC LIMIT 50";
    $result = $conn->query($sql);

    // Crear nuevo documento Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Establecer encabezados
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Nombre');
    $sheet->setCellValue('C1', 'Apellido');
    $sheet->setCellValue('D1', 'Email');
    $sheet->setCellValue('E1', 'Teléfono');
    $sheet->setCellValue('F1', 'Género');
    $sheet->setCellValue('G1', 'Vereda');
    $sheet->setCellValue('H1', 'Municipio');
    $sheet->setCellValue('I1', 'Situación');
    $sheet->setCellValue('J1', 'Autorización');
    $sheet->setCellValue('K1', 'Fecha');

    // Llenar datos
    $row = 2;
    while ($data = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $data['id']);
        $sheet->setCellValue('B' . $row, $data['nombre']);
        $sheet->setCellValue('C' . $row, $data['apellido']);
        $sheet->setCellValue('D' . $row, $data['email']);
        $sheet->setCellValue('E' . $row, $data['telefono']);
        $sheet->setCellValue('F' . $row, $data['genero']);
        $sheet->setCellValue('G' . $row, $data['vereda']);
        $sheet->setCellValue('H' . $row, $data['municipio']);
        $sheet->setCellValue('I' . $row, $data['situacion']);
        $sheet->setCellValue('J' . $row, $data['autorizacion']);
        $sheet->setCellValue('K' . $row, $data['fecha']);
        $row++;
    }

    // Ajustar ancho de columnas automáticamente
    foreach(range('A','K') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Crear archivo Excel
    $writer = new Xlsx($spreadsheet);
    $filename = 'informe_registros_' . date('Y-m-d_H-i-s') . '.xlsx';
    $writer->save($filename);


    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jtoroblandon@gmail.com';
        $mail->Password = 'mcty qwuy qnmm iowg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('jtoroblandon@gmail.com', 'Alcaldía Municipal de Montebello');
        $mail->addAddress('jtoroblandon@gmail.com');

        $mail->addAttachment($filename);

        $mail->isHTML(true);
        $mail->Subject = 'Informe de Registros - Alcaldía de Montebello';
        $mail->Body = 'Adjunto encontrará el informe con los últimos 50 registros del sistema.';

        $mail->send();

        // Eliminar archivo temporal
        unlink($filename);
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
    }
}

$conn->close();

?>
