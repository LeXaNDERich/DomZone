<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';

renderHeader('О компании');
?>

<section class="section">
    <h1>О магазине DomZone</h1>
    <p class="lead">
        Мы собрали практичные хозтовары, которые делают бытовые задачи проще: от уборки до мелкого ремонта.
    </p>
    <div class="about-grid">
        <article class="card">
            <h3>Наша миссия</h3>
            <p>Помогать создавать комфортный и аккуратный дом без лишних затрат времени и денег.</p>
        </article>
        <article class="card">
            <h3>Качество</h3>
            <p>Тщательно проверяем поставщиков и отбираем позиции, которые действительно служат долго.</p>
        </article>
        <article class="card">
            <h3>Сервис</h3>
            <p>Консультируем по подбору товаров и подсказываем оптимальные решения под ваш бюджет.</p>
        </article>
    </div>
</section>

<?php renderFooter(); ?>
