<?php
if ($me && $me->type == '1') {

} else {
    alert('기업회원만 접근가능합니다.');
    back();
}


$date = date('y-m-d');
$chk = true;
?>

<section id="requestBooth">
    <div class="container">
        <h2 class="title-left">참가업체부스신청</h2>

        <form action="/action/reqeustBooth" method="post">

            <div class="form-group">
                <label for="event">행사일정</label>
                <select class="custom-select" name="event" id="event">
                    <option value="">선택</option>
                    <?php
                    $events = $pdo->query("select * from events order by start asc")->fetchAll();
                    foreach ($events as $index => $event) {
                        if ($date < $event->start) {
                            ?>
                            <option data-booths="<?= $event->booths ?>"
                                    value="<?= $event->idx ?>" <?php if ($chk) echo 'selected'; ?>>
                                <?= $event->start ?> ~ <?= $event->end ?></option>
                            <?php
                            $chk = false;
                        }
                    } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="rBooth">부스</label>
                <select class="custom-select" name="rBooth" id="rBooth">
                    <option value="">선택</option>
                </select>
            </div>

            <input type="hidden" name="size" id="size">

            <div class="form-group">
                <label for="item">전시품목: </label>
                <input class="form-control" type="text" id="item" name="item" placeholder="전시품목을 입력해주세요.">
            </div>

            <button class="btn btn-custom2 w-100" type="submit">신청하기</button>
        </form>

        <div class="layouts">
            <?php
            $events = $pdo->query("select * from events")->fetchAll();
            foreach ($events as $event) { ?>
                <?php
                if ($date < $event->start) {
                    echo "<div class='layout' data-idx='$event->idx'>$event->html</div>";
                }
            }
            ?>
        </div>


        <h3>부스신청 내역</h3>
        <table class="table table-striped table-light">
            <thead class="table-dark">
            <th>행사일정[행사시작일, 행사종료일]</th>
            <th>부스신청일</th>
            <th>부스번호</th>
            <th>부스크기</th>
            </thead>
            <tbody>
            <?php
            $requestBooths = $pdo->query("select * from requestBooths where user_idx = '$me->idx'")->fetchAll();
            foreach ($requestBooths as $requestBooth) {
                $event = $pdo->query("select * from events where idx = '$requestBooth->event_idx'")->fetch();
                ?>
                <tr>
                    <td><?= $event->start ?> ~ <?= $event->end ?></td>
                    <td><?= $requestBooth->date ?></td>
                    <td><?= $requestBooth->booth ?></td>
                    <td><?= $requestBooth->size ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</section>
