<?php
$password = 'adminpass1';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password\n";
echo "Hash: $hash\n";

// $2y$10$9ctu5QKHOrK07rPAyQ8M5uF4Via6Vblrd566A8wSCkDjSFIvI6D0.
// $2y$10$vA44iqX6AuB5aG.NIzT8HuAtA5A13N3wWFLHqT1Ss/EuN.peIssDu
?>
