<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow" style="width: 400px;">

    <h4 class="text-center mb-3">Let's get started</h4>

    <form method="POST" action="process_register.php">

      <!-- STEP 1 -->
      <div id="step1">
        <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>

        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>

        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

        <button type="button" class="btn btn-primary w-100" onclick="nextStep()">Next</button>
      </div>
<div class="mt-3 text-center">
    <small class="text-muted">
        Already have an account?
        <a href="login.php" class="text-decoration-none">Login here</a>
    </small>
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

    </form>
  </div>
</div>

<script>
function nextStep() {
  document.getElementById("step1").style.display = "none";
  document.getElementById("step2").style.display = "block";
}

function prevStep() {
  document.getElementById("step1").style.display = "block";
  document.getElementById("step2").style.display = "none";
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