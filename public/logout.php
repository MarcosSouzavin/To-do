<?php
require __DIR__ . '/../app/auth.php';

start_session();
session_destroy();
session_unset();

header('location: login.php');
exit;
