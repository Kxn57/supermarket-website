<?php
include '../_base.php';

auth('Admin');

// ----------------------------------------------------------------------------

if (is_post()) {
    $id = req('id');

    // Delete photo
    $stm = $_db->prepare('SELECT photo FROM product WHERE id = ?');
    $stm->execute([$id]);
    $photo = $stm->fetchColumn();
    unlink("../products/$photo");

    $stm = $_db->prepare('DELETE FROM product WHERE id = ?');
    $stm->execute([$id]);
    temp('info', 'Record deleted');
}

redirect('product_list.php');

// ----------------------------------------------------------------------------
