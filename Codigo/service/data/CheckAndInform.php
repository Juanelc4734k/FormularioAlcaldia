<?php
require_once dirname(__DIR__) . '/lib/vendor/autoload.php';

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

// Verificar cantidad de registros nuevos
$sql = "SELECT COUNT(*) as total FROM registros WHERE DATE(fecha_creacion) = CURDATE()";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// Agregar log para debug
error_log("Total registros hoy: " . $row['total']);

if ($row['total'] >= 50) {
    error_log("Iniciando generación de informe...");
    
    // Obtener los registros del día
    $sql = "SELECT * FROM registros WHERE DATE(fecha_creacion) = CURDATE()";
    $result = $conn->query($sql);
    
    // Crear Excel
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
    while($data = $result->fetch_assoc()) {
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
        $sheet->setCellValue('K' . $row, $data['fecha_creacion']);
        $row++;
    }

    // Ajustar ancho de columnas automáticamente
    foreach(range('A','K') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Guardar Excel
    $filename = 'informe_registros_' . date('Y-m-d_H-i-s') . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);
    
    // Enviar correo
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jtoroblandon@gmail.com';
        $mail->Password = 'mcty qwuy qnmm iowg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('jtoroblandon@gmail.com', 'Alcaldia Municipal de Montebello');
        $mail->addAddress('jtoroblandon@gmail.com');
        
        $mail->addAttachment($filename);

        $mail->isHTML(true);
        $mail->Subject = 'Informe de Registros - Alcaldia de Montebello';
        $mail->Body = 'Adjunto encontrará el informe con los últimos 50 registros del sistema.';
        
        $mail->send();
        unlink($filename); // Eliminar archivo temporal
        
        error_log("Informe enviado exitosamente");
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
    }
}

$conn->close();
?> 