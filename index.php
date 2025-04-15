<?php
$faculties = [
    ["name" => "Faculty of Science", "color" => "#f5a623", "icon" => "https://cdn-icons-png.flaticon.com/128/10296/10296400.png", "link" => "science.php"],
    ["name" => "Faculty of Arts", "color" => "#ff5722", "icon" => "https://cdn-icons-png.flaticon.com/128/2970/2970785.png", "link" => "./art/art.php"],
    ["name" => "Faculty of Medicine", "color" => "#4caf50", "icon" => "https://cdn-icons-png.flaticon.com/512/2866/2866373.png", "link" => "./medicine/medicine.php"],
    ["name" => "Faculty of Law", "color" => "#8e44ad", "icon" => "https://cdn-icons-png.flaticon.com/128/4252/4252296.png", "link" => "./law/law.php"],
    ["name" => "Faculty of Management & Finance", "color" => "#e67e22", "icon" => "https://cdn-icons-png.flaticon.com/128/10857/10857083.png", "link" => "./management/management.php"],
    ["name" => "Faculty of Nursing", "color" => "#27ae60", "icon" => "https://cdn-icons-png.flaticon.com/128/1165/1165602.png", "link" => "./nursing/nursing.php"],
    ["name" => "Faculty of Technology", "color" => "#2980b9", "icon" => "https://cdn-icons-png.flaticon.com/128/8389/8389176.png", "link" => "./technology/technology.php"],
    ["name" => "Students' Information System", "color" => "#0d47a1", "icon" => "https://cdn-icons-png.flaticon.com/512/2649/2649236.png", "link" => "https://sis.cmb.ac.lk"],
    ["name" => "Learning Management System", "color" => "#388e3c", "icon" => "https://cdn-icons-png.flaticon.com/512/2271/2271412.png", "link" => "https://sci.cmb.ac.lk"]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Faculty Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    /* Global styles */
    body {
      background-color: #f4f4f4;
      text-align: center;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }
    .header {
      background-color: #800055;
      color: white;
      padding: 20px;
      font-size: 24px;
      font-weight: bold;
    }
    .faculty-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 20px;
    }
    .faculty-card {
      border-radius: 10px;
      margin: 15px;
      padding: 20px;
      color: white;
      font-size: 20px;
      text-align: center;
      font-weight: bold;
      text-decoration: none;
    }
    .faculty-card img {
      width: 50px;
      height: 50px;
      margin-bottom: 10px;
    }
    /* Divider styling */
    .divider {
      width: 80%;
      height: 2px;
      background-color: #ccc;
      margin: 40px auto;
      border: none;
    }
    /* Social Icons styles */
    ul.example-2 {
      list-style: none;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 20px;
      padding: 0;
    }
    ul.example-2 li.icon-content {
      margin: 0 10px;
      position: relative;
    }
    ul.example-2 li.icon-content .tooltip {
      position: absolute;
      top: -30px;
      left: 50%;
      transform: translateX(-50%);
      color: #fff;
      padding: 6px 10px;
      border-radius: 5px;
      opacity: 0;
      visibility: hidden;
      font-size: 14px;
      transition: all 0.3s ease;
    }
    ul.example-2 li.icon-content:hover .tooltip {
      opacity: 1;
      visibility: visible;
      top: -50px;
    }
    ul.example-2 li.icon-content a {
      position: relative;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      color: #4d4d4d;
      background-color: #fff;
      transition: all 0.3s ease-in-out;
      text-decoration: none;
    }
    ul.example-2 li.icon-content a:hover {
      box-shadow: 3px 2px 45px 0px rgba(0, 0, 0, 0.12);
      color: white;
    }
    ul.example-2 li.icon-content a svg {
      position: relative;
      z-index: 1;
      width: 30px;
      height: 30px;
    }
    ul.example-2 li.icon-content a .filled {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 0;
      background-color: #000;
      transition: all 0.3s ease-in-out;
    }
    ul.example-2 li.icon-content a:hover .filled {
      height: 100%;
    }
    /* Social colors */
    ul.example-2 li.icon-content a[data-social="facebook"] .filled,
    ul.example-2 li.icon-content a[data-social="facebook"] ~ .tooltip {
      background-color: #3b5998;
    }
    ul.example-2 li.icon-content a[data-social="twitter"] .filled,
    ul.example-2 li.icon-content a[data-social="twitter"] ~ .tooltip {
      background-color: #1da1f2;
    }
    ul.example-2 li.icon-content a[data-social="youtube"] .filled,
    ul.example-2 li.icon-content a[data-social="youtube"] ~ .tooltip {
      background-color: #ff0000;
    }
  </style>
