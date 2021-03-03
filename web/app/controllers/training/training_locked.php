<?php
requirePHPLib('training');

if (!validateUInt($_GET['id']) || !($training = queryTrainingBrief($_GET['id']))) {
    become404Page();
}

$training_content = queryTrainingContent($training['id']);

// var_dump($training);

if (!isTrainingVisibleToUser($training, $myUser)) {
    become404Page();
}

if (isTrainingUnlockedToUser($training['id'], $myUser) || hasTrainingPermission($myUser, $training)) {
    redirectTo("/training/${training['id']}");
}

if ($myUser) {
    $training = queryTrainingBriefForUser($training['id'], $myUser['username']);
}

$header_t = '<tr>';
$header_t .= '<th class="text-center" style="width:5em;">ID</th>';
$header_t .= '<th>'.UOJLocale::get('training::training').'</th>';
if ($myUser != null) {
    $header_t .= '<th class="text-center" style="width:150px;">'.UOJLocale::get('training::completion').'</th>';
}
$header_t .= '<th class="text-center" style="width:5em;">'.UOJLocale::get('training::total tasks').'</th>';
$header_t .= '</tr>';

$pag_config_t = array('page_len' => 10000000);
$pag_config_t['col_names'] = array(
    '*',
    '(select count(*) from trainings_includes where p_id=trainings.id) total_tasks',
    "(select count(*) from (best_ac_submissions join trainings_includes on best_ac_submissions.problem_id = s_id) where p_id = trainings.id and best_ac_submissions.submitter = '{$myUser['username']}') completed",
    '(select count(*) from trainings_cond where t_id=trainings.id) total_cond',
    "((select count(*) from (trainings_completion join trainings_cond on trainings_completion.training_id = c_id) where type = 'T' and t_id = trainings.id and trainings_completion.user = '{$myUser['username']}') + (select count(*) from (best_ac_submissions join trainings_cond on best_ac_submissions.problem_id = c_id) where type = 'P' and t_id = trainings.id and best_ac_submissions.submitter = '{$myUser['username']}')) cond_complete"
);
$username = isset($myUser) ? $myUser['username'] : ":";
$pag_config_t['table_name'] = "trainings join trainings_cond on id = c_id";
$pag_config_t['cond'] = 'type = \'T\' and t_id = '.$training['id'];
$pag_config_t['tail'] = "order by id asc";
$pag_t = new Paginator($pag_config_t);

function echoProblem($problem) {
    global $myUser;
    if (isProblemVisibleToUser($problem, $myUser)) {
        echo '<tr class="text-center">';
        if ($problem['submission_id']) {
            echo '<td class="table-success">';
        } else {
            echo '<td>';
        }
        echo '#', $problem['id'], '</td>';
        echo '<td class="text-left">';
        if ($problem['is_hidden']) {
            echo '<svg width="2.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="red" xmlns="http://www.w3.org/2000/svg">
  <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
  <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
  <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
</svg>';
        }
        echo '<a href="/problem/', $problem['id'], '">', $problem['title'], '</a>';
        if (isset($_COOKIE['show_tags_mode'])) {
            foreach (queryProblemTags($problem['id']) as $tag) {
                echo '<a class="uoj-problem-tag">', '<span class="badge badge-pill badge-secondary">', HTML::escape($tag), '</span>', '</a>';
            }
        }
        echo '</td>';
        echo '<td class="text-left">', getClickZanBlock('P', $problem['id'], $problem['zan']), '</td>';
        echo '</tr>';
    }
}

$header_p = '<tr>';
$header_p .= '<th class="text-center" style="width:5em;">ID</th>';
$header_p .= '<th>'.UOJLocale::get('problems::problem').'</th>';
$header_p .= '<th class="text-center" style="width:180px;">'.UOJLocale::get('appraisal').'</th>';
$header_p .= '</tr>';

$pag_config_p = array('page_len' => 10000000);
$pag_config_p['col_names'] = array('*');
$pag_config_p['table_name'] = "(problems left join best_ac_submissions on best_ac_submissions.submitter = '{$myUser['username']}' and problems.id = best_ac_submissions.problem_id) join trainings_cond on id = c_id";
$pag_config_p['cond'] = 'type = \'P\' and t_id = '.$training['id'];
$pag_config_p['tail'] = "order by id asc";
$pag_p = new Paginator($pag_config_p);

$div_classes = array('table-responsive');
$table_classes = array('table', 'table-bordered', 'table-hover', 'table-striped');

?>

<?php echoUOJPageHeader(HTML::stripTags($training['title']) . ' - ' . UOJLocale::get('training::training')) ?>

<h1 class="page-header text-center">#<?= $training['id']?>. <?= $training['title'] ?></h1>
<a role="button" class="btn btn-info float-right" href="/training/<?= $training['id'] ?>/statistics"><span class="glyphicon glyphicon-stats"></span> <?= UOJLocale::get('problems::statistics') ?></a>

