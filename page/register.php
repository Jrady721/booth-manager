<?php
if ($me) {
    alert('로그인된 회원은 접근할 수 없습니다.');
    back();
}
?>

<section id="register">
    <div class="container">
        <h2 class="title-left">REIGSTER</h2>
        <form action="/action/register" method="post">
            <div class="form-group">
                <label for="id">아이디: </label>
                <input class="form-control" type="text" name="id" id="id" placeholder="아이디를 입력해주세요. (이메일 형식)">
            </div>
            <div class="form-group">
                <label for="password">비밀번호: </label>
                <input class="form-control" type="password" name="password" id="password"
                       placeholder="비밀번호를 입력해주세요. (최소 4자리 이상)">
            </div>
            <div class="form-group">
                <label for="confirmPassword">비밀번호 확인: </label>
                <input class="form-control" type="password" name="confirmPassword" id="confirmPassword"
                       placeholder="비밀번호 확인을 입력해주세요.">
            </div>
            <div class="form-group">
                <label for="name">이름/업체명</label>
                <input class="form-control" type="text" name="name" id="name" placeholder="이름/업체명을 입력해주세요.">
            </div>
            <div class="form-group">
                <div class="custom-control custom-radio custom-control-inline">
                    <input class="custom-control-input" type="radio" name="type" id="type0" value="0" checked>
                    <label class="custom-control-label" for="type0">일반회원 </label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input class="custom-control-input" type="radio" name="type" id="type1" value="1">
                    <label class="custom-control-label" for="type1">기업회원 </label>
                </div>
            </div>
            <button type="submit" class="btn btn-custom2 w-100">회원가입</button>
        </form>
    </div>
</section>