</head>
<body>
  <div class="header">Sci-Hub: Faculty Cyberspace</div>
  <div class="faculty-container">
    <?php foreach ($faculties as $faculty): ?>
      <a href="<?= $faculty['link']; ?>" class="btn btn-lg faculty-card" style="background-color: <?= $faculty['color']; ?>; width: 250px;">
        <img src="<?= $faculty['icon']; ?>" alt="<?= $faculty['name']; ?>">
        <div><?= $faculty['name']; ?></div>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Divider Line -->
  <hr class="divider">

  <!-- Social Icons Section -->
  <ul class="example-2">
    <!-- Facebook -->
    <li class="icon-content">
      <a href="https://www.facebook.com/uocfos" target="_blank" aria-label="Facebook" data-social="facebook">
        <div class="filled"></div>
        <svg role="img" viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg">
          <title>Facebook</title>
          <path d="M22.675 0H1.325C.593 0 0 .593 0 1.326v21.348C0 23.407.593 24 1.325 24h11.495v-9.294H9.691v-3.622h3.129V8.413c0-3.1 1.894-4.788 4.659-4.788 
            1.325 0 2.464.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116
            C23.407 24 24 23.407 24 22.674V1.326C24 .593 23.407 0 22.675 0z"/>
        </svg>
      </a>
      <div class="tooltip">Facebook</div>
    </li>
    <!-- Twitter -->
    <li class="icon-content">
      <a href="https://twitter.com/uocfos" target="_blank" aria-label="Twitter" data-social="twitter">
        <div class="filled"></div>
        <svg role="img" viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg">
          <title>Twitter</title>
          <path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 
            1.014-.608 1.794-1.574 2.163-2.723-.949.564-2.005.974-3.127 1.195-.897-.959-2.178-1.559-3.594-1.559
            -2.722 0-4.928 2.204-4.928 4.928 0 .386.045.762.127 1.124-4.094-.205-7.725-2.165-10.158-5.144
            -.424.723-.666 1.561-.666 2.457 0 1.697.863 3.194 2.175 4.074
            -.802-.026-1.558-.246-2.22-.616v.062c0 2.37 1.684 4.348 3.918 4.798
            -.41.111-.843.171-1.287.171-.314 0-.615-.03-.916-.086
            .631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105
            -.395 0-.779-.023-1.158-.067 2.179 1.397 4.768 2.213 7.557 2.213
            9.054 0 14.004-7.496 14.004-13.986 0-.21 0-.423-.015-.635
            .961-.689 1.8-1.56 2.46-2.548l-.047-.02"/>
        </svg>
      </a>
      <div class="tooltip">Twitter</div>
    </li>
    <!-- YouTube -->
    <li class="icon-content">
      <a href="https://www.youtube.com/user/uocfosnews" target="_blank" aria-label="Youtube" data-social="youtube">
        <div class="filled"></div>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-youtube" viewBox="0 0 16 16">
          <path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104
            .022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104
            c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142
            c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26
            a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104
            A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104
            .022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42
            c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"/>
        </svg>
      </a>
      <div class="tooltip">Youtube</div>
    </li>
  </ul>
</body>
</html>
