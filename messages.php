<?php
date_default_timezone_set('Asia/Kathmandu');

$dbHost = '127.0.0.1';
$dbName = 'feven_portfolio';
$dbUser = 'root';
$dbPass = '';

$pdo = null;
$dbError = '';
$messages = [];

try {
    $pdo = new PDO("mysql:host=$dbHost;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(80) NOT NULL,
        email VARCHAR(120) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $messages = $pdo->query('SELECT id, name, email, message, created_at FROM messages ORDER BY created_at DESC, id DESC')->fetchAll();
} catch (PDOException $error) {
    $pdo = null;
    $dbError = 'The database could not be reached. Make sure MySQL is running in XAMPP, then refresh this page.';
}

function escape($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Messages | Tsion's Portfolio</title>
<meta name="description" content="The message inbox for Tsion's Grade 12 portfolio website.">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%230f2a22'/%3E%3Ctext x='32' y='45' font-family='Arial' font-size='36' font-weight='700' fill='%23e8b45a' text-anchor='middle'%3ET%3C/text%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar site-nav">
  <div class="container">
    <a class="navbar-brand" href="index.php" aria-label="Back to Tsion's portfolio">
      <span class="brand-mark">T</span>
      <span class="brand-text">Tsion<small>Messages</small></span>
    </a>
    <div class="d-flex align-items-center gap-2">
      <a class="btn-ghost d-none d-sm-inline-flex" href="index.php"><i class="bi bi-arrow-left"></i> Portfolio</a>
      <a class="btn-nav" href="index.php#contact"><i class="bi bi-send"></i> Contact</a>
    </div>
  </div>
</nav>

<main class="inbox-section">
  <div class="container">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
      <div>
        <p class="section-label mb-2">Inbox</p>
        <h1 class="inbox-title">Messages</h1>
        <p class="inbox-intro">Every message sent through the contact form appears here.</p>
      </div>
      <div class="inbox-actions">
        <span class="count-pill"><i class="bi bi-envelope me-1"></i> <?= count($messages) ?> <?= count($messages) === 1 ? 'message' : 'messages' ?></span>
        <a class="btn-ghost" href="messages.php"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
      </div>
    </div>

    <?php if ($dbError !== ''): ?>
      <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-triangle me-1"></i> <?= escape($dbError) ?></div>
    <?php elseif (count($messages) === 0): ?>
      <div class="empty-state">
        <div class="empty-icon"><i class="bi bi-inbox"></i></div>
        <h2>No messages yet</h2>
        <p>When someone sends a message from the contact form, it will show up here.</p>
        <a class="btn-main" href="index.php#contact">View Contact Form <i class="bi bi-arrow-right"></i></a>
      </div>
    <?php else: ?>
      <div class="table-card">
        <div class="table-responsive">
          <table class="table messages-table align-middle">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">From</th>
                <th scope="col">Email</th>
                <th scope="col">Message</th>
                <th scope="col">Received</th>
              </tr>
            </thead>
            <tbody>
              <?php $rowNumber = 1; ?>
              <?php foreach ($messages as $message): ?>
                <tr>
                  <td class="row-number"><?= str_pad((string) $rowNumber, 2, '0', STR_PAD_LEFT) ?></td>
                  <td>
                    <div class="sender">
                      <div class="sender-badge"><?= escape(mb_strtoupper(mb_substr($message['name'], 0, 1))) ?></div>
                      <div>
                        <strong><?= escape($message['name']) ?></strong>
                        <span>Website visitor</span>
                      </div>
                    </div>
                  </td>
                  <td class="email-cell"><a href="mailto:<?= escape($message['email']) ?>"><?= escape($message['email']) ?></a></td>
                  <td class="message-cell"><?= nl2br(escape($message['message'])) ?></td>
                  <td class="date-cell">
                    <?= escape(date('d M Y', strtotime($message['created_at']))) ?>
                    <span><?= escape(date('g:i A', strtotime($message['created_at']))) ?></span>
                  </td>
                </tr>
                <?php $rowNumber++; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<footer class="inbox-footer">
  <div class="container d-flex flex-wrap justify-content-between gap-2">
    <span>&copy; <?= $currentYear ?> Tsion. Grade 12 portfolio.</span>
    <a href="index.php">Back to portfolio <i class="bi bi-arrow-right"></i></a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>