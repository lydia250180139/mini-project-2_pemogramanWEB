<?php

session_start();

require_once "../config/db.php";

$_SESSION["csrf"] ??= bin2hex(random_bytes(32));

$name = "";
$category = "";
$price = "";
$stock = "";

$errors = [];


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");

    $category = trim($_POST["category"] ?? "");

    $price = filter_input(
        INPUT_POST,
        "price",
        FILTER_VALIDATE_FLOAT
    );

    $stock = filter_input(
        INPUT_POST,
        "stock",
        FILTER_VALIDATE_INT
    );


    // Validasi nama

    if (mb_strlen($name) < 3) {

        $errors["name"] =
            "Nama minimal 3 karakter.";

    }


    // Validasi kategori

    if ($category == "") {

        $errors["category"] =
            "Kategori wajib diisi.";

    }


    // Validasi harga

    if ($price === false ||
        $price === null ||
        $price <= 0) {

        $errors["price"] =
            "Harga harus lebih dari 0.";

    }


    // Validasi stok

    if ($stock === false ||
        $stock === null ||
        $stock < 0) {

        $errors["stock"] =
            "Stok tidak boleh negatif.";

    }


    // Cek nama duplikat

    if (empty($errors)) {

        $stmt = $pdo->prepare(
            "SELECT id
             FROM products
             WHERE name = :name"
        );

        $stmt->execute([
            "name" => $name
        ]);


        if ($stmt->fetch()) {

            $errors["name"] =
                "Nama produk sudah digunakan.";

        }

    }


    // Simpan data

    if (empty($errors)) {

        $stmt = $pdo->prepare(
            "INSERT INTO products
            (name, category, price, stock)
            VALUES
            (:name, :category, :price, :stock)"
        );


        $stmt->execute([

            "name" => $name,

            "category" => $category,

            "price" => $price,

            "stock" => $stock

        ]);


        // PRG

        header(
            "Location: index.php?status=created"
        );

        exit;

    }

}


function e($value)
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk</title>

    <link rel="stylesheet"
          href="assets/style.css">

</head>


<body>

<div class="form-container">

    <div class="form-card">

        <a href="index.php">
            ← Kembali
        </a>

        <h1>
            Tambah Produk
        </h1>


        <form method="POST">

            <label>
                Nama Produk
            </label>

            <input
                type="text"
                name="name"
                value="<?= e($name) ?>"
                required
            >

            <?php if (isset($errors["name"])): ?>

                <small class="error">
                    <?= e($errors["name"]) ?>
                </small>

            <?php endif; ?>


            <label>
                Kategori
            </label>

            <input
                type="text"
                name="category"
                value="<?= e($category) ?>"
                required
            >

            <?php if (isset($errors["category"])): ?>

                <small class="error">
                    <?= e($errors["category"]) ?>
                </small>

            <?php endif; ?>


            <label>
                Harga
            </label>

            <input
                type="number"
                name="price"
                min="1"
                value="<?= e($price) ?>"
                required
            >

            <?php if (isset($errors["price"])): ?>

                <small class="error">
                    <?= e($errors["price"]) ?>
                </small>

            <?php endif; ?>


            <label>
                Stok
            </label>

            <input
                type="number"
                name="stock"
                min="0"
                value="<?= e($stock) ?>"
                required
            >

            <?php if (isset($errors["stock"])): ?>

                <small class="error">
                    <?= e($errors["stock"]) ?>
                </small>

            <?php endif; ?>


            <button
                type="submit"
                class="btn btn-primary full">

                Simpan Produk

            </button>

        </form>

    </div>

</div>

</body>

</html>