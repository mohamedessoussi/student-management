<?php
// ============================================================
// auth/logout.php
// Destroys the session and returns the user to the login page
// ============================================================

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Validator.php';
require_once __DIR__ . '/../classes/User.php';

use Classes\User;

(new User())->logout();
// logout() itself redirects; nothing else needed here
