<?php
declare(strict_types=1);require_once dirname(__DIR__).'/app/admin_auth.php';$admin=require_admin();
$id=filter_var($_GET['id']??$_POST['id']??0,FILTER_VALIDATE_INT)?:0;$post=['title'=>'','slug'=>'','excerpt'=>'','content'=>'','status'=>'draft','published_at'=>null];$error='';
if($id){$s=db()->prepare('SELECT * FROM blog_posts WHERE id=:id');$s->execute([':id'=>$id]);$found=$s->fetch();if(!$found){http_response_code(404);exit('Blog post not found.');}$post=$found;}
if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
 require_valid_csrf_post();$action=(string)($_POST['action']??'save');
 if($action==='delete'&&$id){db()->prepare('DELETE FROM blog_posts WHERE id=:id')->execute([':id'=>$id]);redirect_local('/admin/blogs.php?deleted=1');}
 $title=trim((string)($_POST['title']??''));$slug=strtolower(trim((string)($_POST['slug']??'')));$excerpt=trim((string)($_POST['excerpt']??''));$content=trim((string)($_POST['content']??''));$status=(string)($_POST['status']??'draft');
 if($slug==='')$slug=trim((string)preg_replace('/[^a-z0-9]+/','-',strtolower($title)),'-');
 if($title===''||mb_strlen($title)>180||!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/',$slug)||mb_strlen($slug)>200||$excerpt===''||mb_strlen($excerpt)>500||$content==='')$error='Please enter a title, valid URL slug, excerpt and article content.';
 elseif(!in_array($status,['draft','published'],true))$error='Invalid publishing status.';
 else{
  $check=db()->prepare('SELECT id FROM blog_posts WHERE slug=:slug AND id<>:id');$check->execute([':slug'=>$slug,':id'=>$id]);if($check->fetchColumn())$error='This blog URL slug is already in use.';
  else{$published=$status==='published'?'COALESCE(published_at,NOW())':'NULL';
   if($id){$sql="UPDATE blog_posts SET title=:title,slug=:slug,excerpt=:excerpt,content=:content,status=:status,published_at=".$published.",updated_at=NOW() WHERE id=:id";}
   else{$sql="INSERT INTO blog_posts(title,slug,excerpt,content,status,author_id,published_at,created_at) VALUES(:title,:slug,:excerpt,:content,:status,:author,".($status==='published'?'NOW()':'NULL').",NOW())";}
   $params=[':title'=>$title,':slug'=>$slug,':excerpt'=>$excerpt,':content'=>$content,':status'=>$status];if($id)$params[':id']=$id;else$params[':author']=(int)$admin['id'];
   db()->prepare($sql)->execute($params);redirect_local('/admin/blogs.php?saved=1');
  }
 }
 $post=array_merge($post,['title'=>$title,'slug'=>$slug,'excerpt'=>$excerpt,'content'=>$content,'status'=>$status]);
}
$metaTitle='Edit Blog | Hammad Graphics';$metaDescription='Create and edit blog posts.';$canonicalPath='/admin/blog-edit.php';$metaRobots='noindex,nofollow';require APP_ROOT.'/partials/header.php';?>
<section class="page-hero"><div class="container"><p class="eyebrow">Blog editor</p><h1><?= $id?'Edit':'Create'?> <span class="text-gradient">Blog Post.</span></h1><a class="button button-secondary" href="/admin/blogs.php">Back to Blogs</a></div></section>
<section class="section section-white"><div class="container auth-wrap"><?php if($error):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?>
<form class="order-form" method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$id?>">
<div class="form-row"><label>Title</label><input name="title" maxlength="180" value="<?=e($post['title'])?>" required></div>
<div class="form-row"><label>URL slug</label><input name="slug" maxlength="200" value="<?=e($post['slug'])?>" placeholder="leave blank to generate"></div>
<div class="form-row"><label>Short excerpt</label><textarea name="excerpt" maxlength="500" rows="3" required><?=e($post['excerpt'])?></textarea></div>
<div class="form-row"><label>Article content (plain text)</label><textarea name="content" rows="14" required><?=e($post['content'])?></textarea></div>
<div class="form-row"><label>Publishing status</label><select name="status"><option value="draft" <?=$post['status']==='draft'?'selected':''?>>Draft</option><option value="published" <?=$post['status']==='published'?'selected':''?>>Publish</option></select></div>
<button class="button button-primary" name="action" value="save">Save Blog</button></form>
<?php if($id):?><form method="post" class="delete-form" onsubmit="return confirm('Delete this blog post permanently?')"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$id?>"><button class="button button-secondary" name="action" value="delete">Delete Blog</button></form><?php endif;?>
</div></section><?php require APP_ROOT.'/partials/footer.php';?>