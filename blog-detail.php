<?php
declare(strict_types=1);require_once __DIR__.'/app/bootstrap.php';
$slug=trim((string)($_GET['slug']??''));if(!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/',$slug)){http_response_code(404);require APP_ROOT.'/404.php';exit;}
$s=db()->prepare("SELECT title,excerpt,content,cover_image,published_at FROM blog_posts WHERE slug=:slug AND status='published' AND published_at<=NOW() LIMIT 1");$s->execute([':slug'=>$slug]);$post=$s->fetch();if(!$post){http_response_code(404);require APP_ROOT.'/404.php';exit;}
$metaTitle=$post['title'].' | Hammad Graphics';$metaDescription=$post['excerpt'];$canonicalPath='/blog-detail.php?slug='.rawurlencode($slug);
require APP_ROOT.'/partials/header.php';?>
<section class="page-hero"><div class="container"><p class="eyebrow">Hammad Graphics Blog</p><h1><?=e($post['title'])?></h1><p><?=e($post['excerpt'])?></p></div></section>
<section class="section section-white"><article class="container blog-article"><?php if($post['cover_image']):?><img class="blog-cover" src="/<?=e($post['cover_image'])?>" alt="<?=e($post['title'])?>"><?php endif;?><p class="form-note">Published <?=e($post['published_at']??'')?></p><div class="blog-content"><?=nl2br(e($post['content']))?></div><p><a class="button button-secondary" href="/blog.php">Back to Blog</a></p></article></section>
<?php require APP_ROOT.'/partials/footer.php';?>