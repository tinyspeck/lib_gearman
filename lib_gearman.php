<?php
	#
	# $Id$
	#

	function gearman_run_tasks($tasks, $timeout){

		$gmclient = new GearmanClient();
		$gmclient->addServer($GLOBALS['cfg']['gearman_host']);

		$gmclient->setCreatedCallback(	"gearman_callback_created"	);
		$gmclient->setDataCallback(	"gearman_callback_data"		);
		$gmclient->setStatusCallback(	"gearman_callback_status"	);
		$gmclient->setCompleteCallback(	"gearman_callback_complete"	);
		$gmclient->setFailCallback(	"gearman_callback_fail"		);
		$gmclient->setTimeout($timeout);

		$gearman_tasks = array();
		foreach ($tasks as $k => $v){

			$args = isset($v[1]) ? $v[1] : array();

			$gearman_tasks[$k] = $gmclient->addTask($v[0], serialize($args), null, $k);
		}

		$GLOBALS['gearman_state'] = array();

		if (!@$gmclient->runTasks()){

			if ($gmclient->returnCode() != GEARMAN_TIMEOUT){
				return array(
					'ok'	=> 0,
					'code'	=> $gmclient->getErrno(),
					'error'	=> $gmclient->error(),
					'rcode'	=> $gmclient->returnCode(),
				);
			}
		}

		return array(
			'ok'	=> 1,
			'tasks'	=> $GLOBALS['gearman_state'],
		);
	}

	function gearman_callback_created($task){
		#echo $task->unique()."->CREATED\n";
		$GLOBALS['gearman_state'][$task->unique()]['created'] = 1;
	}

	function gearman_callback_data($task){
		#echo $task->unique()."->DATA\n";
		$GLOBALS['gearman_state'][$task->unique()]['data'][] = $task->data();
	}

	function gearman_callback_status($task){
		#echo $task->unique()."->STATUS\n";
		$GLOBALS['gearman_state'][$task->unique()]['status'][] =  $task->taskNumerator() . "/" . $task->taskDenominator();
	}

	function gearman_callback_complete($task){
		#echo $task->unique()."->COMPLETE\n";
		$GLOBALS['gearman_state'][$task->unique()]['completed'] = 1;
		$GLOBALS['gearman_state'][$task->unique()]['completed_data'] = $task->data();
	}

	function gearman_callback_fail($task){
		#echo $task->unique()."->FAIL\n";
		$GLOBALS['gearman_state'][$task->unique()]['failed'] = 1;
	}
?>
