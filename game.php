<?php
session_start();

// Inicializacija seje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['users']) && isset($_POST['rounds'])) {
    $_SESSION['users'] = $_POST['users']; // [id => ['ime' => string]]
    $_SESSION['rounds'] = (int)$_POST['rounds'];
    $_SESSION['current_round'] = 1;
    $_SESSION['current_player'] = 1;
    $_SESSION['results'] = []; // [id => [round => ['dice' => int, 'sum' => int]]]
    $_SESSION['total_scores'] = []; // [id => int]
    $_SESSION['last_updated'] = null; // Sledi zadnjemu posodobljenemu igralcu
} elseif (!isset($_SESSION['users']) || !isset($_SESSION['rounds'])) {
    header("Location: index.php");
    exit;
}

// Obdelava meta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['throw'])) {
    $user_id = $_SESSION['current_player'];
    $current_round = $_SESSION['current_round'];
    
    // Generiranje meta (1 kocka)
    $dice = rand(1, 6);
    
    // Shranjevanje rezultata
    $_SESSION['results'][$user_id][$current_round] = [
        'dice' => $dice,
        'sum' => $dice
    ];
    
    // Posodobitev skupnih točk
    if (!isset($_SESSION['total_scores'][$user_id])) {
        $_SESSION['total_scores'][$user_id] = 0;
    }
    $_SESSION['total_scores'][$user_id] += $dice;
    
    // Označevanje zadnjega posodobljenega igralca
    $_SESSION['last_updated'] = $user_id;
    
    // Posodobitev naslednjega igralca
    $_SESSION['current_player']++;
    if ($_SESSION['current_player'] > count($_SESSION['users'])) {
        $_SESSION['current_player'] = 1;
        $_SESSION['current_round']++;
    }
    
    // Preverjanje konca igre
    if ($_SESSION['current_round'] > $_SESSION['rounds'] && 
        isset($_SESSION['results'][count($_SESSION['users'])][$_SESSION['rounds']])) {
        header("Location: results.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Igra s kockami</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Igra s kockami</h1>
    <h2>Krog: <?php echo $_SESSION['current_round']; ?> / <?php echo $_SESSION['rounds']; ?></h2>

    <div class="dice-area">
        <img src="images/met.gif" 
             <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['throw'])): ?>
                 data-final="images/kocka<?php echo $_SESSION['results'][$_SESSION['current_player'] == 1 ? count($_SESSION['users']) : $_SESSION['current_player'] - 1][$_SESSION['current_round']]['dice']; ?>.png"
             <?php endif; ?>
             alt="Met" id="dice-image">
    </div>
    <form method="post">
        <button type="submit" name="throw">Met</button>
    </form>

    <h2>Igralci</h2>
    <div class="players">
        <?php foreach ($_SESSION['users'] as $id => $user): ?>
            <div class="user <?php echo $id == $_SESSION['current_player'] ? 'active' : ''; ?>">
                <h3><?php echo htmlspecialchars($user['ime']); ?></h3>
                <?php for ($round = 1; $round <= $_SESSION['rounds']; $round++): ?>
                    <?php if (isset($_SESSION['results'][$id][$round])): ?>
                        <p>Krog <?php echo $round; ?>: <?php echo $_SESSION['results'][$id][$round]['sum']; ?></p>
                    <?php endif; ?>
                <?php endfor; ?>
                <p class="score <?php echo $_SESSION['last_updated'] == $id ? 'score-update' : ''; ?>">
                    Skupne točke: <?php echo isset($_SESSION['total_scores'][$id]) ? $_SESSION['total_scores'][$id] : 0; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Zamenjaj met.gif s končno sliko kocke
        document.addEventListener('DOMContentLoaded', function() {
            const diceImage = document.getElementById('dice-image');
            if (diceImage.dataset.final) {
                setTimeout(function() {
                    diceImage.src = diceImage.dataset.final;
                }, 1000);
            }
        });
    </script>
</body>
</html>