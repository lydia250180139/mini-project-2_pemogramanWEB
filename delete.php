<?php

session_start();

require_once "../config/db.php";


/* Hanya menerima POST */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit;

}


/* Cek ID */

$id = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id) {

    header("Location: index.php");
    exit;

}


/* Cek CSRF */

$csrf = $_POST["csrf"] ?? "";

if (
    empty($_SESSION["csrf"]) ||
    !hash_equals($_SESSION["csrf"], $csrf)
) {

    http_response_code(403);

    exit("Token CSRF tidak valid.");

}


/* DELETE dengan prepared statement */

$stmt = $pdo->prepare(
    "DELETE FROM products
     WHERE id = :id"
);

$stmt->execute([
    "id" => $id
]);


/* Redirect */

header(
    "Location: index.php?status=deleted"
);

exit;