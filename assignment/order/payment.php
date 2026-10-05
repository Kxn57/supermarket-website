<?php
include '../_base.php';

// ----------------------------------------------------------------------------

auth('Member');

$cart = get_cart();
if (!$cart) redirect('cart.php');

$payment_method = post('payment_method', 'card');
$card_name      = post('card_name');
$card_number    = post('card_number');
$expiry         = post('expiry');
$cvv            = post('cvv');

$items = [];
$count = 0;
$total = 0;

$stm = $_db->prepare('SELECT * FROM product WHERE id = ?');
foreach ($cart as $id => $unit) {
    $stm->execute([$id]);
    $p = $stm->fetch();
    if (!$p) continue;

    $subtotal = $p->price * $unit;
    $count += $unit;
    $total += $subtotal;
    $items[] = (object)[
        'id' => $p->id,
        'name' => $p->name,
        'price' => $p->price,
        'photo' => $p->photo,
        'unit' => $unit,
        'subtotal' => $subtotal,
    ];
}

if (!$items) {
    set_cart();
    redirect('cart.php');
}

if (is_post()) {
    if ($payment_method == 'card') {
        if ($card_name == '') {
            $_err['card_name'] = 'Required';
        }

        if ($card_number == '') {
            $_err['card_number'] = 'Required';
        }

        if ($expiry == '') {
            $_err['expiry'] = 'Required';
        }

        if ($cvv == '') {
            $_err['cvv'] = 'Required';
        }
    }

    if (!$_err) {
        $_db->beginTransaction();

        $stm = $_db->prepare('
            INSERT INTO `order` (datetime, user_id, count, total, is_paid, paid_at)
            VALUES (NOW(), ?, ?, ?, 1, NOW())
        ');
        $stm->execute([$_user->id, $count, $total]);
        $order_id = $_db->lastInsertId();

        $stm = $_db->prepare('
            INSERT INTO item (order_id, product_id, price, unit, subtotal)
            VALUES (?, ?, ?, ?, ?)
        ');
        foreach ($items as $i) {
            $stm->execute([$order_id, $i->id, $i->price, $i->unit, $i->subtotal]);
        }

        $_db->commit();

        try {
            $m = get_mail();
            $m->addAddress($_user->email, $_user->name);
            $m->Subject = 'Fresh Mart Payment Successful';
            $m->Body =
                "Hi $_user->name,\n\n" .
                "Your payment was successful.\n" .
                "Order ID: $order_id\n" .
                'Total: RM ' . number_format($total, 2) . "\n\n" .
                "Thank you for shopping with Fresh Mart.";
            $m->send();
        }
        catch (Exception $e) {
            // Keep payment successful even if email sending fails.
        }

        set_cart();
        temp('info', 'Payment successful');
        redirect("detail.php?id=$order_id");
    }
}

// ----------------------------------------------------------------------------

$_title = 'Order | Payment';
include '../_head.php';
?>

<style>
    .payment-shell {
        display: grid;
        grid-template-columns: minmax(340px, 520px) minmax(320px, 420px);
        gap: 24px;
        align-items: start;
    }

    .payment-card {
        padding: 24px;
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 14px 40px #be20301a;
    }

    .payment-card h2 {
        margin: 0 0 10px;
        font-size: 28px;
    }

    .payment-card p {
        margin: 0 0 20px;
        color: #6f6467;
    }

    .payment-methods {
        display: grid;
        gap: 12px;
        margin-bottom: 18px;
    }

    .payment-methods label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        border: 1px solid #eadede;
        border-radius: 14px;
        background: #fff8f8;
    }

    .payment-grid {
        display: grid;
        gap: 14px;
    }

    .payment-grid label {
        display: grid;
        gap: 8px;
        font-weight: 600;
    }

    .payment-row {
        display: grid;
        grid-template-columns: 1fr 140px;
        gap: 14px;
    }

    .payment-actions {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }

    .order-lines {
        display: grid;
        gap: 14px;
        margin-bottom: 20px;
    }

    .order-line {
        display: grid;
        grid-template-columns: 64px 1fr auto;
        gap: 12px;
        align-items: center;
    }

    .order-line img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 14px;
        background: #fff6f6;
    }

    .order-line strong,
    .summary-row strong {
        display: block;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-top: 1px solid #f0dddd;
    }

    .summary-row.total {
        font-size: 20px;
        font-weight: 800;
        color: #d61f2c;
    }

    .ewallet-box {
        display: none;
        padding: 18px;
        border: 1px dashed #eadede;
        border-radius: 18px;
        background: #fff8f8;
        text-align: center;
    }

    .ewallet-box img {
        width: 220px;
        max-width: 100%;
        border-radius: 16px;
        background: #fff;
        padding: 10px;
    }

    @media (max-width: 900px) {
        .payment-shell {
            grid-template-columns: 1fr;
        }

        .payment-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="payment-shell">
    <form method="post" class="payment-card">
        <h2>Payment</h2>

        <div class="payment-methods">
            <label><input type="radio" name="payment_method" value="card" <?= $payment_method == 'card' ? 'checked' : '' ?>> Credit / Debit Card</label>
            <label><input type="radio" name="payment_method" value="ewallet" <?= $payment_method == 'ewallet' ? 'checked' : '' ?>> E-Wallet</label>
        </div>

        <div class="ewallet-box" id="ewallet-fields">
            <p>Scan this QR code to pay with your e-wallet.</p>
            <img src="/photos/qr.jpg" alt="E-Wallet QR Code">
        </div>

        <div class="payment-grid" id="card-fields">
            <label>
                Cardholder Name
                <?php html_text('card_name', 'maxlength="100" placeholder="Enter cardholder name"'); ?>
                <?php err('card_name'); ?>
            </label>

            <label>
                Card Number
                <?php html_text('card_number', 'maxlength="19" placeholder="1234 5678 9012 3456"'); ?>
                <?php err('card_number'); ?>
            </label>

            <div class="payment-row">
                <label>
                    Expiry Date
                    <?php html_text('expiry', 'maxlength="5" placeholder="MM/YY"'); ?>
                    <?php err('expiry'); ?>
                </label>

                <label>
                    CVV
                    <?php html_password('cvv', 'maxlength="4" placeholder="123"'); ?>
                    <?php err('cvv'); ?>
                </label>
            </div>
        </div>

        <div class="payment-actions">
            <button>Pay Now</button>
            <button type="button" data-get="cart.php">Back to Cart</button>
        </div>
    </form>

    <section class="payment-card">
        <h2>Order Summary</h2>
        <p><?= count($items) ?> product(s), <?= $count ?> unit(s)</p>

<script>
    function togglePaymentFields() {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        document.getElementById('card-fields').style.display = method === 'card' ? 'grid' : 'none';
        document.getElementById('ewallet-fields').style.display = method === 'ewallet' ? 'block' : 'none';
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', togglePaymentFields);
    });

    togglePaymentFields();
</script>       

        <div class="order-lines">
            <?php foreach ($items as $i): ?>
                <article class="order-line">
                    <img src="/products/<?= $i->photo ?>" alt="<?= encode($i->name) ?>">
                    <div>
                        <strong><?= $i->name ?></strong>
                        <span>RM <?= number_format($i->price, 2) ?> x <?= $i->unit ?></span>
                    </div>
                    <strong>RM <?= number_format($i->subtotal, 2) ?></strong>
                </article>
            <?php endforeach ?>
        </div>

        <div class="summary-row">
            <span>Total Units</span>
            <strong><?= $count ?></strong>
        </div>
        <div class="summary-row total">
            <span>Total Payment</span>
            <strong>RM <?= number_format($total, 2) ?></strong>
        </div>
    </section>
</div>

<?php
include '../_foot.php';
