<?php

session_start();

require_once "../config/db.php";

$_SESSION["csrf"] ??= bin2hex(random_bytes(32));


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");

    exit;

}


$csrf = $_POST["csrf"] ?? "";


if (!hash_equals(
    $_SESSION["csrf"],
    $csrf
)) {

    http_response_code(403);

    exit("Token CSRF tidak valid.");

}


$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);


if (!$id) {

    header("Location: index.php");

    exit;

}


$stmt = $pdo->prepare(
    "DELETE FROM products
     WHERE id = :id"
);

$stmt->execute([
    "id" => $id
]);


header(
    "Location: index.php?status=deleted"
);

exit;

?>