<?php

session_start();

// Verificar si hay datos POST o datos en sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'name' => $_POST['name'] ?? null,
        'lastname' => $_POST['lastname'] ?? null,
        'email' => $_POST['email'] ?? null,
        'phone' => $_POST['phone'] ?? null,
        'gender' => $_POST['gender'] ?? null,
        'address' => $_POST['address'] ?? null,
        'city' => $_POST['city'] ?? null,
        'country' => $_POST['country'] ?? null,
        'auth' => isset($_POST['auth']) ? 1 : 0
    ];
} elseif (isset($_SESSION['form_data'])) {
    $data = $_SESSION['form_data'];
} else {
    die("No hay datos del formulario para procesar");
}

// Validar que todos los campos requeridos existan y no sean nulos
$required_fields = ['name', 'lastname', 'email', 'phone', 'gender', 'address', 'city', 'country', 'auth'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || $data[$field] === null) {
        die("Campo requerido faltante o vacío: $field");
    }
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alcaldiaformulario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Insertar datos en la base de datos
$sql = "INSERT INTO registros (nombre, apellido, email, telefono, genero, vereda, municipio, situacion, autorizacion) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", 
    $data['name'],
    $data['lastname'], 
    $data['email'],
    $data['phone'],
    $data['gender'],
    $data['address'],
    $data['city'],
    $data['country'],
    $data['auth']
);

$stmt->execute();
$stmt->close();
$conn->close();

// Generar PDF
require('../lib/fpdf186/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// Logo
$pdf->Image('../../assets/logo_alcaldia.png', 10, 10, 50);
$pdf->Ln(20);

// Título
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,mb_convert_encoding('Formulario de Registro', 'ISO-8859-1', 'UTF-8'),0,1,'C');
$pdf->SetFont('Arial','',12);

// Fecha actual en formato español
setlocale(LC_TIME, 'es_ES.UTF-8');
$fecha = mb_convert_encoding(date('d \d\e F \d\e Y'), 'ISO-8859-1', 'UTF-8');
$pdf->Cell(0,10,'Fecha: '.$fecha,0,1,'R');
$pdf->Ln(10);

// Datos del formulario
$pdf->Cell(0,10,mb_convert_encoding('Nombre: '.$data['name'].' '.$data['lastname'], 'ISO-8859-1', 'UTF-8'),0,1);
$pdf->Cell(0,10,mb_convert_encoding('Email: '.$data['email'], 'ISO-8859-1', 'UTF-8'),0,1);
$pdf->Cell(0,10,mb_convert_encoding('Teléfono: '.$data['phone'], 'ISO-8859-1', 'UTF-8'),0,1);
$pdf->Cell(0,10,mb_convert_encoding('Género: '.$data['gender'], 'ISO-8859-1', 'UTF-8'),0,1);
$pdf->Cell(0,10,mb_convert_encoding('Vereda: '.$data['address'], 'ISO-8859-1', 'UTF-8'),0,1);
$pdf->Cell(0,10,mb_convert_encoding('Municipio: '.$data['city'], 'ISO-8859-1', 'UTF-8'),0,1);
$pdf->Cell(0,10,mb_convert_encoding('Situación: '.$data['country'], 'ISO-8859-1', 'UTF-8'),0,1);
$pdf->Cell(0,10,mb_convert_encoding('Autorización: '.($data['auth'] ? 'Sí' : 'No'), 'ISO-8859-1', 'UTF-8'),0,1);

// Ley de tratamiento de datos
$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,mb_convert_encoding('Tratamiento de Datos Personales', 'ISO-8859-1', 'UTF-8'),0,1);
$pdf->SetFont('Arial','',10);
$texto_ley = 'De conformidad con la Ley 1581 de 2012 de Protección de Datos Personales, autorizo a la Alcaldía de Montebello para el tratamiento de mis datos personales conforme a las finalidades establecidas en su Política de Tratamiento de Datos Personales.';
$pdf->MultiCell(0,5,mb_convert_encoding($texto_ley, 'ISO-8859-1', 'UTF-8'),0,'J');

