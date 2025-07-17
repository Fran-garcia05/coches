<?php
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConexion();

$token = $_GET['token'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = password_hash(trim($_POST['new_password']), PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE token_password = ? AND token_expira > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Cambiar contraseña
        $stmt2 = $conn->prepare("UPDATE usuarios SET password = ?, token_password = NULL, token_expira = NULL WHERE token_password = ?");
        $stmt2->bind_param("ss", $newPassword, $token);
        $stmt2->execute();

        $message = "Contraseña actualizada con éxito. <a href='login.php'>Inicia sesión aquí</a>";
    } else {
        $message = "Token inválido o expirado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
<style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f3f4f6;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background-color: #ffffff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #111827;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input[type="email"],
    input[type="password"] {
      padding: 12px;
      font-size: 1rem;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      transition: border-color 0.3s;
    }

    input:focus {
      border-color: #2563eb;
      outline: none;
    }

    button {
      padding: 12px;
      background-color: #2563eb;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #1e3a8a;
    }

    .message {
      text-align: center;
      margin-top: 15px;
      color: crimson;
      font-weight: bold;
    }

    .success {
      color: green;
    }

    a {
      display: block;
      text-align: center;
      margin-top: 15px;
      text-decoration: none;
      color: #2563eb;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
    <div class="container">
    <h2>Restablecer contraseña</h2>
    <?php if (!$message): ?>
    <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="password" name="new_password" placeholder="Nueva contraseña" required>
        <button type="submit">Actualizar</button>
    </form>
    <?php else: ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    </div>
</body>
</html>
