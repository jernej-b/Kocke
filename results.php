<?php
session_start();

if (!isset($_SESSION['users']) || !isset($_SESSION['total_scores'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezultati igre</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Rezultati igre</h1>
    <h2>Končni rezultati</h2>
    <?php
    $max_score = max($_SESSION['total_scores']);
    $winners = array_filter($_SESSION['users'], function($user, $id) use ($max_score) {
        return isset($_SESSION['total_scores'][$id]) && $_SESSION['total_scores'][$id] == $max_score;
    }, ARRAY_FILTER_USE_BOTH);
    $winners_names = array_map(function($winner) {
        return $winner['ime'];
    }, $winners);
    ?>
    <div class="winner">
        Zmagovalec(-ci): <?php echo htmlspecialchars(implode(', ', $winners_names)); ?> (<?php echo $max_score; ?> točk)
    </div>

    <h2>Podrobnosti</h2>
    <div class="players">
        <?php foreach ($_SESSION['users'] as $id => $user): ?>
            <div class="user">
                <h3><?php echo htmlspecialchars($user['ime']); ?></h3>
                <?php for ($round = 1; $round <= $_SESSION['rounds']; $round++): ?>
                    <?php if (isset($_SESSION['results'][$id][$round])): ?>
                        <p>Krog <?php echo $round; ?>: <?php echo $_SESSION['results'][$id][$round]['sum']; ?></p>
                    <?php endif; ?>
                <?php endfor; ?>
                <p>Skupne točke: <?php echo isset($_SESSION['total_scores'][$id]) ? $_SESSION['total_scores'][$id] : 0; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Preusmeritev po 10 sekundah
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 10000);
    </script>
    <?php
    // Počistimo sejo
    session_unset();
    session_destroy();
    ?>
</body>
</html>