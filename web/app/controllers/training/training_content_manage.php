<?php
requirePHPLib('form');
requirePHPLib('training');

if (!validateUInt($_GET['id']) || !($training = queryTrainingBrief($_GET['id']))) {
    become404Page();
}
if (!hasTrainingPermission($myUser, $training)) {
    become403Page();
}

$contents_problem_form = newAddDelCmdForm('contents_p',
    function($target) {
        if (!queryProblemBrief($target)) {
            return "不存在编号为{$target}的题目";
        }
        return '';
    },
    function($type, $target) {
        global $training;
        if ($type == '+') {
            DB::query("insert into trainings_includes (p_id, s_id) values (${training['id']}, $target)");
        } else if ($type == '-') {
            DB::query("delete from trainings_includes where p_id = ${training['id']} and s_id = '$target'");
        } else {
            return false;
        }
        updateTrainingCompletion($training['id']);
    }
);

$contents_problem_form->runAtServer();

?>
<?php echoUOJPageHeader(HTML::stripTags($training['title']) . ' - 内容 - 训练管理') ?>
<h1 class="page-header" align="center">#<?=$training['id']?> : <?=$training['title']?> 管理</h1>
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link" href="/training/<?= $training['id'] ?>/manage/statement" role="tab">编辑</a></li>
    <li class="nav-item"><a class="nav-link active" href="/training/<?= $training['id'] ?>/manage/content" role="tab">内容</a></li>
    <li class="nav-item"><a class="nav-link" href="/training/<?= $training['id'] ?>/manage/condition" role="tab">依赖</a></li>
    <li class="nav-item"><a class="nav-link" href="/training/<?=$training['id']?>" role="tab">返回</a></li>
</ul>
<div class="alert alert-warning" role="alert">
    如果列表中含有隐藏的题目，则会导致用户无法完成训练，请注意！
</div>
<h3>题目</h3>
<table class="table table-bordered table-hover table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>题目</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $row_id = 0;
    $result = DB::query("select id, title, is_hidden from (problems join trainings_includes on id = s_id) where p_id = ${training['id']}");
    while ($row = DB::fetch($result, MYSQLI_ASSOC)) {
        $row_id++;
        echo '<tr>', '<td>', $row_id, '</td>', '<td>', ($row['is_hidden'] ? '<svg width="2.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="red" xmlns="http://www.w3.org/2000/svg">
  <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
  <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
  <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
</svg>' : ""), "<a href='/training/${row['id']}'>#${row['id']} : ${row['title']}</a>", '</td>', '</tr>';
    }
    if ($row_id == 0) {
        echo '<tr><td class="text-center" colspan="233">'.UOJLocale::get('none').'</td></tr>';
    }
    ?>
    </tbody>
</table>
<p class="text-center">命令格式：命令一行一个，+1表示把1号题目加入本训练，-1表示把1号题目从列表中移除</p>
<?php $contents_problem_form->printHTML(); ?>
<?php echoUOJPageFooter() ?>
