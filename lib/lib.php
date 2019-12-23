<?php
/* db */
$pdo = new pdo('mysql:host=localhost; dbname=gw_last', 'root', '', array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8'
));

/* session start */
session_start();

/* date set */
date_default_timezone_set('Asia/Seoul');

/* functions */
function alert($msg)
{
    echo "<script>alert('$msg')</script>";
}

function move($url)
{
    echo "<script>location.replace('$url')</script>";
}

function back()
{
    echo "<script>history.back()</script>";
    exit();
}

function console($log)
{
    echo "<script>console.log(`$log`)</script>";
}

/* params 를 넘길 떄 1: page 2: model: */

/* libray */
extract($_REQUEST);

/* page 구하기 */
$page = isset($page) ? $page : 'index';
$params = explode('/', $page);
$page = isset($params[0]) ? $params[0] : $page;

/* 그래프 그리기 */
if ($params[0] == 'graph') {

    $font = "C:\Windows\Fonts\Malgun.ttf";

    $count = $params[1];
    $total = $params[2];

    $graph = imagecreatetruecolor(1800, 600);

    /* 색 지정 */
    $bg = imagecolorallocate($graph, 240, 240, 240);
    $white = imagecolorallocate($graph, 255, 255, 255);
    imagefill($graph, 0, 0, $white);

    $main = imagecolorallocate($graph, 233, 95, 101);

    // 투명색으로 채우기
//    imagecolortransparent($graph, 0);

    /* 기본 회색 채우기 */
    imagefilledarc($graph, 300, 300, 500, 500, 0, 360, $bg, IMG_ARC_PIE);

    $end = (($count / $total) * 360);

    if ($end != 0) {
        imagefilledarc($graph, 300, 300, 500, 500, -90, $end - 90, $main, IMG_ARC_PIE);
    }

    $text = "총 참관 가능 인원: $total";
    imagettftext($graph, 30, 0, 800, 120, imagecolorallocate($graph, 0, 0, 0), $font, $text);

    $text = "예매인원: $count";
    imagettftext($graph, 30, 0, 800, 260, imagecolorallocate($graph, 0, 0, 0), $font, $text);

    $text = "예매율(%): " . ($count / $total * 100) . "%";
    imagettftext($graph, 30, 0, 800, 400, imagecolorallocate($graph, 0, 0, 0), $font, $text);

    header('Content-type: image/png');
    imagepng($graph);
    
    exit();
}

