<?php

session_start();

require_once "../config/db.php";

/* Membuat token CSRF */
if (!isset($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

/* Mengambil data produk */
$stmt = $pdo->query(
    "SELECT id, name, category, price, stock
     FROM products
     ORDER BY
     CASE category
        WHEN 'Laptop' THEN 1
        WHEN 'iPad' THEN 2
        WHEN 'Aksesoris' THEN 3
        ELSE 4
     END,
     id DESC"
);

$products = $stmt->fetchAll();

/* Kategori */
$kategori = [
    "Laptop" => [],
    "iPad" => [],
    "Aksesoris" => []
];

/* Masukkan produk ke kategorinya */
foreach ($products as $product) {

    if (isset($kategori[$product["category"]])) {
        $kategori[$product["category"]][] = $product;
    }
}

/* Statistik */
$totalProduk = count($products);
$totalLaptop = count($kategori["Laptop"]);
$totalIpad = count($kategori["iPad"]);
$totalAksesoris = count($kategori["Aksesoris"]);

$totalStok = 0;

foreach ($products as $product) {
    $totalStok += (int) $product["stock"];
}

/* Pesan */
$status = $_GET["status"] ?? "";

/* Icon kategori */
$icons = [
    "Laptop" => "💻",
    "iPad" => "📱",
    "Aksesoris" => "🎧"
];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>StockElectro - Manajemen Produk</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <!-- HEADER -->

    <header class="header">

        <div class="header-content">

            <span class="label">
                ✦ ELECTRONIC INVENTORY
            </span>

            <h1>
                Manajemen Stok
                <span>Produk Elektronik</span>
            </h1>

            <p>
                Kelola stok Laptop, iPad, dan Aksesoris
                dengan mudah dalam satu sistem.
            </p>

            <a href="create.php" class="btn-primary">
                ＋ Tambah Produk
            </a>

        </div>

        <div class="header-decoration">

            <div class="glow-circle"></div>

            <div class="device device-laptop">
                💻
            </div>

            <div class="device device-ipad">
                📱
            </div>

            <div class="device device-headset">
                🎧
            </div>

        </div>

    </header>


    <!-- ALERT -->

    <?php if ($status === "created"): ?>

        <div class="alert success">
            ✓ Produk berhasil ditambahkan.
        </div>

    <?php elseif ($status === "updated"): ?>

        <div class="alert success">
            ✓ Produk berhasil diperbarui.
        </div>

    <?php elseif ($status === "deleted"): ?>

        <div class="alert success">
            ✓ Produk berhasil dihapus.
        </div>

    <?php endif; ?>


    <!-- STATISTIK -->

    <section class="stats">

        <div class="stat-card">

            <div class="stat-icon blue">
                ◈
            </div>

            <div>
                <small>Total Produk</small>
                <h2><?= $totalProduk ?></h2>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon purple">
                💻
            </div>

            <div>
                <small>Laptop</small>
                <h2><?= $totalLaptop ?></h2>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon cyan">
                📱
            </div>

            <div>
                <small>iPad</small>
                <h2><?= $totalIpad ?></h2>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon orange">
                🎧
            </div>

            <div>
                <small>Aksesoris</small>
                <h2><?= $totalAksesoris ?></h2>
            </div>

        </div>

    </section>


    <!-- DAFTAR PRODUK -->

    <section class="products">

        <div class="section-title">

            <div>

                <span>INVENTORY</span>

                <h2>
                    Daftar Produk
                </h2>

            </div>

            <p>
                <?= $totalProduk ?> produk tersedia
            </p>

        </div>


        <?php foreach ($kategori as $namaKategori => $items): ?>

            <?php

            if ($namaKategori === "Laptop") {
                $categoryClass = "laptop";
            } elseif ($namaKategori === "iPad") {
                $categoryClass = "ipad";
            } else {
                $categoryClass = "accessory";
            }

            ?>

            <div class="category">

                <!-- JUDUL KATEGORI -->

                <div class="category-title">

                    <div class="category-icon <?= $categoryClass ?>">
                        <?= $icons[$namaKategori] ?>
                    </div>

                    <div>

                        <h3>
                            <?= htmlspecialchars(
                                $namaKategori,
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>
                        </h3>

                        <small>
                            <?= count($items) ?> produk
                        </small>

                    </div>

                </div>


                <!-- PRODUCT LIST -->

                <div class="product-list">

                    <?php if (count($items) > 0): ?>

                        <?php foreach ($items as $product): ?>

                            <div class="product-card">


                                <!-- VISUAL -->

                                <div class="product-visual <?= $categoryClass ?>-bg">

                                    <?php if ($namaKategori === "Laptop"): ?>

                                        <div class="laptop-image">

                                            <div class="laptop-screen">
                                                TECH
                                            </div>

                                            <div class="laptop-base"></div>

                                        </div>

                                    <?php elseif ($namaKategori === "iPad"): ?>

                                        <div class="ipad-image">

                                            <div class="ipad-screen">
                                                iPad
                                            </div>

                                        </div>

                                    <?php else: ?>

                                        <div class="accessory-image">
                                            🎧
                                        </div>

                                    <?php endif; ?>


                                    <span class="badge">
                                        <?= htmlspecialchars(
                                            $namaKategori,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>
                                    </span>

                                </div>


                                <!-- CONTENT -->

                                <div class="product-content">

                                    <h3>
                                        <?= htmlspecialchars(
                                            $product["name"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>
                                    </h3>


                                    <p class="price">
                                        Rp <?= number_format(
                                            $product["price"],
                                            0,
                                            ",",
                                            "."
                                        ) ?>
                                    </p>


                                    <p class="stock">

                                        <span></span>

                                        Stok:

                                        <strong>
                                            <?= (int) $product["stock"] ?>
                                        </strong>

                                    </p>


                                    <!-- ACTION -->

                                    <div class="actions">

                                        <a
                                            href="edit.php?id=<?= (int) $product["id"] ?>"
                                            class="edit"
                                        >
                                            ✎ Edit
                                        </a>


                                        <!-- DELETE POST + CSRF -->

                                        <form
                                            action="delete.php"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus produk ini?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $product["id"] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="csrf"
                                                value="<?= htmlspecialchars(
                                                    $_SESSION["csrf"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="delete"
                                            >
                                                ♲ Hapus
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="empty">

                            <div>
                                <?= $icons[$namaKategori] ?>
                            </div>

                            Belum ada produk
                            <?= htmlspecialchars(
                                $namaKategori,
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </section>


    <!-- FOOTER -->

    <footer>

        <div>

            <strong>
                StockElectro
            </strong>

            <span>
                © 2026
            </span>

        </div>

        <span>
            Sistem Manajemen Produk Elektronik
        </span>

    </footer>

</div>

</body>
</html>