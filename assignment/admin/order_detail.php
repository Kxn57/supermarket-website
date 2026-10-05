<?php
include '../_base.php';

auth('Admin');

// ----------------------------------------------------------------------------

$id = req('id');

$stm = $_db->prepare("
    SELECT o.*, u.name, u.email
    FROM `order` AS o
    JOIN user AS u ON u.id = o.user_id
    WHERE o.id = ?
");
$stm->execute([$id]);
$o = $stm->fetch();

if (!$o) {
    redirect('order_list.php');
}

$stm = $_db->prepare("
    SELECT i.*, p.name, p.photo
    FROM item AS i
    JOIN product AS p ON p.id = i.product_id
    WHERE i.order_id = ?
");
$stm->execute([$id]);
$arr = $stm->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Admin | Order Detail';
include '../_head.php';
?>

<style>
    .popup {
        width: 100px;
        height: 100px;
    }
</style>

<form class="form">
    <label>Order Id</label>
    <div><?= $o->id ?></div>
    <span></span>

    <label>Member Name</label>
    <div><?= encode($o->name) ?></div>
    <span></span>

    <label>Email</label>
    <div><?= encode($o->email) ?></div>
    <span></span>

    <label>Date Time</label>
    <div><?= $o->datetime ?></div>
    <span></span>

    <label>Payment</label>
    <div><?= $o->is_paid ? 'Paid' : 'Pending' ?></div>
    <span></span>

    <label>Item Count</label>
    <div><?= $o->count ?></div>
    <span></span>

    <label>Total</label>
    <div>RM <?= $o->total ?></div>
    <span></span>
</form>

<p><?= count($arr) ?> item(s)</p>

<table class="table">
    <tr>
        <th>Product Id</th>
        <th>Product Name</th>
        <th>Price (RM)</th>
        <th>Unit</th>
        <th>Subtotal (RM)</th>
    </tr>

    <?php foreach ($arr as $i): ?>
    <tr>
        <td><?= $i->product_id ?></td>
        <td><?= encode($i->name) ?></td>
        <td><?= $i->price ?></td>
        <td><?= $i->unit ?></td>
        <td>
            <?= $i->subtotal ?>
            <img src="/products/<?= $i->photo ?>" class="popup">
        </td>
    </tr>
    <?php endforeach ?>

    <tr>
        <th colspan="3"></th>
        <th><?= $o->count ?></th>
        <th><?= $o->total ?></th>
    </tr>
</table>

<p>
    <button data-get="order_list.php">Back</button>
</p>

<?php
include '../_foot.php';
