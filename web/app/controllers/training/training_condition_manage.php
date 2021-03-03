<?php
requirePHPLib('form');
requirePHPLib('training');

if (!validateUInt($_GET['id']) || !($training = queryTrainingBrief($_GET['id']))) {
    become404Page();
}
if (!hasTrainingPermission($myUser, $training)) {
    become403Page();
}

$cond_problem_form = newAddDelCmdForm('cond_p',
    function($target) {
        if (!queryProblemBrief($target)) {
            return "不存在编号为{$target}的题目";
        }
        return '';
    },
    function($type, $target) {
        global $training;
        if ($type == '+') {
            DB::query("insert into trainings_cond (t_id, c_id, type) values (${training['id']}, $target, 'P')");
        } else if ($type == '-') {
            DB::query("delete from trainings_cond where t_id = ${training['id']} and c_id = '$target' and type = 'P'");
        }
    }
);
$cond_training_form = newAddDelCmdForm('cond_t',
    function($target) {
        global $training;
        if ($target == $training['id']) {
            return "不能把自己加入到自己的列表中";
        }
        if (!judgeLoopCondition($training['id'], $target)) {
            return "添加编号为{$target}的训练将导致环的产生，请仔细检查";
        }
        if (!queryTrainingBrief($target)) {
            return "不存在编号为{$target}的训练";
        }
        return '';
    },
    function($type, $target) {
        global $training;
        if ($type == '+') {
            DB::query("insert into trainings_cond (t_id, c_id, type) values (${training['id']}, $target, 'T')");
        } else if ($type == '-') {
            DB::query("delete from trainings_cond where t_id = ${training['id']} and c_id = '$target' and type = 'T'");
        }
    }
);

$cond_problem_form->runAtServer();
$cond_training_form->runAtServer();
?>
<?php echoUOJPageHeader(HTML::stripTags($training['title']) . ' - 依赖 - 训练管理') ?>
<h1 class="page-header text-center">#<?=$training['id']?> : <?=$training['title']?> 管理</h1>
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link" href="/training/<?= $training['id'] ?>/manage/statement" role="tab">编辑</a></li>
    <li class="nav-item"><a class="nav-link" href="/training/<?= $training['id'] ?>/manage/content" role="tab">内容</a></li>
    <li class="nav-item"><a class="nav-link active" href="/training/<?= $training['id'] ?>/manage/condition" role="tab">依赖</a></li>
    <li class="nav-item"><a class="nav-link" href="/training/<?=$training['id']?>" role="tab">返回</a></li>
</ul>
<div class="alert alert-primary" role="alert">
    只有用户将先决条件列表中定义的全部题目和训练完成后，才能查看本训练的内容，描述在任何情况下都是公开的（包括未注册用户）。
</div>
<div class="alert alert-warning" role="alert">
    如果先决条件列表中含有隐藏的题目或训练，则会导致用户无法解锁训练，请注意！
</div>
<h3>先决训练</h3>
<table class="table table-bordered table-hover table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>训练</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $row_id = 0;
    $result = DB::query("select id, title, is_hidden from (trainings join trainings_cond on id = c_id) where t_id = ${training['id']} and type = 'T'");
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
<p class="text-center">命令格式：命令一行一个，+1表示把1号训练加入先决条件列表，-1表示把1号训练从先决条件列表中移除</p>
<?php $cond_training_form->printHTML(); ?>
<h3>先决题目</h3>
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
    $result = DB::query("select id, title, is_hidden from (problems join trainings_cond on id = c_id) where t_id = ${training['id']} and type = 'P'");
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
<p class="text-center">命令格式：命令一行一个，+1表示把1号题目加入先决条件列表，-1表示把1号题目从先决条件列表中移除</p>
<?php $cond_problem_form->printHTML(); ?>
<?php echoUOJPageFooter() ?>
