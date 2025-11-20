<?php
$title = "flashcard";
include 'header.php'; ?>
  <link rel="stylesheet" href="../../public/assets/style/flashcard.css" />
</head>

<body>
  <div class="container">

    <button class="btn retour"> &lt; Retour</button>

    <div class="card">
      <h2>Question</h2>
    </div>

    <button class="btn show">Afficher la réponse</button>

    <div class="answers">
      <button class="btn answer">Je sais</button>
      <button class="btn answer">Je ne sais pas</button>
    </div>

    <div class="arrows">
      <button class="btn nav left">&lt;</button>
      <button class="btn nav right">&gt;</button>
    </div>

  </div>
</body>

</html>