<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" href="#tab-statement" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-book"></span> <?= UOJLocale::get('problems::statement') ?></a></li>
    <li class="nav-item"><a class="nav-link" href="#tab-list" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-list-alt"></span> <?= UOJLocale::get('training::requirements') ?></a></li>
    <?php if (hasTrainingPermission($myUser, $training)): ?>
        <li class="nav-item"><a class="nav-link" href="/training/<?= $training['id'] ?>/manage/statement" role="tab"><?= UOJLocale::get('problems::manage') ?></a></li>
    <?php endif ?>
</ul>

<div class="tab-content">
    <div class="alert alert-danger" role="alert">
        <?= UOJLocale::get('training::have not meet req') ?>
    </div>
    <div class="tab-pane active" id="tab-statement">
        <?php if (strlen($training_content['statement']) == 0): ?>
            <div class="text-center">
                <div style="font-size:233px; color:lightgray"><?= UOJLocale::get('training::no des') ?></div>
                <p><?= UOJLocale::get('training::no des comment') ?></p>
            </div>
        <?php else: ?>
            <article class="top-buffer-md"><?= $training_content['statement'] ?></article>
        <?php endif ?>
    </div>
    <div class="tab-pane" id="tab-list">
        <?php if ($pag_t->isEmpty() && $pag_p->isEmpty()): ?>
            <div class="text-center">
                <div style="font-size:233px; color:lightgray"><?= UOJLocale::get('training::no content') ?></div>
                <p><?= UOJLocale::get('training::no content comment') ?></p>
            </div>
        <?php else: ?>
            <div class="col-sm-12 order-sm-9 checkbox text-right">
                <label class="checkbox-inline" for="input-show_tags_mode"><input type="checkbox" id="input-show_tags_mode" <?= isset($_COOKIE['show_tags_mode']) ? 'checked="checked" ': ''?>/> <?= UOJLocale::get('problems::show tags') ?></label>
            </div>
            <?php if (!$pag_t->isEmpty()): ?>
                <h3><?= UOJLocale::get('training::req sub') ?></h3>
                <?php if ($myUser): ?>
                    <?php
                    $perc_t = $training['training_cond'] > 0 ? round(100 * $training['cond_training_complete'] / $training['training_cond']) : 0;
                    ?>
                    <div class="progress bot-buffer-no" title="<?=$training['cond_training_complete']?> / <?=$training['training_cond']?>">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?=$training['cond_training_complete']?>" aria-valuemin="0" aria-valuemax="<?=$training['training_cond']?>" style="width: <?=$perc_t?>%; min-width: 20px;"><?=$perc_t?>%</div>
                    </div>
                <?php endif; ?>
                <?php
                echo '<div class="', join($div_classes, ' '), '">';
                echo '<table class="', join($table_classes, ' '), '">';
                echo '<thead>';
                echo $header_t;
                echo '</thead>';
                echo '<tbody>';

                foreach ($pag_t->get() as $idx => $row) {
                    echoTraining($row);
                    echo "\n";
                }
                if ($pag_t->isEmpty()) {
                    echo '<tr><td class="text-center" colspan="233">'.UOJLocale::get('none').'</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                ?>
            <?php endif ?>
            <?php if (!$pag_p->isEmpty()): ?>
                <h3><?= UOJLocale::get('training::req pro') ?></h3>
                <?php if ($myUser): ?>
                    <?php
                    $perc_p = $training['problem_cond'] > 0 ? round(100 * $training['cond_problem_complete'] / $training['problem_cond']) : 0;
                    ?>
                    <div class="progress bot-buffer-no" title="<?=$training['cond_problem_complete']?> / <?=$training['problem_cond']?>">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?=$training['cond_problem_complete']?>" aria-valuemin="0" aria-valuemax="<?=$training['problem_cond']?>" style="width: <?=$perc_p?>%; min-width: 20px;"><?=$perc_p?>%</div>
                    </div>
                <?php endif; ?>
                <?php
                echo '<div class="', join($div_classes, ' '), '">';
                echo '<table class="', join($table_classes, ' '), '">';
                echo '<thead>';
                echo $header_p;
                echo '</thead>';
                echo '<tbody>';

                foreach ($pag_p->get() as $idx => $row) {
                    echoProblem($row);
                    echo "\n";
                }
                if ($pag_p->isEmpty()) {
                    echo '<tr><td class="text-center" colspan="233">'.UOJLocale::get('none').'</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                ?>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
<script type="text/javascript">
    $('#input-show_tags_mode').click(function() {
        if (this.checked) {
            $.cookie('show_tags_mode', '', {path: '/training'});
        } else {
            $.removeCookie('show_tags_mode', {path: '/training'});
        }
        location.reload();
    });
</script>
<?php echoUOJPageFooter() ?>