$me = isset($_SESSION['me']) ? $_SESSION['me'] : null;
if ($params[0] == 'action') {
    $action = $params[1];
    if ($action == 'register') {
        $error = '';
        /* 일부러 공백 체크 안했음 trim() */

        if ($id == '') {
            $error .= '아이디를 입력해주세요.\n';
        } else if (!filter_var($id, FILTER_VALIDATE_EMAIL)) {
            $error .= '아이디가 올바른 이메일 형식이 아닙니다.\n';
        }

        if ($password == '') {
            $error .= '비밀번호를 입력해주세요.\n';
        } else if (strlen($password) < 4) {
            $error .= '비밀번호는 최소 4자리 이상이어야합니다.\n';
        } else if ($confirmPassword == '') {
            $error .= '비밀번호 확인을 입력해주세요.\n';
        } else if ($password != $confirmPassword) {
            $error .= '비밀번호와 비밀번호 확인이 일치하지 않습니다.\n';
        }

        if ($name == '') {
            $error .= '이름/업체명을 입력해주세요.\n';
        }

        /* ? 아이디 중복체크도 안하고 암호화도 안하고.. */
        if ($error != '') {
            alert($error);
            back();
        } else {
            $pdo->query("insert into users(id, password, name, type) values('$id', '$password', '$name', '$type')");
            alert('회원가입이 완료되었습니다.');
            move('/');
        }
    } else if ($action == 'login') {
//        $_SESSION['me'] =
        $me = $pdo->query("select * from users where id = '$id' and password = '$password'")->fetch();
        if ($me) {
            $_SESSION['me'] = $me;
            alert('로그인이 완료되었습니다.');
            move('/');
        } else {
            alert('로그인에 실패하였습니다.');
            back();
        }
    } else if ($action == 'logout') {
        session_destroy();
        alert('로그아웃이 완료되었습니다.');
        move('/');
    } else if ($action == 'addEvent') {
        $error = '';
        if ($start == '') {
            $error .= '행사시작일을 입력해주세요.\n';
        } else if (!preg_match('/^\d\d-\d\d-\d\d$/', $start)) {
            $error .= '행사시작일을 yy-mm-dd 형식으로 작성해주세요.\n';
        }

        if ($end == '') {
            $error .= '행사종료일을 입력해주세요.\n';
        } else if (!preg_match('/^\d\d-\d\d-\d\d$/', $end)) {
            $error .= '행사종료일을 yy-mm-dd 형식으로 작성해주세요.\n';
        }

        if ($personnel == '') {
            $error .= '참관 가능인원을 입력해주세요.\n';
        } else if (!preg_match('/^\d+$/', $personnel)) {
            $error .= '참관 가능인원은 숫자로 입력해주세요.\n';
        }

        $events = $pdo->query("select * from events")->fetchAll();

        $chk = false;

        // 그런데 확실한 문제가 있다.... 애초에 들어온값이 이상하면 정상작동안함 예로 처음부터 end가 start 보다 작다거나 그런 경우나 제대로된 날짜가 아닐떄 33-33-33 이런거.. text로 받은 한계랄까...
        foreach ($events as $event) {
            $event->start;
            // 시작이 요소의 시작보다 크거나 같은데 요소의 끝보다 작을 경우 겹치거나 일치하는 경우
            if (!($start > $event->end || $end < $event->start)) {
                $chk = true;
                break;
            }
        }
        if ($chk) $error .= '행사일정이 겹칩니다.\n';

        if ($error) {
            alert($error);
            back();
        } else {
            $pdo->query("insert into events(start, end, personnel, html, booths) values('$start', '$end', '$personnel', '$html', '$booths')");

            alert('행사일정이 등록되었습니다.');
            back();
        }
    } else if ($action == 'addTicketing') {

        if ($event == '') {
            alert('행사일정을 선택해주세요.');
            back();
        }

        $total = $pdo->query("select * from events where idx = '$event'")->fetch()->personnel;

        $count = 0;
        $sharedData = $pdo->query("select * from ticketings where event_idx = '$event'")->fetchAll();
        foreach ($sharedData as $da) {
            $count += $da->num;
        }

        if ($total < $count + $num) {
            alert('총 참관 가능 인원의 정원을 초과하여 예매가 불가능합니다.');
            back();
        } else {
            $pdo->query("insert into ticketings(user_idx, event_idx, date, num) values('$me->idx', '$event', now(), '$num')");
            alert('예매가 완료되었습니다.');
            move('/ticketing');
        }

    } else if ($action == 'editTicketing') {
        $pdo->query("update ticketings set num = '$num' where idx = '$idx'");
        alert('수정완료');
        move('/ticketing');
    } else if ($action == 'deleteTicketing') {
        $pdo->query("delete from ticketings where idx = '$idx'");
        alert('예매가 취소되었습니다.');
        move('/ticketing');
    } else if ($action == 'reqeustBooth') {
        if ($event == '') {
            alert('행사일정을 선택해주세요.');
        } else if ($rBooth == '') {
            alert('부스를 선택해주세요.');
        } else if ($item == '') {
            alert('전시품목을 입력해주세요.');
        } else {
//        $event = $pdo->query("select * from events where idx = '$event'");
            $pdo->query("insert into requestBooths(event_idx, date, booth, size, user_idx, item) values('$event', now(), '$rBooth', '$size', '$me->idx', '$item')");

            $eBooths = $pdo->query("select * from events where idx = '$event'")->fetch()->booths;
            $eBooths = str_replace("$rBooth/", "$rBooth:disabled/", $eBooths);
            $pdo->query("update events set booths = '$eBooths' where idx = '$event'");

            alert('성공적으로 부스신청이 완료되었습니다.');
            move('/request-booth');
        }
        back();

    } else if ($action == 'download') {
        define('ROOT', $_SERVER['DOCUMENT_ROOT']);

        if (!is_dir(ROOT . '/excel')) {
            mkdir(ROOT . '/excel');
        }

        $dir = $_SERVER['DOCUMENT_ROOT'] . '/data2/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        /* 이렇게 다운로드 진행하면 data2 폴더에 기본 xlsx 파일 정보를 만들어 두지 않으면.. 작동안됨.. */
//        /* 압축풀기 */
//        if ($_FILES['file']['name']) {
//            $file = $_FILES['file'];
//            $zip = new \ZipArchive();
//            $zip->open($file['tmp_name']);
//            $zip->extractTo($dir);
//
//            back();
//        } else {
        $sharedData = '';
        $sheetData = "";

        /* 데이터 삽입 */
        $ticketings = $pdo->query("select * from ticketings where user_idx = '$me->idx'")->fetchAll();
        foreach ($ticketings as $index => $ticketing) {
            $event = $pdo->query("select * from events where idx = '$ticketing->event_idx'")->fetch();

            $sharedData .= "<si><t>$event->start ~ $event->end</t></si><si><t>$ticketing->date</t></si><si><t>$ticketing->num</t></si>";
            $sheetData .= "<row><c t='s'><v>" . (($index + 1) * 3) . "</v></c><c t='s'><v>" . (($index + 1) * 3 + 1) . "</v></c><c t='s'><v>" . (($index + 1) * 3 + 2) . "</v></c></row>";
        }

        $shared =
            "<sst xmlns=\"http://schemas.openxmlformats.org/spreadsheetml/2006/main\">
                    <si><t>행사일정</t></si>
                    <si><t>예매일</t></si>
                    <si><t>참관인원</t></si>
                    $sharedData
                </sst>";

        $sheet = "<worksheet xmlns=\"http://schemas.openxmlformats.org/spreadsheetml/2006/main\">
                        <sheetData>
                            <row>
                                <c t=\"s\">
                                    <v>0</v>
                                </c>
                                <c t=\"s\">
                                    <v>1</v>
                                </c>
                                <c t=\"s\">
                                    <v>2</v>
                                </c>
                            </row>
                            $sheetData
                        </sheetData>
                    </worksheet>";


        $handle = fopen(ROOT . '/data2/xl/sharedStrings.xml', 'w');
        fwrite($handle, $shared);
        fclose($handle);

        $handle = fopen(ROOT . '/data2/xl/worksheets/sheet1.xml', 'w');
        fwrite($handle, $sheet);
        fclose($handle);

        function addFolderToZip($dir, $zipArchive, $zipdir = '')
        {
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {

                    //Add the directory
                    if (!empty($zipdir)) $zipArchive->addEmptyDir($zipdir);

                    // Loop through all the files
                    while (($file = readdir($dh)) !== false) {

                        //If it's a folder, run the function again!
                        if (!is_file($dir . $file)) {
                            // Skip parent and root directories
                            if (($file !== ".") && ($file !== "..")) {
                                addFolderToZip($dir . $file . "/", $zipArchive, $zipdir . $file . "/");
                            }

                        } else {
                            // Add the files
                            $zipArchive->addFile($dir . $file, $zipdir . $file);

                        }
                    }
                }
            }
        }

        $zipname = "예매정보.xlsx";
        $zip = new \ZipArchive;
        $zip->open(ROOT . '/excel/' . $zipname, \ZipArchive::CREATE);

        addFolderToZip(ROOT . '/data2/', $zip);

        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipname);
        header('Content-Length: ' . filesize(ROOT . '/excel/' . $zipname));
        readfile(ROOT . '/excel/' . $zipname);
//        }
    }

    exit();
}
