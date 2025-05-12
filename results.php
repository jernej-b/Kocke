<?php
session_start();

// Check session
if (!isset($_SESSION['users']) || !isset($_SESSION['total_scores'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Game Results</h1>
    <h2>Final Results</h2>
    <?php
    // Calculate winner
    $max_score = max($_SESSION['total_scores']);
    $winners = array_filter($_SESSION['users'], function($user, $id) use ($max_score) {
        return isset($_SESSION['total_scores'][$id]) && $_SESSION['total_scores'][$id] == $max_score;
    }, ARRAY_FILTER_USE_BOTH);
    $winners_names = array_map(function($winner) {
        return $winner['ime'];
    }, $winners);
    ?>
    <div class="winner">
        Winner:<br><?php echo htmlspecialchars(strtoupper(implode(', ', $winners_names))); ?>
    </div>

    <h2>Details</h2>
    <div class="players">
        <?php foreach ($_SESSION['users'] as $id => $user) { ?>
            <div class="user">
                <h3><?php echo htmlspecialchars($user['ime']); ?></h3>
                <?php for ($round = 1; $round <= $_SESSION['rounds']; $round++) { ?>
                    <?php if (isset($_SESSION['results'][$id][$round])) { ?>
                        <p>Round <?php echo $round; ?>: <?php echo $_SESSION['results'][$id][$round]['sum']; ?></p>
                    <?php } ?>
                <?php } ?>
                <p>Total Score: <?php echo isset($_SESSION['total_scores'][$id]) ? $_SESSION['total_scores'][$id] : 0; ?></p>
            </div>
        <?php } ?>
    </div>

    <!-- Winner sound -->
    <audio id="winSound" src="sounds/violin_win.wav" autoplay></audio>

    <script>
        // Redirect after 10 seconds
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 10000);
    </script>
    <?php
    // Clear session
    session_unset();
    session_destroy();
    ?>
</body>
</html>