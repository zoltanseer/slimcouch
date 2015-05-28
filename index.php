<?php
	// Reuqire Slim
	require "Slim/Slim.php";
	\Slim\Slim::registerAutoloader();

	// Require Couch Wrapper
	require_once "Couch/couch.php";
	require_once "Couch/couchClient.php";
	require_once "Couch/couchDocument.php";

	// Initialize FW
	$app = new \Slim\Slim();

	// Init DBs
	$dbhost = "http://zoltanseer.iriscouch.com";
	$db['lotto649'] = new couchClient($dbhost, "lotto649");
	$db['lottoeuro'] = new couchClient($dbhost, "lottoeuro");

	// Map routes
	$app->get('/get/:name', function ($name) use ($app, $db) {
		$currentDB = $db[$name];
		try {
		   $view = $currentDB->getView($name,'getAllNumbers');
		   $app->response()->header("Content-Type", "application/json");
		   echo json_encode($view->rows);
		} catch (Exception $e) {
		   echo "ERROR::: ".$e->getMessage()."<BR>\n";
		}
	});


	// Desired form: {"numbers":[2,5,22,29,44,45],"super":0}
	$app->post('/save/:name', function ($name) use ($app, $db) {
		$currentDB = $db[$name];
		$nrs = $app->request()->getBody();
		$prep = json_decode($nrs, true);
		$insert = array("numbers" => $prep["numbers"], "super" => $prep["super"]);
		$doc = new couchDocument($currentDB);
		$doc->set($insert);
		$app->response()->header("Content-Type", "application/json");
		echo $nrs;
	});

	$app->delete('/delete/:name/:id', function ($name, $id) use ($app, $db) {
		$currentDB = $db[$name];
		$doc = new couchDocument($currentDB);
		$doc->load($id);
		$resp = $currentDB->deleteDoc($doc);
		$app->response()->header("Content-Type", "application/json");
		echo json_encode($resp);
	});

	$app->put('/update/:name/:id', function ($name, $id) use ($app, $db) {

	});


	// Run App
	$app->run();




?>