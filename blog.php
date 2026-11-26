<?php
declare(strict_types=1);require_once __DIR__.'/app/bootstrap.php';
$s=db()->query("SELECT id,title,slug,excerpt,cover_image,published_at FROM blog_posts WHERE status='published' AND published_at<=NOW() ORDER BY published_at DESC,id DESC");
$posts=$s->fetchAll();$metaTitle='Design Blog | Hammad Graphics';$metaDescription='Design tips, creative ideas and updates from Hammad Graphics.';$canonicalPath='/blog.php';
require APP_ROOT.'/partials/header.php';?>
<section class="page-hero"><div class="container"><p class="eyebrow">Hammad Graphics Journal</p><h1>Ideas that make <span class="text-gradient">brands stronger.</span></h1><p>Design guidance, creative inspiration and practical tips.</p></div></section>
<section class="section section-white"><div class="container"><div class="blog-grid">
<?php if(!$posts):?><p>New articles will be published here soon.</p><?php else:foreach($posts as $post):?><article class="blog-card"><?php if($post['cover_image']):?><img src="/<?=e($post['cover_image'])?>" alt="<?=e($post['title'])?>" loading="lazy"><?php endif;?><p class="eyebrow"><?=e($post['published_at']??'')?></p><h2><a href="/blog-detail.php?slug=<?=rawurlencode($post['slug'])?>"><?=e($post['title'])?></a></h2><p><?=e($post['excerpt'])?></p><a class="text-link" href="/blog-detail.php?slug=<?=rawurlencode($post['slug'])?>">Read article →</a></article><?php endforeach;endif;?>
</div></div></section><?php require APP_ROOT.'/partials/footer.php';?>