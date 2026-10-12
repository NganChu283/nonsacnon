<?php
// Retired internal registration screen. Preserve historical database records.
require __DIR__.'/../includes/auth.php';
require_perm('workshops');
header('Location: workshops.php');
exit;
