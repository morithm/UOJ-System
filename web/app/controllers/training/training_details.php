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

if (!isTrainingUnlockedToUser($training['id'], $myUser) && !hasTrainingPermission($myUser, $training)) {
    redirectTo("/training/${training['id']}/locked");
}

if ($myUser) {
    $training = queryTrainingBriefForUser($training['id'], $myUser['username']);
}

function echoProblem($problem)
{
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
        echo '</td>';
        echo '</tr>';
    }
}

$header_p = '<tr>';
$header_p .= '<th class="text-center" style="width:5em;">ID</th>';
$header_p .= '<th>' . UOJLocale::get('problems::problem') . '</th>';
$header_p .= '</tr>';

$pag_config_p = array('page_len' => 10000000);
$pag_config_p['col_names'] = array('*');
$pag_config_p['table_name'] = "(problems left join best_ac_submissions on best_ac_submissions.submitter = '{$myUser['username']}' and problems.id = best_ac_submissions.problem_id) join trainings_includes on id = s_id";
$pag_config_p['cond'] = 'p_id = ' . $training['id'];
$pag_config_p['tail'] = "order by id asc";
$pag_p = new Paginator($pag_config_p);

$div_classes = array('table-responsive');
$table_classes = array('table', 'table-bordered', 'table-hover', 'table-striped');

?>

<?php
$REQUIRE_LIB['shjs'] = '';
?>

<?php echoUOJPageHeader(HTML::stripTags($training['title']) . ' - ' . UOJLocale::get('training::training')) ?>

<h1 class="page-header text-center">#<?= $training['id'] ?>. <?= $training['title'] ?></h1>
<a role="button" class="btn btn-info float-right" href="/training/<?= $training['id'] ?>/statistics"><span class="glyphicon glyphicon-stats"></span> <?= UOJLocale::get('problems::statistics') ?></a>

<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" href="#tab-statement" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-book"></span> <?= UOJLocale::get('problems::statement') ?></a></li>
    <li class="nav-item"><a class="nav-link" href="#tab-list" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-list-alt"></span> <?= UOJLocale::get('training::missions') ?></a></li>
    <?php if (hasTrainingPermission($myUser, $training)) : ?>
        <li class="nav-item"><a class="nav-link" href="/training/<?= $training['id'] ?>/manage/statement" role="tab"><?= UOJLocale::get('problems::manage') ?></a></li>
    <?php endif ?>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="tab-statement">
        <?php if (strlen($training_content['statement']) == 0) : ?>
            <div class="text-center">
                <div style="font-size:233px; color:lightgray"><?= UOJLocale::get('training::no des') ?></div>
                <p><?= UOJLocale::get('training::no des comment') ?></p>
            </div>
        <?php else : ?>
            <article class="top-buffer-md"><?= $training_content['statement'] ?></article>
        <?php endif ?>
    </div>
    <div class="tab-pane" id="tab-list">
        <?php if ($training['total_tasks'] == 0) : ?>
            <div class="text-center">
                <div style="font-size:233px; color:lightgray"><?= UOJLocale::get('training::no content') ?></div>
                <p><?= UOJLocale::get('training::no content comment') ?></p>
            </div>
        <?php else : ?>

            <?php if (!$pag_p->isEmpty()) : ?>
                <h3><?= UOJLocale::get('training::included pro') ?></h3>
                <?php if ($myUser) : ?>
                    <?php
                    $perc = $training['total_tasks'] > 0 ? round(100 * $training['completed'] / $training['total_tasks']) : 0;
                    ?>
                    <div class="progress bot-buffer-no" title="<?= $training['completed'] ?> / <?= $training['total_tasks'] ?>">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $training['completed'] ?>" aria-valuemin="0" aria-valuemax="<?= $training['total_tasks'] ?>" style="width: <?= $perc ?>%; min-width: 20px;"><?= $perc ?>%</div>
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
                    echo '<tr><td class="text-center" colspan="233">' . UOJLocale::get('none') . '</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                ?>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
<?php echoUOJPageFooter() ?>