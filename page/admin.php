<?php
if (!($me && $me->id == 'admin')) {
    alert('사이트관리자만 접근가능합니다.');
    back();
}
?>
<div class="loading">
    <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<section id="admin">
    <div class="row justify-content-around">
        <!-- saves -->
        <div class="" id="save">
            <h2 class="title-left">SAVE</h2>
            <div class="saves"></div>
        </div>

        <div id="layout" class="">
            <h2 class="title-left">LAYOUT</h2>
            <div class="d-flex col-num"></div>
            <div class="d-flex">
                <div class="row-num"></div>
                <div class="table-wrap">
                </div>
            </div>

            <div class="settings form-inline justify-content-between">
                <div class="form-group">
                    <label for="booth" class="mr-2">부스: </label>
                    <select class="custom-select" name="booth" id="booth">
                        <option value="">선택</option>
                    </select>
                </div>
                <div class="bg-color"></div>
                <div class="size">면적: <span>0</span>m²</div>
                <div class="">
                    <button class="btn btn-save btn-custom2">저장하기</button>
                    <button class="btn btn-edit btn-custom2">수정하기</button>
                    <button class="btn btn-delete btn-custom2">삭제하기</button>
                    <button class="btn btn-reset btn-custom2">초기화</button>
                </div>
            </div>

            <form action="/action/addEvent" method="post" class="form-inline justify-content-between">
                <div class="form-group">
                    <label for="start" class="pr-3">행사시작일: </label>
                    <input class="form-control" type="text" name="start" id="start" placeholder="(yy-mm-dd)">
                </div>
                <div class="form-group">
                    <label for="start" class="pr-3">행사종료일: </label>
                    <input class="form-control" type="text" name="end" id="end" placeholder="(yy-mm-dd)">
                </div>
                <div class="form-group">
                    <label for="start" class="pr-3">참관 가능인원: </label>
                    <input class="form-control" type="text" name="personnel" id="personnel"
                           placeholder="(숫자)">
                </div>
                <input type="hidden" name="html" id="html">
                <input type="hidden" name="booths" id="booths">
                <button class="btn btn-custom2" type="submit">등록</button>
            </form>
        </div>

        <!-- types -->
        <div class="" id="type">
            <h2 class="title-left">TYPE</h2>
            <div class="types"></div>
        </div>
    </div>
</section>