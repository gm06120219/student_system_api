<?php

function add_student($post) {
    file_put_contents('./log.txt', '===>add_student' . "\n", FILE_APPEND);
    $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));

    // 1. open sql
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=db_student');
        $pdo->query('set names utf8;');
    } catch (Exception $e) {
        die('connect database fail' . $e->getMessage());
    }

    // 2. check session
    if (!array_key_exists("session_token", $post)) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISSTOKEN', 'msg' => 'Token miss'));
        $pdo = null;
        return $result;
    }

    session_id($post['session_token']);
    session_start();
    if ($post['session_token'] != $_SESSION["session_token"]) {
        $result = json_encode(array('code' => 'ERROR_MSG_TOKEN', 'msg' => 'Token expired or wrong', 'data' => array('session_token' => $_SESSION["session_token"])));
        $pdo = null;
        return $result;
    }

    // 3. check parameter
    if (!array_key_exists("name", $post) or !array_key_exists("sex", $post) or !array_key_exists("classid", $post)) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }

    // 4. process request
    $name = $post['name'];
    $sex = $post['sex'];
    $classid = $post['classid'];
    $sql = "insert into t_stu values(null, '{$name}', '{$sex}', '{$classid}')";
    $rw = $pdo->exec($sql);
    if ($rw > 0) {
        file_put_contents('./log.txt', 'add student "' . $name . ' success\n', FILE_APPEND);
        $result = json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'success'));
    } else {
        $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));
    }

    // 5. clean sql handle
    $pdo = null;
    return $result;
}

function delete_student($post) {
    file_put_contents('./log.txt', '===>delete_student' . "\n", FILE_APPEND);
    $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));

    // 1. open sql
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=db_student');
        $pdo->query('set names utf8;');
    } catch (Exception $e) {
        die('connect database fail' . $e->getMessage());
    }

    // 2. check session
    if (!$post['session_token']) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISSTOKEN', 'msg' => 'Token miss'));
        $pdo = null;
        return $result;
    }

    session_id($post['session_token']);
    session_start();
    if ($post['session_token'] != $_SESSION["session_token"]) {
        $result = json_encode(array('code' => 'ERROR_MSG_TOKEN', 'msg' => 'Token expired or wrong'));
        $pdo = null;
        return $result;
    }

    // 3. check parameter
    if (!array_key_exists('id', $post)) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }

    // 4. process request
    $id = $post['id'];
    $sql = "DELETE FROM `t_stu` WHERE `t_stu`.`Id` = '{$id}'";
    $rw = $pdo->exec($sql);
    if ($rw > 0) {
        $result = json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'success'));
    } else {
        $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));
    }

    // 5. clean sql handle
    $pdo = null;
    file_put_contents('./log.txt', 'result:' . $result . "\n", FILE_APPEND);
    return $result;
}

function modify_student($post) {
    $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));

    // 1. open sql
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=db_student');
        $pdo->query('set names utf8;');
    } catch (Exception $e) {
        die('connect database fail' . $e->getMessage());
    }

    // 2. check session
    if (!$post['session_token']) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISSTOKEN', 'msg' => 'Token miss'));
        $pdo = null;
        return $result;
    }

    session_id($post['session_token']);
    session_start();
    if ($post['session_token'] != $_SESSION["session_token"]) {
        $result = json_encode(array('code' => 'ERROR_MSG_TOKEN', 'msg' => 'Token expired or wrong'));
        $pdo = null;
        return $result;
    }

    // 3. check parameter
    if (!array_key_exists('id', $post) or (!array_key_exists('name', $post) and !array_key_exists('sex', $post) and !array_key_exists('classid', $post))) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }

    // 4. process request
    $id = $post['id'];

    $str = "";
    if (array_key_exists('name', $post)) {
        $str = "`Name` = '" . $post['name'] . "'";
    }

    if (array_key_exists('sex', $post)) {
        if ($str == "") {
            $str = "`Sex` = '" . $post['sex'] . "'";
        } else {
            $str = $str . ", `Sex` = '" . $post['sex'] . "'";
        }
    }

    if (array_key_exists('classid', $post)) {
        if ($str == "") {
            $str = "`Classid` = '" . $post['classid'] . "'";
        } else {
            $str = $str . ", `Classid` = '" . $post['classid'] . "'";
        }
    }

    if ($str == "") {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }
    $sql = "UPDATE `t_stu` SET " . $str . " WHERE `t_stu`.`Id` = '{$id}'";
    $rw = $pdo->exec($sql);
    if ($rw > 0) {
        $result = json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'success'));
    } else {
        $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));
    }

    // 5. clean sql handle
    $pdo = null;
    return $result;
}

