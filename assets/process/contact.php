<?php

    // Asegúrate de que se esté utilizando el método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Método no permitido
        echo 'Método no permitido.';
        exit;
    }

    // Asuntos del correo
    $subject_admin = 'Meridiana: Recibiste un consulta desde la web';
    $subject_user = 'Meridiana: Confirmación de recepción de tu consulta';

    // Verifica si los campos requeridos están presentes y no están vacíos
    if (empty($_POST['nombre_completo']) || empty($_POST['telefono']) || empty($_POST['email']) || empty($_POST['mensaje'])) {
        echo('Datos requeridos incompletos.');
        exit;
    }

    // Filtra y valida los datos de entrada para mayor seguridad
    $nombre_completo = htmlspecialchars(strip_tags($_POST['nombre_completo']));
    $telefono = htmlspecialchars(strip_tags($_POST['telefono']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $mensaje = htmlspecialchars(strip_tags($_POST['mensaje']));

    // Si el email no es válido, muestra un error
    if (!$email) {
        echo('Correo electrónico inválido.');
        exit;
    }

    // Plantilla de correo para el administrador
    $contenido_admin = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1' />
        <meta name='viewport' content='width=device-width'>
        <title>Nuevo mensaje de contacto</title>
    </head>
    <body bgcolor='#010101' style='background-color: #010101; padding: 64px 0;'>
        <center>
            <table style='width: 800px; background-color: #F4F4F4; font: 16px/150% 'Inter', 'Segoe UI', 'Helvetica Neue', Helveitca, Arial, sans-serif;'>
                <tr>
                    <td colspan='2' style='padding: 0 60px;'>
                        <img src='https://meridiana.tech/assets/images/logo-color.svg' style='display: block; width: 168px; margin: 64px 0;'/>
                        <h1 style='font-weight: 500; margin-bottom: 64px;'>Contacto</h1>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style='padding: 0 64px;'>
                        <p style='line-height: 150%;'>
                            <strong>NOMBRE COMPLETO:</strong> [[nombre_completo]]<br>
                            <strong>TELÉFONO/CELULAR:</strong> [[telefono]]<br>
                            <strong>EMAIL:</strong> [[email]]<br>
                            <strong>¿EN QUÉ PODEMOS AYUDARTE?:</strong><br>
                            [[mensaje]]
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style='padding: 128px 64px;'>
                        <hr style='margin-bottom: 32px;'>
                        <small>&copy; 2026 Meridiana - Todos los derechos reservados.</small>
                    </td>
                </tr>
            </table>
        </center>
    </body>
    </html>
    ";

    // Plantilla de correo para el usuario
    $contenido_user = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1' />
        <meta name='viewport' content='width=device-width'>
        <title>Confirmación de contacto</title>
    </head>
    <body bgcolor='#010101' style='background-color: #010101; padding: 64px 0;'>
        <center>
            <table style='width: 800px; background-color: #f4f4f4; font: 16px/150% 'Inter', 'Segoe UI', 'Helvetica Neue', Helveitca, Arial, sans-serif;'>
                <tr>
                    <td colspan='2' style='padding: 0 60px;'>
                        <img src='https://meridiana.tech/assets/images/logo-color.svg' style='display: block; width: 168px; margin: 64px 0;'/>
                        <h1 style='font-weight: 500; margin-bottom: 64px;'>Gracias por contactarnos</h1>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style='padding: 0 64px;'>
                        <p style='line-height: 150%;'>
                            Estimado/a [[nombre_completo]],<br>
                            Gracias por ponerte en contacto con nosotros. Hemos recibido tu consulta y te responderemos a la brevedad.<br><br>
                            <strong>Resumen de tu mensaje:</strong><br>
                            [[mensaje]]
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan='2' style='padding: 128px 64px;'>
                        <hr style='margin-bottom: 32px;'>
                        <small>&copy; 2026 Meridiana - Todos los derechos reservados.</small>
                    </td>
                </tr>
            </table>
        </center>
    </body>
    </html>
    ";

    // Encabezados del correo para el administrador
    $headers_admin  = "From: $nombre_completo <$email>\r\n";
    $headers_admin .= "Reply-To: $email\r\n";
    $headers_admin .= "MIME-Version: 1.0\r\n";
    $headers_admin .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Encabezados del correo de confirmación para el usuario
    $headers_user  = "From: Meridiana <info@meridiana.tech>\r\n";
    $headers_user .= "Reply-To: info@meridiana.tech\r\n";
    $headers_user .= "MIME-Version: 1.0\r\n";
    $headers_user .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Enviar correo al administrador
    if (mail('info@meridiana.tech', $subject_admin, $contenido_admin, $headers_admin)) {
        // Enviar correo de confirmación al usuario
        if (mail($email, $subject_user, $contenido_user, $headers_user)) {
            echo 'success';
        } else {
            echo 'Error al enviar el correo de confirmación.';
        }
    } else {
        echo 'Error al enviar el correo al administrador.';
    }

?>
