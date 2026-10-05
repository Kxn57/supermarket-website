<?php
include '_base.php';

// ----------------------------------------------------------------------------

$featured = $_db->query('SELECT * FROM product LIMIT 4')->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Fresh Mart';
include '_head.php';
?>

<style>
    main > h1 {
        display: none;
    }

    .hero-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 20px;
    }
</style>

<section class="hero-box">
    <div>
        <h2>Fresh groceries with a simple Jaya Grocer style</h2>
        <div class="hero-buttons">
            <a href="/product/list.php"><button type="button">Shop Now</button></a>

            <?php if ($_user?->role == 'Admin'): ?>
                <a href="/admin/product_list.php"><button type="button">Manage Products</button></a>
            <?php endif ?>
        </div>
    </div>

    <img src="/photos/logo2.png" alt="Fresh Mart logo">
</section>

<section class="simple-box">
    <h2>Top Selling Products</h2>
    <div class="product-grid">
        <?php foreach ($featured as $p): ?>
            <article class="product-card">
                <img src="/products/<?= $p->photo ?>" alt="<?= encode($p->name) ?>">
                <h3><?= $p->name ?></h3>
                <p>Fresh Mart item</p>
                <strong>RM <?= number_format($p->price, 2) ?></strong>
                <a href="/product/detail.php?id=<?= $p->id ?>"><button type="button">View Product</button></a>
            </article>
        <?php endforeach ?>
    </div>
</section>

<?php
include '_foot.php';
