<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === 'madecars2026') {
        $_SESSION['admin_logged'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Contraseña incorrecta";
    }
}
?>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | MadeCars</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'brand-red': '#DC2626',
                        'brand-black': '#000000',
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-principal: #000000;
            --texto-principal: #ffffff;
        }
        html.light-mode {
            --bg-principal: #ffffff;
            --texto-principal: #1f2937;
        }
        body {
            background-color: var(--bg-principal);
            color: var(--texto-principal);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <form method="POST" class="bg-brand-black dark:bg-brand-black border-2 border-brand-red p-8 rounded-2xl shadow-2xl w-96 max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-brand-red font-montserrat mb-2">MADECARS</h1>
            <h2 class="text-xl font-bold text-white">Panel de Administración</h2>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-900/30 border border-red-600 text-red-400 px-4 py-3 rounded-lg mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="mb-6">
            <label class="block text-gray-400 text-sm mb-2">Contraseña</label>
            <input type="password" name="password" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-brand-red transition" required>
        </div>
        
        <button type="submit" class="w-full bg-brand-red hover:bg-red-700 text-white font-bold py-3 rounded-lg transition-all duration-300 font-montserrat">
            <i class="fas fa-sign-in-alt mr-2"></i> Entrar
        </button>
        
        <p class="text-gray-500 text-xs text-center mt-4">© 2026 MadeCars</p>
    </form>
</body>
</html>