<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dice Game</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Dice Game Simulation</h1>
    <form action="game.php" method="post">
        <h2>Enter Players</h2>
        <div class="players-input">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="player-input">
                    <label>Player <?php echo $i; ?>: <input type="text" name="users[<?php echo $i; ?>][ime]" required></label>
                </div>
            <?php endfor; ?>
        </div>
        <label>Number of Rounds: <input type="number" name="rounds" min="1" max="10" required></label>
        <input type="submit" value="Start">
    </form>
</body>
</html>
