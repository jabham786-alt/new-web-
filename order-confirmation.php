<?php
declare(strict_types=1);
require_once __DIR__ . '/app/bootstrap.php';
$reference = (string) ($_SESSION['last_order_reference'] ?? '');
if (!preg_match('/^HG-[A-F0-9]{8}$/', $reference)) {
    redirect_local('/order.php');
}
unset($_SESSION['last_order_reference']);
$metaTitle = 'Request Received | Hammad Graphic';
$metaDescription = 'Your Hammad Graphic project request has been received.';
$canonicalPath = '/order-confirmation.php';
$metaRobots = 'noindex,nofollow,noarchive';
require APP_ROOT . '/partials/header.php';
?>
<section class="section section-white confirmation-section">
    <div class="container confirmation-card">
        <div class="success-mark">✓</div>
        <p class="eyebrow">Request received</p>
        <h1>Thank you for choosing Hammad Graphic.</h1>
        <p>Your project request has been saved. Keep this reference for your records:</p>
        <div class="reference-code"><?= e($reference) ?></div>
        <p>We'll review your requirements and contact you with the quotation and next steps. You do not need to send payment until the price and payment details are confirmed.</p>
        <a class="button button-primary" href="/">Back to home</a>
    </div>
</section>
<?php require APP_ROOT . '/partials/footer.php'; ?>
