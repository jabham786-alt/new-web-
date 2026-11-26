<?php
declare(strict_types=1);require_once __DIR__.'/app/bootstrap.php';
$base=rtrim((string)app_config('app.base_url',''),'/');
if($base===''||!filter_var($base,FILTER_VALIDATE_URL)||!str_starts_with($base,'https://')){http_response_code(503);header('Content-Type: text/plain; charset=UTF-8');exit('Set app.base_url to your verified HTTPS website domain before submitting the sitemap to search engines.');}
$paths=['/','/services.php','/about.php','/contact.php','/blog.php'];
header('Content-Type: application/xml; charset=UTF-8');echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach($paths as $path)echo '<url><loc>'.htmlspecialchars($base.$path,ENT_XML1|ENT_QUOTES,'UTF-8').'</loc></url>';
$posts=db()->query("SELECT slug,published_at FROM blog_posts WHERE status='published' AND published_at<=NOW() ORDER BY published_at DESC")->fetchAll();
foreach($posts as $post){$url=$base.'/blog-detail.php?slug='.rawurlencode($post['slug']);echo '<url><loc>'.htmlspecialchars($url,ENT_XML1|ENT_QUOTES,'UTF-8').'</loc><lastmod>'.htmlspecialchars(date('c',strtotime((string)$post['published_at'])),ENT_XML1|ENT_QUOTES,'UTF-8').'</lastmod></url>';}
echo '</urlset>';
