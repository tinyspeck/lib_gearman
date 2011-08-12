lib_gearman - Easier PHP Gearman
================================

Run multiple jobs in parallel, with a timeout.
Requires the Gearman PECL extension.

    include('lib_gearman');

    $GLOBALS['cfg']['gearman_host'] = 'my_server';

    $ret = gearman_run_tasks(array(
        'task_1' => array('gearman_job_1'),
        'task_2' => array('gearman_job_2', $args),
    ), $timeout_in_ms);


    if (!$ret['ok']){
        echo "Something bad happened!\n";
        print_r($ret);
        exit;
    }

    if ($ret['tasks']['task_1']['completed']){

        $out = $ret['tasks']['task_1']['completed_data'];

        echo "task 1 completed, returned $out\n";
    }else{

        echo "task 1 did not complete\n";
        print_r($ret['tasks']['task_1']);
    }
