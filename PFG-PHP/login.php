<?php include('db.php'); session_start(); ?>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password_input = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM members WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $full_name, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password_input, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['role'] = $role;
            echo "Login successful. Welcome, $full_name!";
            // Redirect based on role
            if ($role === 'admin' || $role === 'super_admin') {
                header("Location: admin.php");
            } else {
                header("Location: member_dashboard.php");
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="english">
<head>
    <title>Gym Management System | User Login</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>User Login</h2>
        <form id="loginForm" action="#" method="post" onsubmit="return validateForm()">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" placeholder="Enter Your Email" autocomplete="off" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Your Password" autocomplete="off" required>
            
            <button type="submit">Login</button>
        </form>
        <div class="footer-text">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>

    <script>
        function validateForm() {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if(email === '' || password === '') {
                alert('Please fill in all fields.');
                return false;
            }
            // Simple email format validation
            const emailPattern = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/;
            if(!emailPattern.test(email)) {
                alert('Please enter a valid email address.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>