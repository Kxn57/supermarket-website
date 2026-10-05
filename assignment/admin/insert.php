<?php
include '../_base.php';


auth('Admin');
// ----------------------------------------------------------------------------

if (is_post()) {
    $id    = req('id');
    $name  = req('name');
    $price = req('price');
    $f     = get_file('photo');

    // Validate: id
    if ($id == '') {
        $_err['id'] = 'Required';
    }
    else if (!preg_match('/^P\d{3}$/', $id)) {
        $_err['id'] = 'Invalid format';
    }
    else if (!is_unique($id, 'product', 'id')) {
        $_err['id'] = 'Duplicated';
    }

    // Validate: name
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    // Validate: price
    if ($price == '') {
        $_err['price'] = 'Required';
    }
    else if (!is_money($price)) {
        $_err['price'] = 'Must be money';
    }
    else if ($price < 0.01 || $price > 99.99) {
        $_err['price'] = 'Must between 0.01 - 99.99';
    }

    // Validate: photo (file)
    if (!$f) {
        $_err['photo'] = 'Required';
    }
    else if (!str_starts_with($f->type, 'image/')) {
        $_err['photo'] = 'Must be image';
    }
    else if ($f->size > 1 * 1024 * 1024) {
        $_err['photo'] = 'Maximum 1MB';
    }

    // DB operation
    if (!$_err) {
        // Save photo
        $photo = save_photo($f, '../products');

        $stm = $_db->prepare('
            INSERT INTO product (id, name, price, photo)
            VALUES (?, ?, ?, ?)
        ');
        $stm->execute([$id, $name, $price, $photo]);

        temp('info', 'Record inserted');
        redirect('product_list.php');
    }
}

// ----------------------------------------------------------------------------

$_title = 'Product | Insert';
include '../_head.php';
?>

<p>
    <button data-get="index.php">Index</button>
</p>

<form method="post" class="form" enctype="multipart/form-data" novalidate>
    <label for="id">Id</label>
    <?= html_text('id', 'maxlength="4" placeholder="P999" data-upper') ?>
    <?= err('id') ?>

    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="price">Price</label>
    <?= html_number('price', 0.01, 99.99, 0.01) ?>
    <?= err('price') ?>

    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/images/photo.jpg">
    </label>
    <?= err('photo') ?>

    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>

<?php
include '../_foot.php';