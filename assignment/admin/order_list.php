<?php
include '../_base.php';

auth('Admin');

// ----------------------------------------------------------------------------

$search = req('search');

$stm = $_db->prepare("
    SELECT o.*, u.name, u.email
    FROM `order` AS o
    JOIN user AS u ON u.id = o.user_id
    WHERE o.id LIKE ?
    OR u.name LIKE ?
    OR u.email LIKE ?
    ORDER BY o.id DESC
");
$stm->execute(["%$search%", "%$search%", "%$search%"]);
$arr = $stm->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Admin | Order List';
include '../_head.php';
?>

<style>
    .admin-search-box {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 18px;
    }

    .customer-meta strong,
    .customer-meta span {
        display: block;
    }

    .customer-meta span {
        color: #777;
        font-size: 13px;
    }

    .table-actions {
        display: flex;
        gap: 8px;
    }
</style>

<form class="admin-search-box">
    <?= html_search('search', 'placeholder="Search order id, member name or email"') ?>
    <button>Search</button>
</form>

<p><?= count($arr) ?> order(s)</p>

<style>
    .demo-box {
        width: 70%;
        min-width: 520px;
    }

    .demo-box .table {
        width: 100%;
    }
</style>

<section class = "demo-box">
    <table class="table">
        <tr>
            <th>Order Id</th>
            <th>Member</th>
            <th>Date Time</th>
            <th>Items</th>
            <th>Total (RM)</th>
            <th>Payment</th>
        </tr>

        <?php foreach ($arr as $o): ?>
        <tr>
            <td><?= $o->id ?></td>
            <td>
                <div class="customer-meta">
                    <strong><?= encode($o->name) ?></strong>
                    <span><?= encode($o->email) ?></span>
                </div>
            </td>
            <td><?= $o->datetime ?></td>
            <td><?= $o->count ?></td>
            <td><?= $o->total ?></td>
            <td><?= $o->is_paid ? 'Paid' : 'Pending' ?></td>
            <td class="table-actions">
                <button data-get="order_detail.php?id=<?= $o->id ?>">Detail</button>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
</section>

<p>
<br>
<br>
</p>

<?php
include '../_foot.php';
