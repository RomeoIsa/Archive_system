<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
<?php
session_start();
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow" style="width: 400px;">

    <h4 class="text-center mb-3">Let's get started</h4>

    <form method="POST" action="process_register.php">

  <!-- STEP 1 -->
      <div id="step1">
        <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>

        <input type="email" id="reg_email" name="email" class="form-control mb-3" placeholder="Email" required>

        <div class="position-relative">
          <input id="reg_password" type="password" name="password" class="form-control mb-3 pe-5" placeholder="Password" required>
          <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y" style="margin-right: 6px;" onclick="toggleRegPassword()" id="toggleRegPasswordBtn" aria-label="Toggle password visibility">
            <i class="bi bi-eye" id="regPasswordIcon"></i>
          </button>
        </div>

        <div id="registerError" class="text-danger mb-2" style="display:none;"></div>

        <?php if (!empty($_SESSION['register_error'])): ?>
          <div class="alert alert-danger mt-2 mb-2"><?= htmlspecialchars($_SESSION['register_error']); ?></div>
          <?php unset($_SESSION['register_error']); ?>
        <?php endif; ?>

        <button type="button" class="btn btn-primary w-100" onclick="checkExistingAndNext()">Next</button>
      </div>

      <!-- STEP 2 -->
      <div id="step2" style="display:none;">

        <select name="role" id="role" class="form-control mb-3" onchange="toggleLevel()" required>
          <option value="">Select Role</option>
          <option value="student">Student</option>
          <option value="staff">Staff</option>
        </select>

        <select name="institution" class="form-control mb-3" required>
          <option value="">Select Institution</option>
          <option value="1">University of Lagos</option>
          <option value="2">University of Ibadan</option>
          <option value="3">Babcock University</option>
          <option value="4">Covenant University</option>
          <option value="5">Caleb University</option>
          <option value="6">Nile University</option>
        </select>

        <select name="level" id="level" class="form-control mb-3">
          <option value="">Select Level</option>
          <option value="100">100</option>
          <option value="200">200</option>
          <option value="300">300</option>
          <option value="400">400</option>
          <option value="500">500</option>
          <option value="600">600</option>
        </select>

        <button type="button" class="btn btn-secondary mb-2 w-100" onclick="prevStep()">Back</button>

        <button type="submit" class="btn btn-success w-100">Create Account</button>
      </div>

      <div class="mt-3 text-center">
        <small class="text-muted">
          Already have an account?
          <a href="login.php" class="text-decoration-none">Login here</a>
        </small>
      </div>

    </form>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.js"></script>
<script>
function nextStep() {
  document.getElementById("step1").style.display = "none";
  document.getElementById("step2").style.display = "block";
}

function prevStep() {
  document.getElementById("step1").style.display = "block";
  document.getElementById("step2").style.display = "none";
}

function toggleRegPassword() {
  const pwd = document.getElementById('reg_password');
  const btn = document.getElementById('toggleRegPasswordBtn');
  const isHidden = pwd.type === 'password';

  pwd.type = isHidden ? 'text' : 'password';
  const icon = document.getElementById('regPasswordIcon');
  icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
}

function checkExistingAndNext() {
  const email = document.getElementById('reg_email').value.trim();
  const errorDiv = document.getElementById('registerError');

  errorDiv.style.display = 'none';
  errorDiv.textContent = '';

  if (!email) {
    errorDiv.textContent = 'Email is required';
    errorDiv.style.display = 'block';
    return;
  }

  fetch('process_check_user.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'email=' + encodeURIComponent(email)
  })
  .then(r => r.json())
  .then(data => {
    if (data && data.exists) {
      errorDiv.textContent = 'User already exists, please login.';
      errorDiv.style.display = 'block';
      document.getElementById("step1").style.display = "block";
      document.getElementById("step2").style.display = "none";
      return;
    }
    nextStep();
  })
  .catch(() => {
    errorDiv.textContent = 'Could not check user. Please try again.';
    errorDiv.style.display = 'block';
  });
}

function toggleLevel() {
  let role = document.getElementById("role").value;
  let level = document.getElementById("level");

  if (role === "student") {
    level.style.display = "block";
  } else {
    level.style.display = "none";
  }
}
</script>

</body>
</html>