function find_student($post) {
    $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));

    // 1. open sql
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=db_student');
        $pdo->query('set names utf8;');
    } catch (Exception $e) {
        die('connect database fail' . $e->getMessage());
    }

    // 2. check session
    if (!$post['session_token']) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISSTOKEN', 'msg' => 'Token miss'));
        $pdo = null;
        return $result;
    }

    session_id($post['session_token']);
    session_start();
    if ($post['session_token'] != $_SESSION["session_token"]) {
        $result = json_encode(array('code' => 'ERROR_MSG_TOKEN', 'msg' => 'Token expired or wrong'));
        $pdo = null;
        return $result;
    }

    // 3. check parameter
    if (!array_key_exists('id', $post) and !array_key_exists('name', $post) and !array_key_exists('sex', $post) and !array_key_exists('classid', $post)) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }

    // 4. process request
    $str = "";
    if (array_key_exists('id', $post)) {
        // $str = "`Id` = '" . $post['id'] . "'";
        $str = "`Id` LIKE '%" . $post['id'] . "%'";
    }

    if (array_key_exists('name', $post)) {
        if ($str == "") {
            // $str = "`Name` = '" . $post['name'] . "'";
            $str = "`Name` LIKE '%" . $post['name'] . "%'";
        } else {
            // $str = ", `Name` = '" . $post['name'] . "'";
            $str = $str . ", `Name` LIKE '%" . $post['name'] . "%'";
        }
    }

    if (array_key_exists('sex', $post)) {
        if ($str == "") {
            // $str = "`Sex` = '" . $post['sex'] . "'";
            $str = "`Sex` LIKE '%" . $post['sex'] . "%'";
        } else {
            // $str = $str . ", `Sex` = '" . $post['sex'] . "'";
            $str = $str . ", `Sex` LIKE '%" . $post['sex'] . "%'";
        }
    }

    if (array_key_exists('classid', $post)) {
        if ($str == "") {
            // $str = "`Classid` = '" . $post['classid'] . "'";
            $str = "`Classid` LIKE '%" . $post['classid'] . "%'";
        } else {
            // $str = $str . ", `Classid` = '" . $post['classid'] . "'";
            $str = $str . ", `Classid` LIKE '%" . $post['classid'] . "%'";
        }
    }

    if ($str == "") {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }

    // SELECT * FROM `t_stu` WHERE `Name` LIKE '李%'
    $sql = "SELECT * FROM `t_stu` WHERE " . $str;
    $result = $pdo->query($sql)->fetchAll();
    if (count($result) > 0) {
        $json = array('total' => count($result), 'row' => array());
        $index = 0;
        foreach ($result as $row) {
            $json['row'][$index++] = array('id' => $row['Id'], 'name' => $row['Name'], 'sex' => $row['Sex'], 'classid' => $row['Classid']);
        }
        $str = json_encode($json, JSON_UNESCAPED_UNICODE);
        $result = json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'success', 'data' => $str), JSON_UNESCAPED_UNICODE);
    } elseif (count($result) == 0) {
        $result = json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'success', 'data' => ''));
    } else {
        $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));
    }

    // // 5. clean sql handle
    $pdo = null;
    return $result;
}

function exact_find_student($post) {
    $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));

    // 1. open sql
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=db_student');
        $pdo->query('set names utf8;');
    } catch (Exception $e) {
        die('connect database fail' . $e->getMessage());
    }

    // 2. check session
    if (!$post['session_token']) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISSTOKEN', 'msg' => 'Token miss'));
        $pdo = null;
        return $result;
    }

    session_id($post['session_token']);
    session_start();
    if ($post['session_token'] != $_SESSION["session_token"]) {
        $result = json_encode(array('code' => 'ERROR_MSG_TOKEN', 'msg' => 'Token expired or wrong'));
        $pdo = null;
        return $result;
    }

    // 3. check parameter
    if (!array_key_exists('id', $post) and !array_key_exists('name', $post) and !array_key_exists('sex', $post) and !array_key_exists('classid', $post)) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }

    // 4. process request
    $str = "";
    if (array_key_exists('id', $post)) {
        // $str = "`Id` = '" . $post['id'] . "'";
        $str = "`Id` = '" . $post['id'] . "'";
    }

    if (array_key_exists('name', $post)) {
        if ($str == "") {
            // $str = "`Name` = '" . $post['name'] . "'";
            $str = "`Name` = '" . $post['name'] . "'";
        } else {
            // $str = ", `Name` = '" . $post['name'] . "'";
            $str = $str . ", `Name` = '" . $post['name'] . "'";
        }
    }

    if (array_key_exists('sex', $post)) {
        if ($str == "") {
            // $str = "`Sex` = '" . $post['sex'] . "'";
            $str = "`Sex` = '" . $post['sex'] . "'";
        } else {
            // $str = $str . ", `Sex` = '" . $post['sex'] . "'";
            $str = $str . ", `Sex` = '" . $post['sex'] . "'";
        }
    }

    if (array_key_exists('classid', $post)) {
        if ($str == "") {
            // $str = "`Classid` = '" . $post['classid'] . "'";
            $str = "`Classid` = '" . $post['classid'] . "'";
        } else {
            // $str = $str . ", `Classid` = '" . $post['classid'] . "'";
            $str = $str . ", `Classid` = '" . $post['classid'] . "'";
        }
    }

    if ($str == "") {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }

    // SELECT * FROM `t_stu` WHERE `Name` LIKE '李%'
    $sql = "SELECT * FROM `t_stu` WHERE " . $str;
    $result = $pdo->query($sql)->fetchAll();
    if (count($result) > 0) {
        $json = array('total' => count($result), 'row' => array());
        $index = 0;
        foreach ($result as $row) {
            $json['row'][$index++] = array('id' => $row['Id'], 'name' => $row['Name'], 'sex' => $row['Sex'], 'classid' => $row['Classid']);
        }
        $str = json_encode($json, JSON_UNESCAPED_UNICODE);
        $result = json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'success', 'data' => $str), JSON_UNESCAPED_UNICODE);
    } elseif (count($result) == 0) {
        $result = json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'success', 'data' => ''));
    } else {
        $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));
    }

    // // 5. clean sql handle
    $pdo = null;
    return $result;

}

