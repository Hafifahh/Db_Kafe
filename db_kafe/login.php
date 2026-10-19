<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username == "admin_kafe" && $password == "12345") {

        $_SESSION["login"] = true;
        $_SESSION["username"] = $username;

        header("Location: home.php");
        exit();

    } else {

        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Sistem Informasi Kafe</title>

    <link rel="stylesheet" href="src/style.css">
</head>

<body class="login-body">

    <div class="decor decor1">☕</div>
    <div class="decor decor2">✦</div>
    <div class="decor decor3">☕</div>
    <div class="decor decor4">✦</div>

    <div class="login-wrapper">

        <div class="cafe-logo">
            ☕
        </div>

        <div class="login-box">

            <h1>SISTEM INFORMASI KAFE</h1>

            <div class="line"></div>

            <h2>LOGIN</h2>

            <p>Silakan masuk untuk melanjutkan</p>

            <?php
            if (isset($error)) {
                echo "<div class='error'>$error</div>";
            }
            ?>

            <form method="POST" action="login.php">

                <div class="input-box">
                    <span>👤</span>

                    <input
                        type="text"
                        name="username"
                        placeholder="Username"
                        required>
                </div>

                <div class="input-box">
                    <span>🔒</span>

                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required>
                </div>

                <button type="submit">
                    LOGIN
                </button>

            </form>

        </div>

    </div>

</body>
</html>