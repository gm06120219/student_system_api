<?php
error_reporting(E_ALL ^ E_NOTICE);
function uuid() {
    $chars = md5(uniqid(mt_rand(), true));
    $uuid = substr($chars, 0, 8) . '-'
    . substr($chars, 8, 4) . '-'
    . substr($chars, 12, 4) . '-'
    . substr($chars, 16, 4) . '-'
    . substr($chars, 20, 12);
    return $uuid;
}

function start_session($post) {
    file_put_contents('./log.txt', '===>start_session' . "\n", FILE_APPEND);
    $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));

    // 1. open sql
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=db_student');
        $pdo->query('set names utf8;');
    } catch (Exception $e) {
        die('connect database fail' . $e->getMessage());
    }

    // 2. check parameter
    if (!array_key_exists("username", $post) or !array_key_exists("password", $post)) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }

    // 3. process request
    $username = $post['username'];
    $password = $post['password'];
    $sql = "SELECT * FROM `t_admin` WHERE `Username` = '$username' AND `Password` = '$password' AND `Role` = 1";
    $result = $pdo->query($sql)->fetchAll();
    $result_count = count($result);
    if ($result_count == 1) {
        $id = uuid();
        session_id($id);
        $_SESSION['session_token'] = $id;
        file_put_contents('./log.txt', 'session is:' . $_SESSION['session_token'] . "\n", FILE_APPEND);
        $error_respon = array('code' => 'SUCCESS_MSG', 'msg' => 'Success', 'data' => array('session_token' => $_SESSION['session_token']));
        $result = json_encode($error_respon);
    } else {
        $error_respon = array('code' => 'ERROR_MSG_WRONG', 'msg' => 'Username or password wrong');
        $result = json_encode($error_respon);
    }

    // 4. clean sql handle
    $pdo = null;
    return $result;
}

function end_session($post) {
    // 1. check session
    if (!$post['session_token']) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISSTOKEN', 'msg' => 'Token miss'));
        return $result;
    }

    // 2. process request
    session_start();
    session_id($post['session_token']);
    if ($_SESSION('session_token') and $_SESSION('session_token') != $post['session_token']) {
        return json_encode(array('code' => 'ERROR_MSG_TOKEN', 'msg' => 'Token expired or wrong'));
    }
    unset($post['session_token']);
    session_destroy();
    return json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'end success'));
}

function get_current_session($post) {
    session_id($post['session_token']);
    session_start();
    $result['session_token'] = $_SESSION['session_token'];
    return json_encode($result);
}

if (array_key_exists("action", $_GET)) {
    switch ($_GET['action']) {
    case 'start_session':
        session_start();
        echo start_session($_POST);
        break;

    case 'end_session':
        echo end_session($_POST);
        break;

    case 'get_current_session':
        $result = get_current_session($_POST);
        echo $result;
        break;

    default:
        echo json_encode(array('code' => 'ERROR_MSG_ACTION', 'msg' => 'action not exist'));
        break;
    }
} else {
    echo json_encode(array('code' => 'ERROR_MSG_NOACTION', 'msg' => 'no action'));
}

?>