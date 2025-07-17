<?php
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConexion();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Creamos un token simulado y guardamos en la base de datos
        $token = bin2hex(random_bytes(16));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt2 = $conn->prepare("UPDATE usuarios SET token_password = ?, token_expira = ? WHERE email = ?");
        $stmt2->bind_param("sss", $token, $expira, $email);
        $stmt2->execute();

        header("Location: resetpassword.php?token=$token");
        exit;
    } else {
        $message = "El correo no está registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
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
    <h2>Recuperar contraseña</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Tu correo" required>
        <button type="submit">Continuar</button>
    </form>

    <?php if ($message): ?>
        <p style="color:red;"><?php echo $message; ?></p>
    <?php endif; ?>
    </div>
</body>
</html>
