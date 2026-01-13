<?php
session_start();

/* ==============================
   LOGIN ADMIN
================================ */
$CLAVE_ADMIN = "1040573096"; // CAMBIA ESTA CLAVE

if (!isset($_SESSION['admin'])) {
    if (isset($_POST['clave']) && $_POST['clave'] === $CLAVE_ADMIN) {
        $_SESSION['admin'] = true;
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Login Admin</title>
            <style>
                body {
                    font-family: Arial;
                    background: #f4f6f8;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                form {
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0,0,0,.1);
                    width: 300px;
                }
                input, button {
                    width: 100%;
                    padding: 10px;
                    margin-top: 10px;
                }
                button {
                    background: #222;
                    color: white;
                    border: none;
                    cursor: pointer;
                }
            </style>
        </head>
        <body>
            <form method="POST">
                <h2>Admin IV Productos</h2>
                <input type="password" name="clave" placeholder="Clave admin" required>
                <button>Entrar</button>
            </form>
        </body>
        </html>
        <?php
        exit;
    }
}

/* ==============================
   CONEXIÓN BD
================================ */
$conn = new mysqli("localhost", "root", "", "iv_productos");
if ($conn->connect_error) {
    die("Error de conexión");
}

/* ==============================
   ACEPTAR / RECHAZAR
================================ */
if (isset($_GET['accion'], $_GET['id'])) {
    $id = intval($_GET['id']);
    $estado = ($_GET['accion'] === 'aceptar') ? 'Aceptado' : 'Rechazado';

    $stmt = $conn->prepare("UPDATE pedidos SET estado=? WHERE id=?");
    $stmt->bind_param("si", $estado, $id);
    $stmt->execute();
    $stmt->close();

    // Al cambiar estado, desaparece del admin
    header("Location: admin_pedidos.php");
    exit;
}

/* ==============================
   SOLO PEDIDOS PENDIENTES
================================ */
$result = $conn->query("
    SELECT * FROM pedidos
    WHERE estado = 'Pendiente'
    ORDER BY fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Admin Pedidos</title>
<style>
body {
    font-family: Arial;
    background: #f4f6f8;
    padding: 20px;
}
h1 {
    text-align: center;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}
th, td {
    border: 1px solid #ddd;
    padding: 10px;
    vertical-align: top;
}
th {
    background: #222;
    color: white;
}
.btn {
    padding: 6px 10px;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    display: inline-block;
    margin-bottom: 5px;
}
.aceptar { background: #28a745; }
.rechazar { background: #dc3545; }
.logout {
    float: right;
    text-decoration: none;
    color: #dc3545;
    font-weight: bold;
}
</style>
</head>

<body>

<a class="logout" href="logout.php">Cerrar sesión</a>

<h1>📦 Pedidos Pendientes</h1>

<table>
<tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Dirección</th>
    <th>Productos</th>
    <th>Total</th>
    <th>Pago</th>
    <th>Acción</th>
</tr>

<?php if ($result->num_rows > 0): ?>
<?php while ($p = $result->fetch_assoc()): ?>
<tr>
    <td><?= $p['id'] ?></td>

    <td>
        <?= htmlspecialchars($p['nombre_cliente']) ?><br>
        <?= htmlspecialchars($p['telefono']) ?><br>
        <?= htmlspecialchars($p['ciudad']) ?>
    </td>

    <td><?= htmlspecialchars($p['direccion']) ?></td>

    <td>
        <?php
        $productos = json_decode($p['productos'], true);
        foreach ($productos as $prod) {
            echo "• " . htmlspecialchars($prod['nombre']) .
                 " x" . intval($prod['cantidad']) . "<br>";
        }
        ?>
    </td>

    <td>$<?= number_format($p['valor_total'], 0, ',', '.') ?></td>
    <td><?= htmlspecialchars($p['metodo_pago']) ?></td>

    <td>
        <a class="btn aceptar" href="?accion=aceptar&id=<?= $p['id'] ?>">Aceptar</a>
        <a class="btn rechazar" href="?accion=rechazar&id=<?= $p['id'] ?>">Rechazar</a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="7" style="text-align:center">No hay pedidos pendientes</td>
</tr>
<?php endif; ?>

</table>

</body>
</html>

<?php $conn->close(); ?>
