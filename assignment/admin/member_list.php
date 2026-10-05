<?php
include '../_base.php';

auth('Admin');

// ----------------------------------------------------------------------------

$search = req('search');

$stm = $_db->prepare("
    SELECT id, name, email, photo, role
    FROM user
    WHERE role = 'Member'
    AND (name LIKE ? OR email LIKE ?)
    ORDER BY name
");
$stm->execute(["%$search%", "%$search%"]);
$arr = $stm->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Admin | Member List';
include '../_head.php';
?>

<style>
    .admin-search-box {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 18px;
    }

    .member-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .member-cell img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #eee;
    }

    .member-cell strong,
    .member-cell span {
        display: block;
    }

    .member-cell span {
        color: #777;
        font-size: 13px;
    }
</style>

<form class="admin-search-box">
    <?= html_search('search', 'placeholder="Search member name or email"') ?>
    <button>Search</button>
</form>

<p><?= count($arr) ?> member(s)</p>

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
            <th>Id</th>
            <th>Member</th>
            <th>Email</th>
            <th>Role</th>
            <th></th>
        </tr>

        <?php foreach ($arr as $m): ?>
        <tr>
            <td><?= $m->id ?></td>
            <td>
                <div class="member-cell">
                    <img src="/photos/<?= $m->photo ?>" alt="<?= encode($m->name) ?>">
                    <div>
                        <strong><?= encode($m->name) ?></strong>
                        <span>Member account</span>
                    </div>
                </div>
            </td>
            <td><?= encode($m->email) ?></td>
            <td><?= encode($m->role) ?></td>
            <td>
                <button data-get="member_update.php?id=<?= $m->id ?>">Update</button>
                <button data-post="member_delete.php?id=<?= $m->id ?>" data-confirm>Delete</button>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
</section>

<?php
include '../_foot.php';
