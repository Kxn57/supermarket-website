<?php
include '../_base.php';

// ----------------------------------------------------------------------------

auth('Member');

$stm = $_db->prepare('
    SELECT * FROM `order`
    WHERE user_id = ?
    AND is_paid = 1
    ORDER BY id DESC
');
$stm->execute([$_user->id]);
$arr = $stm->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Order | History';
include '../_head.php';
?>

<!-- (B) EXTRA: CSS -->
<style>
    tr:hover .popup  {
        display: grid !important;
        grid: auto / repeat(5, auto);
        gap: 1px;
        border: none;
    } 

    .popup img {
        width: 50px;
        height: 50px;
        outline: 1px solid #333;
    }

    .table-box {
        width: 50%;
        min-width: 520px;
    }

    .table-box .table {
        width: 100%;
    }
</style>

<p><?= count($arr) ?> record(s)</p>

<div class="table-box">
<table class="table">
    <tr>
        <th>Id</th>
        <th>Datetime</th>
        <th>Count</th>
        <th>Total (RM)</th>
        <th></th>
    </tr>

    <?php foreach ($arr as $o): ?>
    <tr>
        <td><?= $o->id ?></td>
        <td><?= $o->datetime ?></td>
        <td class="right"><?= $o->count ?></td>
        <td class="right"><?= $o->total ?></td>
        <td>
            <button data-get="detail.php?id=<?= $o->id ?>">Detail</button>
            <!-- (A) EXTRA: Product photos -->
            <div class="popup">
                <?php
                    $stm = $_db->prepare('
                        SELECT p.photo 
                        FROM item AS i, product AS p
                        WHERE i.product_id = p.id
                        AND i.order_id = ?
                    ');
                    $stm->execute([$o->id]);
                    $photos = $stm->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($photos as $photo) {
                        echo "<img src='/products/$photo'>";
                    }
                ?>
            </div>
        </td>
    </tr>
    <?php endforeach ?>
</table>
</div>

<?php

