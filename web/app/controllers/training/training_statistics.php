<?php
requirePHPLib('training');

if (!validateUInt($_GET['id']) || !($training = queryTrainingBrief($_GET['id']))) {
    become404Page();
}

if (!isTrainingVisibleToUser($training, $myUser)) {
    become404Page();
}

function echoTrainingMemberList($cond, $tail, $config, $user) {
    $header_row = '<tr><th>用户名</th></tr>';
    $col_names = array();
    $col_names[] = 'user';

    $table_name = isset($config['table_name']) ? $config['table_name'] : 'submissions';

    $table_config = isset($config['table_config']) ? $config['table_config'] : null;

    echoLongTable($col_names, $table_name, $cond, $tail, $header_row,
        function($username) use($config, $user) {
            echo '<tr><td>', $username['user'], '</td></tr>';
        }, $table_config);
}

?>

<?php echoUOJPageHeader(HTML::stripTags($training['title']) . ' - ' . UOJLocale::get('problems::statistics')) ?>

<h1 class="page-header text-center"><?= $training['title'] ?> <?= UOJLocale::get('problems::statistics') ?></h1>

<h2 class="text-center">已完成的用户</h2>

<?php

echoTrainingMemberList(
        "training_id = ${training['id']}", 'order by time desc',
        array('table_name' => 'trainings_completion'),
        $myUser
);
?>

<?php echoUOJPageFooter() ?>

