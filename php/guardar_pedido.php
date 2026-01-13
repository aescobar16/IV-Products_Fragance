<?php
// ------------------------------
// CONEXIÓN A LA BASE DE DATOS
// ------------------------------
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "iv_productos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// ------------------------------
// RECIBIR DATOS DEL FORMULARIO
// ------------------------------
$nombre_cliente = $_POST['nombre_cliente'] ?? '';
$telefono       = $_POST['telefono'] ?? '';
$direccion      = $_POST['direccion'] ?? '';
$ciudad         = $_POST['ciudad'] ?? '';
$productos      = $_POST['productos'] ?? ''; // JSON del carrito
$cantidad       = $_POST['cantidad'] ?? 0;
$metodo_pago    = $_POST['metodo_pago'] ?? '';
$valor_total    = $_POST['valor_total'] ?? 0;
$fecha          = date("Y-m-d H:i:s");

// ------------------------------
// MANEJO DEL COMPROBANTE
// ------------------------------
$comprobante = "";

if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === 0) {
    $directorio = "uploads/";

    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombreArchivo = time() . "_" . basename($_FILES["comprobante"]["name"]);
    $ruta = $directorio . $nombreArchivo;

    if (move_uploaded_file($_FILES["comprobante"]["tmp_name"], $ruta)) {
        $comprobante = $ruta;
    }
}

// ------------------------------
// INSERTAR EN LA BASE DE DATOS
// ------------------------------
$sql = "INSERT INTO pedidos (
            nombre_cliente,
            telefono,
            direccion,
            ciudad,
            productos,
            cantidad,
            metodo_pago,
            valor_total,
            comprobante,
            fecha
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssssisdss",
    $nombre_cliente,
    $telefono,
    $direccion,
    $ciudad,
    $productos,
    $cantidad,
    $metodo_pago,
    $valor_total,
    $comprobante,
    $fecha
);

// ------------------------------
// EJECUCIÓN
// ------------------------------
if ($stmt->execute()) {
    echo "<h2>✅ Gracias por tu compra, $nombre_cliente</h2>";
    echo "<p>Tu pedido fue registrado correctamente.</p>";
    echo "<a href='index.html'>Volver a la tienda</a>";
} else {
    echo "❌ Error al guardar el pedido: " . $stmt->error;
}

// ------------------------------
$stmt->close();
$conn->close();
?>
