<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === 'madecars2026') { // CAMBIAR ESTA CONTRASEÑA
        $_SESSION['admin_logged'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Contraseña incorrecta";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | MadeCars</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white flex items-center justify-center h-screen">
    <form method="POST" class="bg-gray-900 p-8 rounded-2xl border border-gold/50 shadow-2xl w-96">
        <h2 class="text-2xl font-bold text-gold mb-6 text-center">Panel de Administración</h2>
        <?php if (isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>
        <input type="password" name="password" placeholder="Contraseña" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 mb-4" required>
        <button type="submit" class="w-full bg-gold hover:bg-yellow-500 text-black font-bold py-2 rounded-lg">Entrar</button>
    </form>
</body>
</html>