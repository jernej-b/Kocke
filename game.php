<?php
session_start();

// Initialize session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['users']) && isset($_POST['rounds'])) {
    $_SESSION['users'] = $_POST['users']; // [id => ['ime' => string]]
    $_SESSION['rounds'] = (int)$_POST['rounds'];
    $_SESSION['current_round'] = 1;
    $_SESSION['current_player'] = 1;
    $_SESSION['results'] = []; // [id => [round => ['dice' => int, 'sum' => int]]]
    $_SESSION['total_scores'] = []; // [id => int]
    $_SESSION['last_updated'] = null; // Track last updated player
    $_SESSION['next_player'] = null; // Temporary storage for next player
} elseif (!isset($_SESSION['users']) || !isset($_SESSION['rounds'])) {
    header("Location: index.php");
    exit;
}

// Process throw
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['throw'])) {
    $user_id = $_SESSION['current_player'];
    $current_round = $_SESSION['current_round'];
    
    // Generate throw (1 die, 6 sides)
    $dice = rand(1, 6);
    
    // Store result
    $_SESSION['results'][$user_id][$current_round] = [
        'dice' => $dice,
        'sum' => $dice
    ];
    
    // Update total score
    if (!isset($_SESSION['total_scores'][$user_id])) {
        $_SESSION['total_scores'][$user_id] = 0;
    }
    $_SESSION['total_scores'][$user_id] += $dice;
    
    // Mark last updated player
    $_SESSION['last_updated'] = $user_id;
    
    // Determine next player, but don't update current_player yet
    $_SESSION['next_player'] = $user_id + 1;
    $next_round = $current_round;
    if ($_SESSION['next_player'] > count($_SESSION['users'])) {
        $_SESSION['next_player'] = 1;
        $next_round++;
    }
    
    // Check for game end
    if ($next_round > $_SESSION['rounds'] && 
        isset($_SESSION['results'][count($_SESSION['users'])][$_SESSION['rounds']])) {
        header("Location: results.php");
        exit;
    }
}

// Update player after delay
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_player']) && isset($_SESSION['next_player'])) {
    $_SESSION['current_player'] = $_SESSION['next_player'];
    $_SESSION['current_round'] = isset($_SESSION['results'][count($_SESSION['users'])][$_SESSION['current_round']]) ? $_SESSION['current_round'] + 1 : $_SESSION['current_round'];
    $_SESSION['next_player'] = null; // Clear temporary variable
}
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
    <h1>Dice Game</h1>
    <h2>Round: <?php echo $_SESSION['current_round']; ?> / <?php echo $_SESSION['rounds']; ?></h2>

    <h2>Players</h2>
    <div class="players">
        <?php foreach ($_SESSION['users'] as $id => $user) { ?>
            <div class="user <?php echo $id == $_SESSION['current_player'] ? 'active' : ''; ?>">
                <h3><?php echo htmlspecialchars($user['ime']); ?></h3>
                <div class="dice-squares">
                    <?php for ($round = 1; $round <= $_SESSION['rounds']; $round++) { ?>
                        <div class="dice-square <?php echo !isset($_SESSION['results'][$id][$round]) ? 'unknown' : ''; ?>">
                            <?php if (isset($_SESSION['results'][$id][$round])) { ?>
                                <img src="images/kocka<?php echo $_SESSION['results'][$id][$round]['dice']; ?>.gif" 
                                     alt="Dice <?php echo $_SESSION['results'][$id][$round]['dice']; ?>">
                            <?php } else { ?>
                                ?
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <p class="score <?php echo $_SESSION['last_updated'] == $id ? 'score-update' : ''; ?>">
                    Total Score: <?php echo isset($_SESSION['total_scores'][$id]) ? $_SESSION['total_scores'][$id] : 0; ?>
                </p>
            </div>
        <?php } ?>
    </div>

    <form method="post" id="throw-form">
        <button type="submit" name="throw" id="throw-button">Throw</button>
    </form>

    <!-- Hidden form for updating player after delay -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['throw']) && isset($_SESSION['next_player'])) { ?>
        <form id="updatePlayerForm" method="post">
            <input type="hidden" name="update_player" value="1">
        </form>
    <?php } ?>

    <!-- Dice throw sound -->
    <audio id="diceSound" src="sounds/met.mp3" preload="auto"></audio>

    <script>
        // Update player after delay and play sound on throw
        document.addEventListener('DOMContentLoaded', function() {
            const throwButton = document.getElementById('throw-button');
            const diceSound = document.getElementById('diceSound');

            // Check if sound is loaded
            diceSound.addEventListener('loadeddata', function() {
                console.log('Sound met.mp3 loaded successfully');
            });
            diceSound.addEventListener('error', function(e) {
                console.error('Error loading sound met.mp3:', e);
            });

            // Play sound on "Throw" button click
            throwButton.addEventListener('click', function() {
                try {
                    diceSound.currentTime = 0; // Reset sound to start
                    diceSound.play().catch(function(error) {
                        console.error('Error playing sound:', error);
                    });
                } catch (error) {
                    console.error('Error attempting to play sound:', error);
                }
            });

            // Update player after delay
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['throw']) && isset($_SESSION['next_player'])) { ?>
                setTimeout(function() {
                    const updateForm = document.getElementById('updatePlayerForm');
                    if (updateForm) {
                        updateForm.submit();
                    }
                }, 500);
            <?php } ?>
        });
    </script>
</body>
</html>