<?php
include '../_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $id   = req('id');
    $action = req('action');
    $unit = req('unit');

    if ($action == 'favorite') {
        toggle_favorite($id);
    }
    else {
        update_cart($id, $unit);
    }

    redirect();
}

$name = req('name');
$sort = req('sort', 'default');
$view = req('view');
$favorites = get_favorites();

$orders = [
    'default' => 'id',
    'name_asc' => 'name ASC',
    'name_desc' => 'name DESC',
    'price_asc' => 'price ASC',
    'price_desc' => 'price DESC',
];
$order = $orders[$sort] ?? $orders['default'];

$sql = 'SELECT * FROM product WHERE name LIKE ?';
$params = ["%$name%"];

if ($view == 'favorite') {
    if ($favorites) {
        $placeholders = implode(',', array_fill(0, count($favorites), '?'));
        $sql .= " AND id IN ($placeholders)";
        $params = array_merge($params, $favorites);
    }
    else {
        $sql .= ' AND 1 = 0';
    }
}

$stm = $_db->prepare("$sql ORDER BY $order");
$stm->execute($params);
$arr = $stm->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Product | List';
include '../_head.php';
?>

<style>
    .search-box,
    .empty-box {
        background: #fff;
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .search-box form {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-box p {
        margin: 12px 0 0;
        color: #666;
    }

    #products {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 18px;
    }

    .product {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 16px;
    }

    .product img {
        display: block;
        width: 100%;
        height: 170px;
        object-fit: contain;
        margin-bottom: 12px;
        cursor: pointer;
    }

    .product-name {
        min-height: 48px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .product-price {
        color: #d81f2a;
        font-weight: 700;
        font-size: 20px;
        margin-bottom: 12px;
    }

    .cart-form {
        display: grid;
        gap: 8px;
    }

    .product-actions {
        display: grid;
        gap: 8px;
    }

    .favorite-button {
        min-height: 38px;
        background: #fff1f1;
        color: #d81f2a;
        border: 1px solid #f0c7cb;
    }

    .favorite-button.active {
        background: #d81f2a;
        color: #fff;
        border-color: #d81f2a;
    }

    .qty-picker {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        border: 1px solid #ffffff;
        border-radius: 8px;
        overflow: hidden;
    }

    .qty-picker button,
    .qty-display {
        min-height: 40px;
        border: 0;
        background: #ffffff;
        text-align: center;
        font-size: 20px;
        font-weight: 700;
    }

    .qty-picker button {
        cursor: pointer;
        background: #ffffff;
    }

    .qty-display {
        display: grid;
        place-items: center;
    }

    @media (max-width: 700px) {
        .search-box form {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="search-box">
    <form>
        <?= html_search('name', 'placeholder="Search product name"') ?>
        <?= html_select('sort', [
            'default' => 'Default',
            'name_asc' => 'Name A-Z',
            'name_desc' => 'Name Z-A',
            'price_asc' => 'Price Low-High',
            'price_desc' => 'Price High-Low',
        ], null) ?>
        <?php if ($view): ?>
            <?= html_hidden('view') ?>
        <?php endif ?>
        <button>Search</button>
    </form>
    <p><?= count($arr) ?> product(s) found<?= $view == 'favorite' ? ' in favorites' : '' ?></p>
</div>

<?php if ($arr): ?>
    <div id="products">
        <?php foreach ($arr as $p): ?>
            <?php
            $cart = get_cart();
            $id   = $p->id;
            $unit = $cart[$p->id] ?? 1;
            $is_favorite = in_array($p->id, $favorites);
            ?>
            <div class="product">
                <img src="/products/<?= $p->photo ?>"
                     data-get="/product/detail.php?id=<?= $p->id ?>">

                <div class="product-name"><?= $p->name ?></div>
                <div class="product-price">RM <?= number_format($p->price, 2) ?></div>

                <div class="product-actions">
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $p->id ?>">
                        <input type="hidden" name="action" value="favorite">
                        <button class="favorite-button <?= $is_favorite ? 'active' : '' ?>">
                            <?= $is_favorite ? 'Favorited' : 'Add Favorite' ?>
                        </button>
                    </form>

                    <form method="post" class="cart-form">
                        <input type="hidden" name="id" value="<?= $p->id ?>">
                        <input type="hidden" name="unit" value="<?= $unit ?>">

                        <div class="qty-picker">
                            <button type="button" class="qty-minus">-</button>
                            <div class="qty-display"><?= $unit ?></div>
                            <button type="button" class="qty-plus">+</button>
                        </div>

                        <button>ADD</button>
                    </form>
                </div>
            </div>
        <?php endforeach ?>
    </div>
<?php else: ?>
    <div class="empty-box">
        No products found.
    </div>
<?php endif ?>

<script>
    $('.qty-minus').on('click', e => {
        const form = e.target.form;
        const input = form.unit;
        let value = +input.value || 1;
        value = Math.max(1, value - 1);
        input.value = value;
        $(form).find('.qty-display').text(value);
    });

    $('.qty-plus').on('click', e => {
        const form = e.target.form;
        const input = form.unit;
        let value = +input.value || 1;
        value = Math.min(10, value + 1);
        input.value = value;
        $(form).find('.qty-display').text(value);
    });
</script>

<?php
include '../_foot.php';