// Pie de página
$pdf->SetY(-60);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,5,mb_convert_encoding('Alcaldía Municipal de Montebello, Antioquia', 'ISO-8859-1', 'UTF-8'),0,1,'C');
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,5,mb_convert_encoding('Dirección: Carrera 20 N° 19-15 Parque Principal', 'ISO-8859-1', 'UTF-8'),0,1,'C');
$pdf->Cell(0,5,mb_convert_encoding('Teléfono: (604) 448 47 02', 'ISO-8859-1', 'UTF-8'),0,1,'C');
$pdf->Cell(0,5,mb_convert_encoding('Correo electrónico: contactenos@montebello-antioquia.gov.co', 'ISO-8859-1', 'UTF-8'),0,1,'C');
$pdf->Cell(0,5,mb_convert_encoding('Horario de atención: Lunes a Viernes 8:00 am - 12:00 m y 2:00 pm - 6:00 pm', 'ISO-8859-1', 'UTF-8'),0,1,'C');
$pdf->Cell(0,5,'www.montebello-antioquia.gov.co',0,1,'C');

$pdfFile = 'formulario_'.time().'.pdf';
$pdf->Output('F', $pdfFile);

// Reemplazar la sección de envío de correo con PHPMailer
require '../lib/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

// Definir el asunto y el mensaje del correo
$subject = mb_convert_encoding("Registro Exitoso - Formulario Alcaldia de Montebello", 'UTF-8');
$message = "<h2>¡Gracias por registrarte en nuestro sistema!</h2>";
$message .= "<p>Hemos recibido exitosamente tu información con los siguientes datos:</p>";
$message .= "<h3>Información Personal:</h3>";
$message .= "<ul>";
$message .= "<li><strong>Nombre Completo:</strong> " . $data['name'] . " " . $data['lastname'] . "</li>";
$message .= "<li><strong>Correo Electrónico:</strong> " . $data['email'] . "</li>";
$message .= "<li><strong>Teléfono de Contacto:</strong> " . $data['phone'] . "</li>";
$message .= "</ul>";
$message .= "<h3>Datos Demográficos:</h3>";
$message .= "<ul>";
$message .= "<li><strong>Género:</strong> " . $data['gender'] . "</li>";
$message .= "<li><strong>Vereda:</strong> " . $data['address'] . "</li>";
$message .= "<li><strong>Municipio:</strong> " . $data['city'] . "</li>";
$message .= "<li><strong>Situación:</strong> " . $data['country'] . "</li>";
$message .= "</ul>";
$message .= "<h3>Estado de Autorización:</h3>";
$message .= "<p><strong>Tratamiento de Datos:</strong> " . ($data['auth'] ? 'Autorizado' : 'No Autorizado') . "</p>";
$message .= "<p>Se adjunta el PDF con el detalle de tu registro.</p>";
$message .= "<hr>";
$message .= "<p><em>Atentamente,<br>Alcaldía Municipal de Montebello</em></p>";

try {
    // Configuración del servidor SMTP de Gmail
    $mail->isSMTP(); // Establece el uso de SMTP
    $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
    $mail->SMTPAuth = true; // Habilita la autenticación SMTP
    $mail->Username = 'jtoroblandon@gmail.com'; // Correo del remitente
    $mail->Password = 'mcty qwuy qnmm iowg'; // Contraseña de aplicación de Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Tipo de encriptación
    $mail->Port = 587; // Puerto SMTP

    // Configuración de los datos del correo
    $mail->setFrom('jtoroblandon@gmail.com', 'Alcaldía Municipal de Montebello'); // Remitente
    $mail->addAddress($data['email']); // Destinatario principal (usuario)
    $mail->addAddress('jtoroblandon@gmail.com'); // Copia para la alcaldía
    $mail->addReplyTo('jtoroblandon@gmail.com', 'Alcaldía Municipal de Montebello'); // Dirección de respuesta

    // Adjuntar el archivo PDF generado
    $mail->addAttachment($pdfFile);

    // Configuración del contenido del correo
    $mail->isHTML(true); // Habilita formato HTML
    $mail->Subject = $subject; // Asunto del correo
    $mail->Body = nl2br($message); // Cuerpo HTML del correo
    $mail->AltBody = $message; // Versión texto plano del correo

    $mail->send(); // Envía el correo
} catch (Exception $e) {
    // Registra cualquier error en el envío
    error_log("Error al enviar correo: {$mail->ErrorInfo}");
}

// Eliminar archivo PDF temporal
unlink($pdfFile);

// Limpiar la sesión
unset($_SESSION['form_data']);

$_SESSION['message'] = "¡Registro exitoso! Se ha enviado un correo con los datos del formulario.";
header('Location: ../../index.php');
?>


