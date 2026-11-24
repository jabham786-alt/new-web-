<?php
declare(strict_types=1);

function site_url(string $path='/'): string
{
    $base = rtrim((string) app_config('app.base_url', ''), '/');
    return $base === '' ? '' : $base . '/' . ltrim($path, '/');
}

function render_seo_head(string $title, string $description, string $path='/', string $robots='index,follow,max-image-preview:large'): void
{
    $canonical = site_url($path);
    echo '<title>' . e($title) . "</title>\n";
    echo '<meta name="description" content="' . e($description) . '">' . "\n";
    echo '<meta name="robots" content="' . e($robots) . '">' . "\n";
    if ($canonical !== '') {
        echo '<link rel="canonical" href="' . e($canonical) . '">' . "\n";
        echo '<meta property="og:url" content="' . e($canonical) . '">' . "\n";
    }
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:site_name" content="Hammad Graphic">' . "\n";
    echo '<meta property="og:title" content="' . e($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . e($description) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary">' . "\n";
}

function render_business_schema(): void
{
    $base = rtrim((string) app_config('app.base_url', ''), '/');
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => (string) app_config('business.name', 'Hammad Graphic'),
        'founder' => (string) app_config('business.owner', 'Hammad Ali'),
        'description' => 'Graphic design, website design and digital services for businesses.',
        'email' => (string) app_config('business.email', ''),
        'telephone' => (string) app_config('business.phone', ''),
        'areaServed' => ['@type' => 'Country', 'name' => 'Pakistan'],
        'priceRange' => 'Custom quotation',
    ];
    if ($base !== '') {
        $schema['url'] = $base . '/';
    }
    echo '<script type="application/ld+json">'
        . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
        . '</script>' . "\n";
}
