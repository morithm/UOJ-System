<?php
requirePHPLib('form');
requirePHPLib('judger');

if (!validateUInt($_GET['id']) || !($problem = queryProblemBrief($_GET['id']))) {
    become404Page();
}

if (!DB::selectFirst("select * from problems_tags where problem_id = {$problem['id']} and tag = '编程游戏'")) {
    redirectTo('/problem/' . $_GET["id"]);
}

$problem_content = queryProblemContent($problem['id']);


if (isset($_GET['api'])) {
    // header("Access-Control-Allow-Origin: *");
    // header("Access-Control-Allow-Headers: *");
    header("Content-type: application/json");
    if ($_GET['api'] == 'map') {
        $handle = fopen("/var/uoj_data/${problem['id']}/ex_code_game1.in", "r") or die("Unable to open file!");
        fscanf($handle, "%d %d\n", $row, $col);
        fscanf($handle, "%[^\n]", $map);
        echo json_encode([
            "map" => $map,
            "row" => $row,
            "col" => $col
        ]);
    }

    if ($_GET['api'] == 'cmd') {
        if (Auth::check()) {
            $custom_test_submission = DB::selectFirst("select * from custom_test_submissions where submitter = '" . Auth::id() . "' and problem_id = 3 order by id desc limit 1");
            $custom_test_submission_result = json_decode($custom_test_submission['result'], true);
            $detail_str = $custom_test_submission_result['details'];
            preg_match('/\<out\>[\S]+\<\/out\>/',  $detail_str, $matches);
            echo json_encode(substr($matches[0], 5, -6));
        }
        // else {
        //     // echo json_encode("FFFFFRFF");
        //     echo json_encode("FFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFRFFFFFR");
        // }
    }
} else {
    if (!isProblemVisibleToUser($problem, $myUser)) {
        become404Page();
    }

    if (!isSuperUser($myUser) && $myUser['level'] < $problem['level']) {
        $level_text = UOJLocale::get('level', $problem['level']);
        becomeMsgPage("<h1>没有权限</h1><p>很遗憾，您的段位不足，请将段位提高到{$level_text}之后再来。</p>");
    }

    $problem_extra_config = getProblemExtraConfig($problem);
    $custom_test_requirement = getProblemCustomTestRequirement($problem);


    if ($custom_test_requirement && Auth::check()) {
        $custom_test_submission = DB::selectFirst("select * from custom_test_submissions where submitter = '" . Auth::id() . "' and problem_id = {$problem['id']} order by id desc limit 1");
        $custom_test_submission_result = json_decode($custom_test_submission['result'], true);
    }
    if ($custom_test_requirement && $_GET['get'] == 'custom-test-status-details' && Auth::check()) {
        if ($custom_test_submission == null) {
            echo json_encode(null);
        } else if ($custom_test_submission['status'] != 'Judged') {
            echo json_encode(array(
                'judged' => false,
                'html' => getSubmissionStatusDetails($custom_test_submission)
            ));
        } else {
            ob_start();
            $styler = new CustomTestSubmissionDetailsStyler();
            $styler->fade_all_details = true;
            echoJudgementDetails($custom_test_submission_result['details'], $styler, 'custom_test_details');
            $result = ob_get_contents();
            ob_end_clean();
            echo json_encode(array(
                'judged' => true,
                'html' => getSubmissionStatusDetails($custom_test_submission),
                'result' => $result
            ));
        }
        die();
    }

    function handleCustomTestUpload($zip_file_name, $content, $tot_size)
    {
        global $problem, $contest, $myUser;

        $content['config'][] = array('problem_id', $problem['id']);
        $content['config'][] = array('custom_test', 'on');
        $esc_content = DB::escape(json_encode($content));

        $language = '/';
        foreach ([$content['config']] as $row) {
            if (strEndWith($row[0], '_language')) {
                $language = $row[1];
                break;
            }
        }
        if ($language != '/') {
            Cookie::set('uoj_preferred_language', $language, time() + 60 * 60 * 24 * 365, '/');
        }
        $esc_language = DB::escape($language);

        $result = array();
        $result['status'] = "Waiting";
        $result_json = json_encode($result);

        DB::insert("insert into custom_test_submissions (problem_id, submit_time, submitter, content, status, result) values ({$problem['id']}, now(), '{$myUser['username']}', '$esc_content', '{$result['status']}', '$result_json')");
    }

    if ($custom_test_requirement) {
        $custom_test_requirement[0]['languages'] = ['C++', 'C++11'];

        $custom_test_form = newSubmissionForm(
            'custom_test',
            $custom_test_requirement,
            function () {
                return uojRandAvaiableFileName('/tmp/');
            },
            'handleCustomTestUpload'
        );
        $custom_test_form->appendHTML(
            <<<EOD
    <div id="div-custom_test_result"></div>
    EOD
        );
        $custom_test_form->succ_href = 'none';
        $custom_test_form->extra_validator = function () {
            global $ban_in_contest, $custom_test_submission;
            if ($ban_in_contest) {
                return '请耐心等待比赛结束后题目对所有人可见了再提交';
            }
            if ($custom_test_submission && $custom_test_submission['status'] != 'Judged') {
                return '上一个测评尚未结束';
            }
            return '';
        };
        $custom_test_form->ctrl_enter_submit = true;
        $custom_test_form->setAjaxSubmit(
            <<<EOD
    function(response_text) {custom_test_onsubmit(response_text, $('#div-custom_test_result')[0], '{$_SERVER['REQUEST_URI']}?get=custom-test-status-details')}
    EOD
        );
        $custom_test_form->submit_button_config['text'] = UOJLocale::get('problems::compile');
        $custom_test_form->runAtServer();
    }
}
?>
<?php
$REQUIRE_LIB['mathjax'] = '';
$REQUIRE_LIB['shjs'] = '';
$REQUIRE_LIB['react'] = '';
?>
<?php
$limit = getUOJConf("/var/uoj_data/{$problem['id']}/problem.conf");
$time_limit = $limit['time_limit'];
$memory_limit = $limit['memory_limit'];
?>
<?php if (!isset($_GET['api'])) : ?>
    <style type="text/css">
        [data-toggle="collapse"].collapsed .if-not-collapsed {
            display: none;
        }

        [data-toggle="collapse"]:not(.collapsed) .if-collapsed {
            display: none;
        }

        #form-group-custom_test_input {
            display: none;
        }
    </style>
    <?php echoUOJPageHeader(UOJLocale::get('code game')) ?>


    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" href="#tab-statement" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-book"></span> <?= UOJLocale::get('problems::statement') ?></a></li>
        <?php if ($custom_test_requirement) : ?>
            <li class="nav-item"><a class="nav-link" href="#tab-custom-test" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-console"></span> 编写代码</a></li>
        <?php endif ?>
        <?php if (hasProblemPermission($myUser, $problem)) : ?>
            <li class="nav-item"><a class="nav-link" href="/problem/<?= $problem['id'] ?>/manage/statement" role="tab"><?= UOJLocale::get('problems::manage') ?></a></li>
        <?php endif ?>
        <?php if ($contest) : ?>
            <li class="nav-item"><a class="nav-link" href="/contest/<?= $contest['id'] ?>" role="tab"><?= UOJLocale::get('contests::back to the contest') ?></a></li>
        <?php endif ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab-statement">
            <div class="row">
                <div id="code-game" data-api-url="/code-game/<?= $problem['id'] ?>/">
                </div>
                <article class="col-sm-6 col-xs-12 top-buffer-md"><?= $problem_content['statement'] ?></article>
            </div>
        </div>
        <?php if ($custom_test_requirement) : ?>
            <div class="tab-pane" id="tab-custom-test">
                <div class="top-buffer-sm"></div>
                <?php $custom_test_form->printHTML(); ?>
            </div>
        <?php endif ?>
    </div>
    <script src="/js/react-app/maze-game/bundle.js"></script>
    <?php echoUOJPageFooter() ?>

<?php endif ?>