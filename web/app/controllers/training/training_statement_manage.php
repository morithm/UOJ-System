<?php
requirePHPLib('form');
requirePHPLib('training');

if (!validateUInt($_GET['id']) || !($training = queryTrainingBrief($_GET['id']))) {
    become404Page();
}
if (!hasTrainingPermission($myUser, $training)) {
    become403Page();
}

$training_content = queryTrainingContent($training['id']);

$training_editor = new UOJBlogEditor();
$training_editor->name = 'training';
$training_editor->blog_url = "/training/{$training['id']}";
$training_editor->cur_data = array(
    'title' => $training['title'],
    'content_md' => $training_content['statement_md'],
    'content' => $training_content['statement'],
    'tags' => '',
    'is_hidden' => $training['is_hidden']
);
$training_editor->label_text = array_merge($training_editor->label_text, array(
    'view blog' => '查看训练',
    'blog visibility' => '训练可见性'
));

$training_editor->save = function ($data) {
    global $training;
    DB::update("update trainings set title = '" . DB::escape($data['title']) . "' where id = {$training['id']}");
    DB::update("update trainings_contents set statement = '" . DB::escape($data['content']) . "', statement_md = '" . DB::escape($data['content_md']) . "' where id = {$training['id']}");

    if ($data['is_hidden'] != $training['is_hidden']) {
        DB::update("update trainings set is_hidden = {$data['is_hidden']} where id = {$training['id']}");
    }
};

$training_editor->runAtServer();
?>
<?php echoUOJPageHeader(HTML::stripTags($training['title']) . ' - 编辑 - 训练管理') ?>
<h1 class="page-header" align="center">#<?= $training['id'] ?> : <?= $training['title'] ?> 管理</h1>
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" href="/training/<?= $training['id'] ?>/manage/statement" role="tab">编辑</a></li>
    <li class="nav-item"><a class="nav-link" href="/training/<?= $training['id'] ?>/manage/content" role="tab">内容</a></li>
    <li class="nav-item"><a class="nav-link" href="/training/<?= $training['id'] ?>/manage/condition" role="tab">依赖</a></li>
    <li class="nav-item"><a class="nav-link" href="/training/<?= $training['id'] ?>" role="tab">返回</a></li>
</ul>
<?php $training_editor->printHTML() ?>
<?php echoUOJPageFooter() ?>