function list_student($post) {
    file_put_contents('./log.txt', '===>list_student' . "\n", FILE_APPEND);
    $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));

    // 1. open sql
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=db_student');
        $pdo->query('set names utf8;');
    } catch (Exception $e) {
        die('connect database fail' . $e->getMessage());
    }

    // 2. check session
    if (!$post['session_token']) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISSTOKEN', 'msg' => 'Token miss'));
        $pdo = null;
        return $result;
    }

    session_id($post['session_token']);
    session_start();
    file_put_contents('./log.txt', 'session is: ' . $_SESSION["session_token"] . "\n", FILE_APPEND);
    file_put_contents('./log.txt', 'post session: ' . $post['session_token'] . "\n", FILE_APPEND);
    if (!$_SESSION['session_token'] or $post['session_token'] != $_SESSION["session_token"]) {
        $result = json_encode(array('code' => 'ERROR_MSG_TOKEN', 'msg' => 'Token expired or wrong', 'data' => array('session_token' => $_SESSION["session_token"])));
        $pdo = null;
        return $result;
    }

    // 3. check parameter
    if ((!array_key_exists('from', $post) and array_key_exists('count', $post)) or (array_key_exists('from', $post) and !array_key_exists('count', $post))) {
        $result = json_encode(array('code' => 'ERROR_MSG_MISS', 'msg' => 'Paramter miss'));
        $pdo = null;
        return $result;
    }

    // 4. process request
    $sql = "SELECT * FROM `t_stu`";
    if (array_key_exists('from', $post) and array_key_exists('count', $post)) {
        $sql = "SELECT * FROM `t_stu` LIMIT " . $post['from'] . ', ' . $post['count'];
    }

    $result = $pdo->query($sql)->fetchAll();
    if (count($result) > 0) {
        $json = array('total' => count($result), 'row' => array());
        $index = 0;
        foreach ($result as $row) {
            $json['row'][$index++] = array('id' => $row['Id'], 'name' => $row['Name'], 'sex' => $row['Sex'], 'classid' => $row['Classid']);
        }
        $str = json_encode($json, JSON_UNESCAPED_UNICODE);
        $result = json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'success', 'data' => $str), JSON_UNESCAPED_UNICODE);
    } elseif (count($result) == 0) {
        $result = json_encode(array('code' => 'SUCCESS_MSG', 'msg' => 'success', 'data' => ''));
    } else {
        $result = json_encode(array('code' => 'ERROR_MSG_IDC', 'msg' => 'Program error'));
    }

    file_put_contents('./log.txt', '5' . "\n", FILE_APPEND);
    // // 5. clean sql handle
    $pdo = null;

    file_put_contents('./log.txt', 'result is: ' . $result . "\n", FILE_APPEND);
    return $result;
}

if (array_key_exists("action", $_GET)) {
    switch ($_GET['action']) {
    case 'add_student':
        echo add_student($_POST);
        break;

    case 'delete_student':
        echo delete_student($_POST);
        break;

    case 'modify_student':
        echo modify_student($_POST);
        break;

    case 'find_student':
        echo find_student($_POST);
        break;

    case 'exact_find_student':
        echo exact_find_student($_POST);
        break;

    case 'list_student':
        echo list_student($_POST);
        break;

    default:
        echo json_encode(array('code' => 'ERROR_MSG_ACTION', 'msg' => 'action not exist'));
        break;
    }
} else {
    echo json_encode(array('code' => 'ERROR_MSG_NOACTION', 'msg' => 'no action'));
}
?>