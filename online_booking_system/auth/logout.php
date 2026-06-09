<?php
require_once '../configuration/auth.php';

session_unset();
session_destroy();

header('Location: login.php');
exit;
