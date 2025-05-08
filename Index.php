<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="LogIn.css">
</head>
<body>
    <form action="Login.php" method="post">
        <h2>LOGIN</h2>

        <?php if(isset($_GET['error'])) { ?>
            <p class="error"><?php echo $_GET['error']; ?></p>
        <?php } ?>

        <label>Username</label>
        <input type="text" name="username" placeholder="Enter Username" required><br>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter Password" required><br>

        <button type="submit">Login</button>
        <a href="Login.php"><b></b></a>
    </form>
</body>
</html>
