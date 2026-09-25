<?php

session_start();

require_once "../config/db.php";

$_SESSION["csrf"] ??= bin2hex(random_bytes(32));


// Mengambil semua produk dan mengurutkan berdasarkan kategori
$stmt = $pdo->query(
    "SELECT * FROM products
     ORDER BY
     CASE category
        WHEN 'Laptop' THEN 1
        WHEN 'iPad' THEN 2
        WHEN 'Aksesoris' THEN 3
        ELSE 4
     END,
     name ASC"
);

$products = $stmt->fetchAll();

$status = $_GET["status"] ?? "";


// Fungsi keamanan output
function e($data)
{
    return htmlspecialchars(
        $data,
        ENT_QUOTES,
        "UTF-8"
    );
}


// Kelompokkan produk berdasarkan kategori
$kategori = [
    "Laptop" => [],
    "iPad" => [],
    "Aksesoris" => []
];

foreach ($products as $p) {

    if (isset($kategori[$p["category"]])) {

        $kategori[$p["category"]][] = $p;

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manajemen Stok Produk Galeri Laptop</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>

<div class="container">


    <!-- HEADER -->

    <div class="header">

        <div>

            <h1>Manajemen Stok Produk Galeri Laptop</h1>

            <p>
                Sistem Pengelolaan Produk Elektronik
            </p>

        </div>


        <a
            href="create.php"
            class="btn tambah"
        >
            + Tambah Produk
        </a>

    </div>


    <!-- PESAN -->

    <?php if ($status == "created"): ?>

        <div class="success">
            ✓ Produk berhasil ditambahkan.
        </div>

    <?php elseif ($status == "updated"): ?>

        <div class="success">
            ✓ Produk berhasil diubah.
        </div>

    <?php elseif ($status == "deleted"): ?>

        <div class="success">
            ✓ Produk berhasil dihapus.
        </div>

    <?php endif; ?>


    <!-- KATEGORI LAPTOP -->

    <?php if (!empty($kategori["Laptop"])): ?>

        <div class="category-title">
            💻 Laptop
        </div>


        <div class="table-box">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                    <?php $no = 1; ?>

                    <?php foreach ($kategori["Laptop"] as $p): ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td class="nama">
                                <?= e($p["name"]) ?>
                            </td>

                            <td class="harga">

                                Rp
                                <?= number_format(
                                    $p["price"],
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </td>

                            <td>

                                <span class="stok">
                                    <?= e($p["stock"]) ?>
                                </span>

                            </td>

                            <td>

                                <div class="aksi">

                                    <a
                                        href="edit.php?id=<?= $p["id"] ?>"
                                        class="btn edit"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        action="delete.php"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus produk ini?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $p["id"] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="csrf"
                                            value="<?= e($_SESSION["csrf"]) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn hapus"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>


   

    <!-- KATEGORI AKSESORIS -->

    <?php if (!empty($kategori["Aksesoris"])): ?>

        <div class="category-title">
            🎧 Aksesoris
        </div>


        <div class="table-box">

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                    <?php $no = 1; ?>

                    <?php foreach ($kategori["Aksesoris"] as $p): ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td class="nama">
                                <?= e($p["name"]) ?>
                            </td>

                            <td class="harga">

                                Rp
                                <?= number_format(
                                    $p["price"],
                                    0,
                                    ",",
                                    "."
                                ) ?>

                            </td>

                            <td>

                                <span class="stok">
                                    <?= e($p["stock"]) ?>
                                </span>

                            </td>

                            <td>

                                <div class="aksi">

                                    <a
                                        href="edit.php?id=<?= $p["id"] ?>"
                                        class="btn edit"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        action="delete.php"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus produk ini?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $p["id"] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="csrf"
                                            value="<?= e($_SESSION["csrf"]) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn hapus"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>


</div>

</body>

</html>