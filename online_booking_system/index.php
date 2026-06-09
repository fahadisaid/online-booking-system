<?php
require_once 'configuration/auth.php';

if (is_logged_in()) {
    redirect_by_role();
}

header('Location: auth/login.php');
exit;
