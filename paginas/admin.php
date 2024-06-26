<!DOCTYPE html>
<html lang="es">
  
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOF-Mi cuenta</title>
    <link rel="stylesheet" href="../estilos/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="icon" type="image/x-icon" href="../imagenes/logos/BOFlogo.png">
</head>
<!-- Contenido de la pagina -->
<body>
  <!-- Barra de navegacion -->
  <?php include '../php/header.php'; ?>
 <?php // Verificar si el usuario es un administrador
if (!$esAdmin) {
    // Si el usuario no es un administrador, mostrar un mensaje de alerta y redirigir a la página principal
    echo "<script>alert('No tienes el acceso autorizado.'); window.location.href='../index.php';</script>";
    exit;
}
?>
    <article class="cuadromensajes">
    <h2>¡Bienvenido admin!</h2>
            <p>Estos son los ultimos mensajes de contacto recibidos</p>
        <div class="contacto"> <!-- Cuadrado de mensajes recibidos-->
            
            <!-- Aquí se mostrarán los datos de los mensajes -->
            <div class="recibidos"> <!--Zona de mensajes-->
                <?php
                

                $xmlFile = '../paginas/contacto.xml';

                // Verificar si el archivo XML ha sido modificado desde la última carga
                $lastModifiedTime = isset($_SESSION['lastModifiedTime']) ? $_SESSION['lastModifiedTime'] : 0;
                clearstatcache();
                $fileModifiedTime = filemtime($xmlFile);

                if ($fileModifiedTime > $lastModifiedTime || !isset($_SESSION['xml'])) {
                    // El archivo XML ha sido modificado o no está cargado en la sesión, cargar el nuevo XML
                    $xml = simplexml_load_file($xmlFile);

                    if ($xml) {
                        // Convertir el objeto SimpleXMLElement a un array para serialización
                        $xmlArray = json_decode(json_encode($xml), true);

                        $_SESSION['xml'] = $xmlArray;
                        $_SESSION['lastModifiedTime'] = $fileModifiedTime;
                        $_SESSION['numeroregistros'] = count($xmlArray['envio']);
                        $_SESSION['posicion'] = 0;
                    }
                }

                // Obtener el envío actual basado en la posición almacenada en la sesión
                $envio = isset($_SESSION['xml'], $_SESSION['xml']['envio'][$_SESSION['posicion']]) ? $_SESSION['xml']['envio'][$_SESSION['posicion']] : null;

                // Procesar la navegación mediante formularios y botones
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (isset($_POST['leido']) && $_POST['leido'] === 'marcar como leido') {
                        $posicion = $_SESSION['posicion'];

                        if ($posicion >= 0 && $posicion < $_SESSION['numeroregistros']) {
                            // Eliminar el mensaje del XML
                            $xml = simplexml_load_file($xmlFile);
                            unset($xml->envio[$posicion]);
                            $xml->asXML($xmlFile);

                            // Recargar la sesión con el XML actualizado
                            $xmlArray = json_decode(json_encode($xml), true);
                            $_SESSION['xml'] = $xmlArray;
                            $_SESSION['numeroregistros'] = count($xmlArray['envio']);
                            $_SESSION['posicion'] = 0; // Volver a la primera posición después de eliminar
                
                            // Redirigir a la misma página después de marcar como leído para reflejar el cambio
                            header('Location: ' . $_SERVER['PHP_SELF']);
                            exit;
                        }
                    }

                    if (isset($_POST['direccion'])) {
                        $direccion = $_POST['direccion'];

                        if (isset($_SESSION['posicion']) && isset($_SESSION['numeroregistros'])) {
                            switch ($direccion) {
                                case 'primero':
                                    $_SESSION['posicion'] = 0;
                                    break;
                                case 'anterior':
                                    if ($_SESSION['posicion'] > 0) {
                                        $_SESSION['posicion']--;
                                    }
                                    break;
                                case 'siguiente':
                                    if ($_SESSION['posicion'] < $_SESSION['numeroregistros'] - 1) {
                                        $_SESSION['posicion']++;
                                    }
                                    break;
                                case 'ultimo':
                                    $_SESSION['posicion'] = $_SESSION['numeroregistros'] - 1;
                                    break;
                                default:
                                    break;
                            }
                        }

                        // Redirigir a la misma página después de procesar el formulario para reflejar el cambio
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit;
                    }
                }
                ?>

                <?php if (!empty($envio) && is_array($envio)): ?>
                    <h2>Mensajes recibidos</h2>
                    <hr>
                    <p style="color:Black; font-size:25px"> <strong> Mensaje número <?php echo $_SESSION['posicion'] + 1; ?>
                            de
                            <?php echo $_SESSION['numeroregistros']; ?>
                        </strong>
                    </p>
                    <!-- Mostrar los datos del envío -->
                    <p><strong style="color:Black;">Nombre:</strong>
                        <?php echo isset($envio['nombre']) ? $envio['nombre'] : 'N/A'; ?></p>
                    <p><strong style="color:Black;">Apellidos:</strong>
                        <?php echo isset($envio['apellidos']) ? $envio['apellidos'] : 'N/A'; ?></p>
                    <p><strong style="color:Black;">Correo:</strong>
                        <?php echo isset($envio['correo']) ? $envio['correo'] : 'N/A'; ?></p>
                    <p><strong style="color:Black;">Mensaje:</strong>
                        <?php echo isset($envio['mensaje']) ? $envio['mensaje'] : 'N/A'; ?></p>

                    <!-- Formulario para marcar como leído y eliminar el mensaje -->
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="leido" value="marcar como leido">
                        <button class="marcarleido" type="submit">Marcar como leído</button>
                    </form>

                <?php else: ?>
                    <p>No hay mensajes para leer.</p>
                <?php endif; ?>

                <!-- Botones de navegación -->
                <div class="botonMensajesContainer">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="direccion" value="primero">
                        <button class="botonMensajes" type="submit">← ←</button>
                    </form>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="direccion" value="anterior">
                        <button class="botonMensajes" type="submit">←</button>
                    </form>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="direccion" value="siguiente">
                        <button class="botonMensajes" type="submit">→</button>
                    </form>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="direccion" value="ultimo">
                        <button class="botonMensajes" type="submit">→ →</button>
                    </form>
                </div>
                <br><br>
            </div>
        </div>
</article>
     <!-- Pie de pagina -->
    <footer class="footer" id="footer"></footer>
    <script src="../scripts/footer.js"></script>
    <script src="../scripts/usuarios.js"></script>
  </body>
</html>