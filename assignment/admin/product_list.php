<?php
include '../_base.php';

auth('Admin');

// ----------------------------------------------------------------------------

$arr = $_db->query('SELECT * FROM product')->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Admin | Product list';
include '../_head.php';
?>

<style>
    .popup {
        width: 100px;
        height: 100px;
    }
</style>

<p>
    <button data-get="insert.php">Insert</button>
</p>

<p><?= count($arr) ?> record(s)</p>

<style>
    .demo-box {
        width: 50%;
        min-width: 520px;
    }

    .demo-box .table {
        width: 100%;
    }
</style>

<section class= "demo-box">
    <table class="table">
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Price</th>
            <th></th>
        </tr>

        <?php foreach ($arr as $p): ?>
        <tr>
            <td><?= $p->id ?></td>
            <td><?= $p->name ?></td>
            <td><?= $p->price ?></td>
            <td>
                <button data-get="update.php?id=<?= $p->id ?>">Update</button>
                <button data-post="delete.php?id=<?= $p->id ?>">Delete</button>
                <img src="/products/<?= $p->photo ?>" class="popup">
            </td>
        </tr>
        <?php endforeach ?>
    </table>

</section>

<?php
include '../_foot.php';