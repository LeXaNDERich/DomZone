<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/repository.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));

    if ($name === '' || $phone === '' || $message === '') {
        $errorMessage = 'Заполните все поля формы.';
    } else {
        saveContactRequest($name, $phone, $message);
        $successMessage = 'Спасибо! Ваша заявка отправлена.';
    }
}

renderHeader('Контакты');
?>

<section class="section">
    <h1>Контакты</h1>
    <p class="lead">Свяжитесь с нами удобным способом или оставьте заявку на обратный звонок.</p>
    <?php if ($successMessage !== ''): ?>
        <p class="alert success"><?= htmlspecialchars($successMessage) ?></p>
    <?php endif; ?>
    <?php if ($errorMessage !== ''): ?>
        <p class="alert error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <div class="contact-grid">
        <article class="card">
            <h3>Магазин DomZone</h3>
            <p>г. Москва, ул. Практичная, д. 12</p>
            <p>Телефон: +7 (999) 123-45-67</p>
            <p>Email: info@domzona.local</p>
        </article>

        <form class="card contact-form" method="post" action="/contact.php">
            <h3>Обратная связь</h3>
            <label>
                Имя
                <input type="text" name="name" placeholder="Ваше имя" value="<?= htmlspecialchars((string) ($_POST['name'] ?? '')) ?>">
            </label>
            <label>
                Телефон
                <input type="tel" name="phone" placeholder="+7 (...)" value="<?= htmlspecialchars((string) ($_POST['phone'] ?? '')) ?>">
            </label>
            <label>
                Сообщение
                <textarea name="message" rows="4" placeholder="Чем можем помочь?"><?= htmlspecialchars((string) ($_POST['message'] ?? '')) ?></textarea>
            </label>
            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>
</section>

<?php renderFooter(); ?>
