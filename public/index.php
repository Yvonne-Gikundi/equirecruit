<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>EquiRecruit — Welcome</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Basic reset */
    * { margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif; }

    body {
      background-color: #f8f9fa;
      color: #333;
    }

    /* Navbar */
    .nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #2c3e50;
      padding: 15px 30px;
      color: #fff;
    }
    .nav h2 { color: #fff; margin: 0; font-size: 24px; }
    .nav nav a {
      color: #fff;
      margin-left: 20px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }
    .nav nav a:hover { color: #e74c3c; }
    .btn { 
      padding: 12px 25px; 
      background-color: #e74c3c; 
      color: #fff; 
      border-radius: 5px; 
      text-decoration: none; 
      font-weight: bold; 
      transition: 0.3s; 
      margin: 5px;
      display: inline-block;
    }
    .btn:hover { background-color: #c0392b; }

    .btn-outline { 
      background-color: transparent; 
      border: 2px solid #e74c3c; 
      color: #e74c3c; 
    }
    .btn-outline:hover { background-color: #e74c3c; color: #fff; }

    /* Hero */
    .hero {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      padding: 80px 50px;
      background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('hero-image.jpg') center/cover no-repeat;
      color: #fff;
      min-height: 70vh;
    }
    .hero-text { max-width: 600px; }
    .hero-text h1 { font-size: 48px; margin-bottom: 20px; }
    .hero-text p { font-size: 20px; margin-bottom: 30px; line-height: 1.5; }

    /* Sections */
    .container { max-width: 1100px; margin: auto; padding: 60px 20px; }
    .container h2 { text-align: center; font-size: 36px; margin-bottom: 40px; color: #2c3e50; }
    .section-text { text-align: center; font-size: 18px; color: #555; margin-bottom: 30px; }

    /* Features */
    .features {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
      gap: 30px;
    }
    .feature-card {
      background-color: #fff;
      flex: 1 1 250px;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .feature-card:hover { 
      transform: translateY(-10px); 
      box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }
    .feature-card h3 { margin-bottom: 15px; color: #e74c3c; }
    .feature-card p { color: #555; }

    /* Media Queries: Responsive */
    @media (max-width: 992px) {
      .hero { flex-direction: column; text-align: center; padding: 60px 20px; }
      .hero-text h1 { font-size: 40px; }
      .hero-text p { font-size: 18px; }
      .features { flex-direction: column; align-items: center; }
    }

    @media (max-width: 600px) {
      .nav { flex-direction: column; align-items: flex-start; padding: 15px; }
      .nav nav { display: flex; flex-direction: column; margin-top: 10px; }
      .nav nav a { margin: 5px 0; }
      .hero-text h1 { font-size: 32px; }
      .hero-text p { font-size: 16px; }
      .feature-card { width: 100%; max-width: 350px; padding: 20px; }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-text">
      <h1>Welcome to EquiRecruit</h1>
      <p>Your smart assistant for fair and efficient job recruitment. Applicants find jobs that match their skills, recruiters find the best candidates.</p>
      <a href="jobs.php" class="btn">Find Jobs</a>
      <a href="auth_register.php" class="btn btn-outline">Post a Job</a>
    </div>
  </section>

  <!-- How It Works -->
  <section class="container">
    <h2>How It Works</h2>
    <div class="features">
      <div class="feature-card">
        <h3>For Applicants</h3>
        <p>Create an account, upload your resume, and apply for jobs with one click.</p>
      </div>
      <div class="feature-card">
        <h3>For Recruiters</h3>
        <p>Post a job and instantly see AI-powered candidate matches to find the best fit.</p>
      </div>
    </div>
  </section>

  <!-- Why Choose Us -->
  <section class="container">
    <h2>Why Choose EquiRecruit?</h2>
    <div class="features">
      <div class="feature-card">
        <h3>AI-Powered</h3>
        <p>Smart algorithms match applicants and jobs efficiently, saving time for everyone.</p>
      </div>
      <div class="feature-card">
        <h3>Fair & Transparent</h3>
        <p>Every application is reviewed without bias and ensures equal opportunity.</p>
      </div>
      <div class="feature-card">
        <h3>User-Friendly</h3>
        <p>An intuitive platform that’s easy for both applicants and recruiters to navigate.</p>
      </div>
    </div>
  </section>

</body>
</html>
