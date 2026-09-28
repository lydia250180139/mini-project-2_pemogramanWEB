<?php

require_once "../config/db.php";


/* Ambil ID */

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id) {

    header("Location: index.php");
    exit;

}


/* Ambil produk berdasarkan ID */

$stmt = $pdo->prepare(
    "SELECT *
     FROM products
     WHERE id = :id"
);

$stmt->execute([
    "id" => $id
]);

$product = $stmt->fetch();


if (!$product) {

    header("Location: index.php");
    exit;

}


/* Nilai awal */

$name = $product["name"];
$category = $product["category"];
$price = $product["price"];
$stock = $product["stock"];

$errors = [];


/* PROSES UPDATE */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

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


    /* VALIDASI NAMA */

    if (mb_strlen($name) < 3) {

        $errors["name"] =
            "Nama produk minimal 3 karakter.";

    }


    /* VALIDASI KATEGORI */

    if (
        !in_array(
            $category,
            ["Laptop", "iPad", "Aksesoris"],
            true
        )
    ) {

        $errors["category"] =
            "Kategori produk tidak valid.";

    }


    /* VALIDASI HARGA */

    if (
        $price === false ||
        $price === null ||
        $price <= 0
    ) {

        $errors["price"] =
            "Harga harus lebih dari 0.";

    }


    /* VALIDASI STOK */

    if (
        $stock === false ||
        $stock === null ||
        $stock < 0
    ) {

        $errors["stock"] =
            "Stok tidak boleh negatif.";

    }


    /* CEK NAMA UNIK */

    if (!isset($errors["name"])) {

        $stmt = $pdo->prepare(
            "SELECT id
             FROM products
             WHERE name = :name
             AND id != :id"
        );

        $stmt->execute([
            "name" => $name,
            "id" => $id
        ]);

        if ($stmt->fetch()) {

            $errors["name"] =
                "Nama produk sudah digunakan.";

        }

    }


    /* UPDATE */

    if (empty($errors)) {

        $stmt = $pdo->prepare(
            "UPDATE products
             SET
                name = :name,
                category = :category,
                price = :price,
                stock = :stock
             WHERE id = :id"
        );

        $stmt->execute([
            "name" => $name,
            "category" => $category,
            "price" => $price,
            "stock" => $stock,
            "id" => $id
        ]);


        /* PRG */

        header(
            "Location: index.php?status=updated"
        );

        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Produk - StockElectro</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="form-page">

    <div class="form-card">

        <div class="form-header">

            <span>
                ✦ STOCKELECTRO
            </span>

            <h1>
                Edit Produk
            </h1>

            <p>
                Perbarui informasi produk yang
                tersimpan di dalam sistem.
            </p>

        </div>


        <?php if (!empty($errors)): ?>

            <div class="error-box">

                <strong>
                    ⚠ Periksa kembali data:
                </strong>

                <ul>

                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= htmlspecialchars(
                                $error,
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <form
            action="edit.php?id=<?= (int) $id ?>"
            method="POST"
            class="product-form"
        >


            <!-- NAMA -->

            <label for="name">
                Nama Produk
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="<?= htmlspecialchars(
                    $name,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>"
                minlength="3"
                required
            >


            <?php if (isset($errors["name"])): ?>

                <small class="field-error">
                    <?= htmlspecialchars(
                        $errors["name"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </small>

            <?php endif; ?>


            <!-- KATEGORI -->

            <label for="category">
                Kategori
            </label>

            <select
                id="category"
                name="category"
                required
            >

                <option
                    value="Laptop"
                    <?= $category === "Laptop"
                        ? "selected"
                        : "" ?>
                >
                    💻 Laptop
                </option>

                <option
                    value="iPad"
                    <?= $category === "iPad"
                        ? "selected"
                        : "" ?>
                >
                    📱 iPad
                </option>

                <option
                    value="Aksesoris"
                    <?= $category === "Aksesoris"
                        ? "selected"
                        : "" ?>
                >
                    🎧 Aksesoris
                </option>

            </select>


            <?php if (isset($errors["category"])): ?>

                <small class="field-error">
                    <?= htmlspecialchars(
                        $errors["category"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </small>

            <?php endif; ?>


            <!-- HARGA -->

            <label for="price">
                Harga
            </label>

            <div class="input-prefix">

                <span>Rp</span>

                <input
                    type="number"
                    id="price"
                    name="price"
                    value="<?= htmlspecialchars(
                        (string) $price,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                    min="1"
                    step="0.01"
                    required
                >

            </div>


            <?php if (isset($errors["price"])): ?>

                <small class="field-error">
                    <?= htmlspecialchars(
                        $errors["price"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </small>

            <?php endif; ?>


            <!-- STOK -->

            <label for="stock">
                Jumlah Stok
            </label>

            <input
                type="number"
                id="stock"
                name="stock"
                value="<?= htmlspecialchars(
                    (string) $stock,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>"
                min="0"
                required
            >


            <?php if (isset($errors["stock"])): ?>

                <small class="field-error">
                    <?= htmlspecialchars(
                        $errors["stock"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </small>

            <?php endif; ?>


            <!-- BUTTON -->

            <div class="form-actions">

                <a
                    href="index.php"
                    class="cancel"
                >
                    ← Kembali
                </a>

                <button
                    type="submit"
                    class="save"
                >
                    ✓ Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>