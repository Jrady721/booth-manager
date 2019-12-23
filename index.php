<?php
require_once 'lib/lib.php';
console($page);
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>자동차 축제</title>

    <!-- styles -->
    <!-- 다음에 작성할 때 부트스트랩으로 기본 init을 실행한 후 짜보기로 하자... 그게 더 빠를 수도 (form 같은게 많이 들어가는 경우) -->
    <!--    <link rel="stylesheet" href="bootstrap-4.3.1-dist/css/bootstrap.css">-->

    <link rel="stylesheet" href="/fontawesome-free-5.1.0-web/css/all.css">
    <link rel="stylesheet" href="/bootstrap-4.3.1-dist/css/bootstrap.css">
    <!--    <link rel="stylesheet" href="/jquery-ui/jquery-ui.css">-->
    <link rel="stylesheet" href="/css/style.css">

    <!-- scripts -->
    <script src="/js/jquery-3.4.1.js"></script>
    <script src="/jquery-ui-1.12.1/jquery-ui.js"></script>
    <script src="/js/script.js" defer></script>
</head>
<body class="<?= $page == 'admin' ? 'admin-body' : '' ?>">

<!-- app -->
<div id="app" class="<?= ($page != 'index') ? "sub-page page-$page" : 'main-page' ?>">
    <!-- header -->
    <header class="shadow-sm">
        <nav class="navbar navbar-expand bg-white navbar-light">
            <div class="container">
                <a href="/" class="navbar-brand"><img src="/images/logo.png" alt="logo"></a>
                <ul class="navbar-nav main-menu">
                    <li class="nav-item">
                        <a href="/about" class="nav-link">빅국제모터쇼</a>
                        <ul class="sub-menu navbar-nav">
                            <li class="nav-item"><a href="/about" class="nav-link">행사소개</a></li>
                            <li class="nav-item"><a href="/history" class="nav-link">모터쇼 연혁</a></li>
                        </ul>
                    </li>
                    <?php if ($me) { ?>
                        <li class="nav-item"><a class="nav-link" href="/action/logout">로그아웃</a></li>
                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link" href="/login">로그인</a></li>
                    <?php } ?>
                    <!--                        <li class="nav-item"><a href="/login" class="nav-link">로그인/로그아웃</a></li>-->
                    <li class="nav-item"><a href="/register" class="nav-link">회원가입</a></li>
                    <li class="nav-item"><a href="/ticketing" class="nav-link">예매하기</a></li>
                    <li class="nav-item"><a href="/booth" class="nav-link">관람안내</a>
                        <ul class="sub-menu navbar-nav">
                            <li class="nav-item"><a href="/booth" class="nav-link">참가업체부스배치도</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a href="/admin" class="nav-link">관리자</a>
                        <ul class="sub-menu navbar-nav">
                            <li class="nav-item"><a href="/admin" class="nav-link">사이트관리자</a></li>
                            <li class="nav-item"><a href="/request-booth" class="nav-link">부스신청</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="social-medias row">
                    <div class="social-media col">
                        <img src="/images/Social_Icon_Media/icon1.png" alt="social-media">
                    </div>
                    <div class="social-media col">
                        <img src="/images/Social_Icon_Media/icon2.png" alt="social-media">
                    </div>
                    <div class="social-media col">
                        <img src="/images/Social_Icon_Media/icon4.png" alt="social-media">
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- main -->
    <main>
        <?php
        include_once "./page/$page.php";
        ?>
    </main>

    <?php if ($page != 'admin'): ?>
        <!-- footer -->
        <footer>
            <div class="address">
                <div class="container">
                    <div class="row">
                        <address class="col-8">
                            <div class="logo">
                                <img src="/images/logo.png" alt="logo">
                            </div>

                            부산광역시 해운대구 APEC로 55, BEXCO (우48060) <br>
                            TEL : (051)740-3520, 3516 ｜ FAX : (051)740-3404 | E-mail : bimos@bexco.co.kr
                        </address>
                        <div class="map col-4">
                            <img src="/images/map.jpg" class="img-fluid" alt="map">
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <div class="container">
                    <div class="row justify-content-between">
                        <p class="col-10">Copyright(c) Busan International Motor Show. All Rights Reserved.</p>
                        <div class="col-2 social-medias row justify-content-between">
                            <div class="social-media col">
                                <span class="fab fa-facebook"></span>
                            </div>
                            <div class="social-media col">
                                <span class="fab fa-twitter"></span>
                            </div>
                            <div class="social-media col">
                                <span class="fab fa-google-plus"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    <?php endif; ?>
</div>
<script>
    var page = '<?= $page ?>';
</script>
</body>
</html>