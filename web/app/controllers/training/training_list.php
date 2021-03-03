<?php
requirePHPLib('form');
requirePHPLib('judger');
requirePHPLib('data');
requirePHPLib('training');

if (isset($myUser) && isSuperUser($myUser)) {
    $new_training_form = new UOJForm('new_training');
    $new_training_form->handle = function () {
        DB::query("insert into trainings (title, is_hidden) values ('New Training', 1)");
        $id = DB::insert_id();
        DB::query("insert into trainings_contents (id, statement, statement_md) values ($id, '', '')");
    };
    $new_training_form->submit_button_config['align'] = 'right';
    $new_training_form->submit_button_config['class_str'] = 'btn btn-primary';
    $new_training_form->submit_button_config['text'] = UOJLocale::get('training::add new');
    $new_training_form->submit_button_config['smart_confirm'] = '';

    $new_training_form->runAtServer();
}

$header = '<tr>';
$header .= '<th class="text-center" style="width:5em;">ID</th>';
$header .= '<th>' . UOJLocale::get('training::training') . '</th>';
if ($myUser != null) {
    $header .= '<th class="text-center" style="width:150px;">' . UOJLocale::get('training::completion') . '</th>';
}
$header .= '<th class="text-center" style="width:5em;">' . UOJLocale::get('training::total tasks') . '</th>';
$header .= '</tr>';

$cond = array();

if ($cond) {
    $cond = join($cond, ' and ');
} else {
    $cond = '1';
}

$pag_config = array('page_len' => 100);
$pag_config['col_names'] = array(
    '*',
    '(select count(*) from trainings_includes where p_id=trainings.id) total_tasks',
    "(select count(*) from (best_ac_submissions join trainings_includes on best_ac_submissions.problem_id = s_id) where p_id = trainings.id and best_ac_submissions.submitter = '{$myUser['username']}') completed",
    '(select count(*) from trainings_cond where t_id=trainings.id) total_cond',
    "((select count(*) from (trainings_completion join trainings_cond on trainings_completion.training_id = c_id) where type = 'T' and t_id = trainings.id and trainings_completion.user = '{$myUser['username']}') + (select count(*) from (best_ac_submissions join trainings_cond on best_ac_submissions.problem_id = c_id) where type = 'P' and t_id = trainings.id and best_ac_submissions.submitter = '{$myUser['username']}')) cond_complete"
);
$username = isset($myUser) ? $myUser['username'] : ":";
$pag_config['table_name'] = "trainings";
$pag_config['cond'] = $cond;
$pag_config['tail'] = "order by id asc";
$pag = new Paginator($pag_config);

$div_classes = array('table-responsive');
$table_classes = array('table', 'table-bordered', 'table-hover', 'table-striped');
?>
<?php echoUOJPageHeader(UOJLocale::get('training')) ?>
<div class="row">
    <div class="col-sm-4 order-sm-5">
        <?php echo $pag->pagination(); ?>
    </div>
</div>
<div class="top-buffer-sm"></div>
<?php
echo '<div class="', join($div_classes, ' '), '">';
echo '<table class="', join($table_classes, ' '), '">';
echo '<thead>';
echo $header;
echo '</thead>';
echo '<tbody>';

foreach ($pag->get() as $idx => $row) {
    echoTraining($row);
    echo "\n";
}
if ($pag->isEmpty()) {
    echo '<tr><td class="text-center" colspan="233">' . UOJLocale::get('none') . '</td></tr>';
}

echo '</tbody>';
echo '</table>';
echo '</div>';

if (isSuperUser($myUser)) {
    $new_training_form->printHTML();
}

echo $pag->pagination();
?>
<?php echoUOJPageFooter() ?>