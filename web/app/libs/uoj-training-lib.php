<?php
function isTrainingVisibleToUser($training, $user)
{
    return !$training['is_hidden'] || hasTrainingPermission($user, $training);
}

function isTrainingUnlockedToUser($id, $user)
{
    if (!$user) return queryTrainingCondNum($id) == 0;
    return DB::selectCount("select c_id from trainings_cond where t_id = $id AND type = 'P' AND c_id not in (select problem_id from best_ac_submissions where submitter = '${user['username']}')") == 0 &&
        DB::selectCount("select c_id from trainings_cond where t_id = $id AND type = 'T' AND c_id not in (select training_id from trainings_completion where user = '${user['username']}')") == 0;
}

function removeCompletionInformation($problem_id, $username)
{
    DB::delete("delete from trainings_completion where user = '$username' and training_id in (select p_id from trainings_includes where s_id = $problem_id)");
}

function hasTrainingPermission($user, $training)
{
    if ($user == null) {
        return false;
    }
    if (isSuperUser($user)) {
        return true;
    }
}

function queryTrainingBrief($id)
{
    return DB::selectFirst("select 
       *, 
       (select count(*) from trainings_includes where p_id = $id) total_tasks,
       (select count(*) from trainings_cond where t_id=trainings.id) total_cond,
       (select count(*) from trainings_cond where t_id=trainings.id and type = 'T') training_cond,
       (select count(*) from trainings_cond where t_id=trainings.id and type = 'P') problem_cond
       from trainings where id = $id", MYSQLI_ASSOC);
}

function queryTrainingBriefForUser($id, $username)
{
    return DB::selectFirst("select 
       *, 
       (select count(*) from trainings_includes where p_id = $id) total_tasks, 
       (select count(*) from (best_ac_submissions join trainings_includes on best_ac_submissions.problem_id = s_id) where p_id = trainings.id and best_ac_submissions.submitter = '$username') completed, 
       (select count(*) from trainings_cond where t_id=trainings.id) total_cond, 
       (select count(*) from trainings_cond where t_id=trainings.id and type = 'T') training_cond,
       (select count(*) from trainings_cond where t_id=trainings.id and type = 'P') problem_cond,
       ((select count(*) from (trainings_completion join trainings_cond on trainings_completion.training_id = c_id) where type = 'T' and t_id = trainings.id and trainings_completion.user = '$username') + (select count(*) from (best_ac_submissions join trainings_cond on best_ac_submissions.problem_id = c_id) where type = 'P' and t_id = trainings.id and best_ac_submissions.submitter = '$username')) cond_complete,
       (select count(*) from (trainings_completion join trainings_cond on trainings_completion.training_id = c_id) where type = 'T' and t_id = trainings.id and trainings_completion.user = '$username') cond_training_complete,
       (select count(*) from (best_ac_submissions join trainings_cond on best_ac_submissions.problem_id = c_id) where type = 'P' and t_id = trainings.id and best_ac_submissions.submitter = '$username') cond_problem_complete
       from trainings where id = $id", MYSQLI_ASSOC);
}

function judgeLoopCondition($parent, $son)
{
    $temp = array();
    $result = DB::query("select * from trainings_cond where type = 'T'");
    while ($row = DB::fetch($result, MYSQLI_ASSOC)) {
        $temp[] = $row;
    }
    foreach ($temp as $item) {
        if ($item['c_id'] == $parent) {
            if (!judgeLoopCondition($item['t_id'], $son)) return false;
        }
    }
    return true;
}
function queryTrainingContent($id)
{
    return DB::selectFirst("select * from trainings_contents where id = $id", MYSQLI_ASSOC);
}
function queryTrainingIncludeNum($id)
{
    return DB::selectCount("select count(*) num from trainings_includes where p_id = $id");
}
function queryTrainingCondNum($id)
{
    return DB::selectCount("select count(*) num from trainings_cond where t_id = $id");
}

function updateTrainingCompletion($id)
{
    DB::delete("delete from trainings_completion where training_id = $id");
    DB::insert("insert into trainings_completion (select $id training_id, username user, NOW() time from user_info where username not in (select username from (select username from user_info) as T2, (select distinct s_id from trainings_includes where p_id = $id) as T3 where (username, s_id) not in (select submitter username, problem_id s_id from best_ac_submissions)))");
}

function echoTraining($training)
{
    global $myUser;
    if (isTrainingVisibleToUser($training, $myUser)) {
        echo '<tr class="text-center">';
        if ($training['completed'] == $training['total_tasks'] && $training['total_tasks'] > 0) {
            echo '<td class="table-success">';
        } else {
            echo '<td>';
        }
        echo '#', $training['id'], '</td>';
        echo '<td class="text-left">';
        if ($training['is_hidden']) {
            echo '<svg width="2.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="red" xmlns="http://www.w3.org/2000/svg">
  <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
  <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
  <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
</svg>';
        }
        echo '<a href="/training/', $training['id'], '">', $training['title'], '</a>';

        echo '</td>';
        if ($myUser != null) {
            if ($training['total_cond'] > $training['cond_complete']) {
                echo <<<EOD
                <td>
                    <svg width="1.2em" height="1.2em" viewBox="0 0 16 16" class="bi bi-lock-fill" fill="gray" xmlns="http://www.w3.org/2000/svg">
                      <path d="M2.5 9a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2V9z"/>
                      <path fill-rule="evenodd" d="M4.5 4a3.5 3.5 0 1 1 7 0v3h-1V4a2.5 2.5 0 0 0-5 0v3h-1V4z"/>
                    </svg>
                </td>            
EOD;
            } else {
                $perc = $training['total_tasks'] > 0 ? round(100 * $training['completed'] / $training['total_tasks']) : 0;
                echo <<<EOD
				<td>
					<div class="progress bot-buffer-no" title="{$training['completed']} / {$training['total_tasks']}">
						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{$training['completed']}" aria-valuemin="0" aria-valuemax="{$training['total_tasks']}" style="width: $perc%; min-width: 20px;">{$perc}%</div>
					</div>
				</td>
EOD;
            }
        }
        echo "<td class='text-center'>{$training['total_tasks']}</td>";
        echo '</tr>';
    }
}
