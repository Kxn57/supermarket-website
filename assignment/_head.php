<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="shortcut icon" href="/photos/logo2.png">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
    <?php
    $is_admin = $_user?->role == 'Admin';
    $count = count(get_cart());
    $user_photo = $_user?->photo ? "/photos/{$_user->photo}" : '/images/photo.jpg';
    ?>

    <div id="info"><?= temp('info') ?></div>

    <header class="site-header">
        <div class="header-top">
            <a href="/" class="brand">
                <img src="/photos/logo2.png" alt="Fresh Mart logo">
                <div>
                    <h2>Fresh Mart</h2>
                    <p>Simple grocery shopping</p>
                </div>
            </a>

            <div class="header-tools">
                <form action="/product/list.php" class="header-search">
                    <input type="search" name="name" placeholder="Search products">
                    <button>Search</button>
                </form>

                <?php if ($_user): ?>
                    <a href="/user/profile.php" class="header-user-card" title="View profile">
                        <img src="<?= $user_photo ?>" alt="<?= encode($_user->name) ?>">
                        <div class="header-user-meta">
                            <strong><?= encode($_user->name) ?></strong>
                            <span><?= encode($_user->role) ?></span>
                        </div>
                    </a>
                <?php endif ?>
            </div>
        </div>

        <nav class="main-nav">
            <a href="/">Home</a>
            <a href="/product/list.php">Product List</a>

            <?php if ($is_admin): ?>
                <a href="/admin/product_list.php">Manage Products</a>
                <a href="/admin/member_list.php">Members</a>
                <a href="/admin/order_list.php">Orders</a>
            <?php endif ?>

            <?php if ($_user?->role == 'Member'): ?>
                <a href="/order/history.php">Order History</a>
                <a href="/product/list.php?view=favorite">Favorites</a>
            <?php endif ?>

            <?php if (!$is_admin): ?>
                <a href="/order/cart.php">Cart<?= $count ? " ($count)" : '' ?></a>
            <?php endif ?>

            <div></div>

            <?php if ($_user): ?>
                <a href="/user/profile.php">Profile</a>
                <a href="/user/password.php">Password</a>
                <a href="/logout.php">Logout</a>
            <?php else: ?>
                <a href="/user/register.php">Register</a>
                <a href="/login.php">Login</a>
            <?php endif ?>
        </nav>
    </header>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>
