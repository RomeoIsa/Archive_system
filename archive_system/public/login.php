<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow" style="width: 400px;">
    
    <h4 class="text-center mb-3">Login</h4>

    <!-- ERROR MESSAGE -->
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger">
        <?php 
          echo $_SESSION['error']; 
          unset($_SESSION['error']); 
        ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="process_login.php">

      <input 
        type="email" 
        name="email" 
        class="form-control mb-3" 
        placeholder="Email"
        value="<?php echo $_SESSION['old_email'] ?? ''; ?>"
        required
      >

      <input 
        type="password" 
        name="password" 
        class="form-control mb-3" 
        placeholder="Password" 
        required
      >

      <button class="btn btn-primary w-100">Login</button>

    </form>

    <p class="text-center mt-3">
      Don't have an account? <a href="register.php">Register</a>
    </p>

  </div>
</div>

<?php unset($_SESSION['old_email']); ?>

</body>
</html>