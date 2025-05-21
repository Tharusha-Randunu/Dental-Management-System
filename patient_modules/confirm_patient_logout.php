<?php
session_start();
session_destroy();
header("Location: ../index.php"); // Redirect to patient login page
exit();
