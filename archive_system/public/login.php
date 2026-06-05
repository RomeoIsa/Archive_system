<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

      <div class="position-relative">
        <input 
          type="password" 
          name="password" 
          id="login_password"
          class="form-control mb-3 pe-5" 
          placeholder="Password" 
          required
        >
        <button type="button" class="btn btn-light position-absolute top-50 end-0 translate-middle-y" style="margin-right: 6px;" onclick="toggleLoginPassword()" id="toggleLoginPasswordBtn" aria-label="Toggle password visibility">
          <i class="bi bi-eye" id="loginPasswordIcon"></i>
        </button>
      </div>

      <button class="btn btn-primary w-100" type="submit">Login</button>

    </form>

    <p class="text-center mt-3">
      Don't have an account? <a href="register.php">Register</a>
    </p>

  </div>
</div>

<?php unset($_SESSION['old_email']); ?>


<script>
  function toggleLoginPassword() {
    const pwd = document.getElementById('login_password');
    const icon = document.getElementById('loginPasswordIcon');


    const isHidden = pwd.type === 'password';
    pwd.type = isHidden ? 'text' : 'password';
    icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
  }
</script>

</body>
</html>
