<?php
include '../_base.php';

auth('Admin');

// ----------------------------------------------------------------------------

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare("SELECT * FROM user WHERE id = ? AND role = 'Member'");
    $stm->execute([$id]);
    $m = $stm->fetch();

    if (!$m) {
        redirect('member_list.php');
    }

    extract((array)$m);
    $_SESSION['member_photo'] = $m->photo;
}

if (is_post()) {
    $id = req('id');
    $email = req('email');
    $name = req('name');
    $photo = $_SESSION['member_photo'] ?? '';
    $f = get_file('photo');

    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else {
        $stm = $_db->prepare("
            SELECT COUNT(*)
            FROM user
            WHERE email = ? AND id != ?
        ");
        $stm->execute([$email, $id]);

        if ($stm->fetchColumn() > 0) {
            $_err['email'] = 'Duplicated';
        }
    }

    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        }
        else if ($f->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    }

    if (!$_err) {
        if ($f) {
            $old = "../photos/$photo";
            if ($photo && is_file($old)) {
                unlink($old);
            }
            $photo = save_photo($f, '../photos');
        }

        $stm = $_db->prepare("
            UPDATE user
            SET email = ?, name = ?, photo = ?
            WHERE id = ? AND role = 'Member'
        ");
        $stm->execute([$email, $name, $photo, $id]);

        unset($_SESSION['member_photo']);
        temp('info', 'Member updated');
        redirect('member_list.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Admin | Update Member';
include '../_head.php';
?>

<form method="post" class="form" enctype="multipart/form-data">
    <?= html_hidden('id') ?>

    <label for="id">Id</label>
    <div><?= $id ?></div>
    <span></span>

    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/photos/<?= $photo ?>">
    </label>
    <?= err('photo') ?>

    <section>
        <button>Submit</button>
        <button type="button" data-get="member_list.php">Back</button>
    </section>
</form>


<?php
include '../_foot.php';
