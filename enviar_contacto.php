<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$nombre = '';
$email = '';
$mensaje = '';
$enviado = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    // Validación básica
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $error = 'Por favor completa todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } else {
        // Enviar correo
        try {
            $mail = new PHPMailer(true);
            
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['SMTP_PORT'] ?? 587;

            // Remitentes y destinatarios
            $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME'] ?? 'Blog Web');
            $mail->addAddress($_ENV['SMTP_FROM']);
            $mail->addReplyTo($email, $nombre);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = "Nuevo mensaje de contacto de: $nombre";
            $mail->Body = "
                <html>
                <body>
                    <h2>Nuevo mensaje de contacto</h2>
                    <p><strong>Nombre:</strong> " . htmlspecialchars($nombre) . "</p>
                    <p><strong>Correo:</strong> " . htmlspecialchars($email) . "</p>
                    <p><strong>Mensaje:</strong></p>
                    <p>" . nl2br(htmlspecialchars($mensaje)) . "</p>
                </body>
                </html>
            ";
            $mail->AltBody = "Nombre: $nombre\nCorreo: $email\nMensaje:\n$mensaje";

            $mail->send();
            $enviado = true;
        } catch (Exception $e) {
            $error = "Error al enviar el mensaje: " . $mail->ErrorInfo;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/static/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <title>Mensaje enviado - Noticiero Informático</title>
</head>
<body>
    <header>
        <img src ="src/static/img/breaking.png" alt="Breaking News" class="logo">
        <h1 class="el_titulo">Últimas novedades del mundo de la informática</h1>
<!-- innecesario si ya estan en el footer,
        <div class="redes-sociales">
            <a href="https://www.facebook.com/" target="_blank">
                <img src ="src/static/img/face.png" alt="Facebook" class="face">
            </a>
            <a href="https://twitter.com/" target="_blank">
                <img src ="src/static/img/x.png" alt="Twitter" class="x">
            </a>
            <a href="https://www.instagram.com/" target="_blank">
                <img src ="src/static/img/insta.png" alt="Instagram" class="insta">
            </a>
        </div>
        -->
    </header>
    <div class="botones">
        <a href="welcome.html" class="botones_menu">Inicio</a>
        <a href="articulos.php" class="botones_menu">Artículos</a>
        <a href="noticias.php" class="botones_menu">Noticias</a>
        <a href="contacto.html" class="botones_menu">Contacto</a>
    </div>
    <div>
        <hr>
    </div>
    
    <?php if ($enviado): ?>
    <div class="suscripcion-confirmacion">
        <p class="suscripcion-confirmacion-titulo">¡Mensaje enviado!</p>
        <p class="suscripcion-confirmacion-descripcion">Gracias por contactarnos, te responderemos lo antes posible.</p>
        <div class="articulo">
            <p class="art_autor">Nombre: <?php echo htmlspecialchars($nombre); ?></p>
            <p class="art_creado_en">Correo: <?php echo htmlspecialchars($email); ?></p>
            <p class="art_descripcion"><?php echo nl2br(htmlspecialchars($mensaje)); ?></p>
        </div>
        <div class="botones">
            <a href="contacto.html" class="boton">Volver</a>
        </div>
    </div>
    <?php elseif ($error): ?>
    <div class="suscripcion-confirmacion">
        <p class="suscripcion-confirmacion-titulo">Error al enviar el mensaje</p>
        <p class="suscripcion-confirmacion-descripcion" style="color: #d32f2f;"><?php echo htmlspecialchars($error); ?></p>
        <div class="botones">
            <a href="contacto.html" class="boton">Intentar de nuevo</a>
        </div>
    </div>
    <?php endif; ?>
    
    <div>
        <hr>
    </div>
    <footer>
        <div class="footer-izquierda">
            <p>2026 Noticiero Informático. Todos los derechos reservados. Developed by Daniel Uohnson</p>
            <p>Contacto: contacto@noticiasinformaticas.com</p>
        </div>
        <div class="footer-derecha cartas">
            <div class="redes-sociales-footer">
                <a href="https://www.facebook.com/" target="_blank">
                    <img src="src/static/img/face.png" alt="Facebook" class="face">
                </a>
                <a href="https://twitter.com/" target="_blank">
                    <img src="src/static/img/x.png" alt="Twitter" class="x">
                </a>
                <a href="https://www.instagram.com/" target="_blank">
                    <img src="src/static/img/insta.png" alt="Instagram" class="insta">
                </a>
            </div>
            <div class="suscribirse">
                <h3>Suscríbete a nuestro boletín</h3>
                <form action="suscripcion.php" method="post">
                    <input type="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                    <button class="suscribirse-boton" type="submit">Suscribirse</button>
                </form>
            </div>
        </div>
    </footer>
</body>
</html>

