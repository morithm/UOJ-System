<?php if (isset($_GET['api'])) : ?>
    <?php
    header("Content-type: application/json");
    if ($_GET['api'] == 'map') {
        echo json_encode([
            "# #@@@ #",
            "#      #",
            "#  #   #",
            "#      #",
            "#      #",
            "#      #",
            "#      #",
            "@@@###@@"
        ]);
    }

    if ($_GET['api'] == 'cmd') {
        if (Auth::check()) {
            $custom_test_submission = DB::selectFirst("select * from custom_test_submissions where submitter = '" . Auth::id() . "' and problem_id = 3 order by id desc limit 1");
            $custom_test_submission_result = json_decode($custom_test_submission['result'], true);
            $detail_str = $custom_test_submission_result['details'];
            preg_match('/\<out\>[\S]+\<\/out\>/',  $detail_str, $matches);
            echo json_encode(substr($matches[0], 5, -6));
        } else {
            echo json_encode('FFRFF');
        }
    }
    ?>

<?php else : ?>
    <?php
    requirePHPLib('form');
    requirePHPLib('judger');

    if (!validateUInt($_GET['id']) || !($problem = queryProblemBrief($_GET['id']))) {
        become404Page();
    }


    $problem_content = queryProblemContent($problem['id']);


    if (!isProblemVisibleToUser($problem, $myUser)) {
        become404Page();
    }
    if (!isSuperUser($myUser) && $myUser['level'] < $problem['level']) {
        $level_text = UOJLocale::get('level', $problem['level']);
        becomeMsgPage("<h1>没有权限</h1><p>很遗憾，您的段位不足，请将段位提高到{$level_text}之后再来。</p>");
    }

    $submission_requirement = json_decode($problem['submission_requirement'], true);
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
            if (!hasViewPermission($problem_extra_config['view_details_type'], $myUser, $problem, $submission)) {
                $styler->fade_all_details = true;
            }
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
        foreach ($content['config'] as $row) {
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
        $custom_test_form->submit_button_config['text'] = UOJLocale::get('problems::run');
        $custom_test_form->runAtServer();
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
    <style type="text/css">
        [data-toggle="collapse"].collapsed .if-not-collapsed {
            display: none;
        }

        [data-toggle="collapse"]:not(.collapsed) .if-collapsed {
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
                <div id="code-game">
                </div>
                <div class="col-sm-6 top-buffer-md">
                    <p>请在代码中使用 <code>#include 'code_game.h'</code> 以添加支持</p>
                    <br />
                    <p>本题支持的函数如下：</p>
                    <ul>
                        <li><code>turnLeft()</code>：向左转</li>
                        <li><code>turnRight()</code>：向右转</li>
                        <li><code>forward()</code>：前进</li>
                    </ul>
                </div>
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