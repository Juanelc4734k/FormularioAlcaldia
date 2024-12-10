<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $lastname = $_POST['lastname']; 
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $auth = isset($_POST['auth']) ? 1 : 0;

    $data = [
        'name' => $name,
        'lastname' => $lastname,
        'email' => $email,
        'phone' => $phone,
        'gender' => $gender,
        'address' => $address,
        'city' => $city,
        'country' => $country,
        'auth' => $auth
    ];

    $_SESSION['form_data'] = $data;
    
    header("Location: service/data/Send.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro - Alcaldía de Montebello</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center p-4">
    <header class="w-full max-w-4xl flex flex-col items-center mb-8">
        <img src="assets/logo_alcaldia.png" alt="Logo Alcaldía de Montebello" class="h-32 mb-4">
        <h1 class="text-3xl font-bold text-center text-[#1B4F72]">Alcaldía Municipal de Montebello</h1>
        <p class="text-lg text-gray-600">"Unidos por el progreso de Montebello"</p>
    </header>
    
    <form action="service/data/Send.php" method="post" class="bg-white shadow-lg rounded-lg px-8 pt-6 pb-8 mb-4 w-full max-w-2xl border border-gray-200">
        <h2 class="text-2xl font-bold text-center mb-8 text-[#1B4F72] border-b pb-4">Formulario de Registro</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nombre</label>
                <input type="text" id="name" name="name" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1B4F72]">
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">Apellido</label>
                <input type="text" id="lastname" name="lastname" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1B4F72]">
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1B4F72]">
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">Teléfono</label>
                <input type="tel" id="phone" name="phone" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1B4F72]">
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="gender">Género</label>
                <select id="gender" name="gender" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1B4F72]">
                    <option value="">Seleccione su género</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="otro">Otro</option>
                    <option value="prefiero_no_decir">Prefiero no decir</option>
                </select>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="address">Vereda</label>
                <input type="text" id="address" name="address" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1B4F72]">
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="city">Municipio</label>
                <select id="city" name="city" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1B4F72]">
                    <option value="montebello">Montebello</option>
                </select>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="country">Situación</label>
                <select id="country" name="country" required 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#1B4F72]">
                    <option value="">Seleccione su situación</option>
                    <option value="poblacion_victima">Población víctima</option>
                    <option value="afro">Afrodescendiente</option>
                    <option value="discapacitado">Discapacitado</option>
                    <option value="indigena">Indígena</option>
                    <option value="adulto_mayor">Adulto mayor</option>
                    <option value="joven">Joven</option>
                    <option value="poblacion_lgbt">Población LGBTIQ+</option>
                    <option value="servidor_publico">Servidor público</option>
                    <option value="otro">Otro</option>
                    <option value="no_aplica">No aplica</option>
                </select>
            </div>
        </div>

        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center space-x-3">
                <input type="checkbox" id="auth" name="auth" required 
                    class="h-5 w-5 text-[#1B4F72] focus:ring-[#1B4F72] border-gray-300 rounded">
                <label for="auth" class="text-sm text-gray-700">
                    Autorizo el tratamiento de mis datos personales de acuerdo a la Ley 1581 de 2012
                </label>
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            <button type="submit" 
                class="bg-[#1B4F72] hover:bg-[#2874A6] text-white font-bold py-3 px-8 rounded-full focus:outline-none focus:shadow-outline transform transition hover:scale-105">
                Enviar Registro
            </button>
        </div>
    </form>

    <footer class="w-full max-w-4xl text-center text-gray-600 text-sm mt-8">
        <p>Alcaldía Municipal de Montebello, Antioquia</p>
        <p>Dirección: Carrera 20 N° 19-15 Parque Principal</p>
        <p>Teléfono: (604) 448 47 02</p>
        <p>Correo: contactenos@montebello-antioquia.gov.co</p>
        <p>Horario de atención: Lunes a Viernes 8:00 am - 12:00 m y 2:00 pm - 6:00 pm</p>
    </footer>
    <?php if (isset($_SESSION['message'])): ?>
        <script>
            Swal.fire({
                title: '¡Registro exitoso!',
                text: '<?php echo $_SESSION['message']; ?>',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        </script>
        <?php require_once __DIR__ . '/service/data/CheckAndInform.php'; ?>
    <?php endif; ?>
</body>
</html>