<?php
session_start();
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulacija igralnih kock</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Simulacija igralnih kock</h1>
    <form action="game.php" method="post">
        <h2>Vnos uporabnikov</h2>
        <div class="players-input">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="player-input">
                    <label>Uporabnik <?php echo $i; ?>: <input type="text" name="users[<?php echo $i; ?>][ime]" required></label>
                </div>
            <?php endfor; ?>
        </div>
        <label>Število krogov: <input type="number" name="rounds" min="1" max="10" required></label>
        <input type="submit" value="Začni">
    </form>
</body>
</html>