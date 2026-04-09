<?php
declare(strict_types=1);

require __DIR__ . '/includes/auth.php';

logoutUser();
header('Location: /index.php');
exit;
