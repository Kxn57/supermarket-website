<?php
include '../_base.php';

auth('Admin');

// ----------------------------------------------------------------------------

if (is_post()) {
    $id = req('id');

    $stm = $_db->prepare("SELECT COUNT(*) FROM `order` WHERE user_id = ?");
    $stm->execute([$id]);
    $has_orders = $stm->fetchColumn() > 0;

    if ($has_orders) {
        temp('info', 'Cannot delete member because this member already has orders');
        redirect('member_list.php');
    }

    $stm = $_db->prepare("SELECT photo FROM user WHERE id = ? AND role = 'Member'");
    $stm->execute([$id]);
    $photo = $stm->fetchColumn();

    $stm = $_db->prepare("DELETE FROM user WHERE id = ? AND role = 'Member'");
    $stm->execute([$id]);

    if ($photo) {
        $path = "../photos/$photo";
        if (is_file($path)) {
            unlink($path);
        }
    }

    temp('info', 'Member deleted');
}

redirect('member_list.php');
