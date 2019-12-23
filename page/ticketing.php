<?php

if (!$me) {
    alert('로그인 후 사용가능합니다.');
    back();
}

$date = date('y-m-d');


?>
<section id="ticketing">
    <div class="container">
        <h2 class="title-left">TICKETING</h2>
        <form action="action/addTicketing" method="post">
            <div class="form-group">
                <label for="event">행사일정</label>
                <select name="event" class="custom-select" id="event">
                    <option value="">선택</option>
                    <?php
                    $events = $pdo->query("select * from events")->fetchAll();
                    foreach ($events as $index => $event) {
                        if ($date < $event->end) { ?>
                            <option value="<?= $event->idx ?>"><?= $event->start ?> ~ <?= $event->end ?></option>
                        <?php }
                    } ?>
                </select>
            </div>

            <div class="form-group">
                <p>참관인원</p>
                <input type="number" name="num" min="1" value="1" class="form-control">
            </div>


            <?php
            foreach ($events as $event) {
                $total = $event->personnel;

                $count = 0;
                $data = $pdo->query("select * from ticketings where event_idx='$event->idx'")->fetchAll();
                foreach ($data as $da) {
                    $count += $da->num;
                }

                ?>
                <div class="event-data d-none mb-5" data-idx="<?= $event->idx ?>">
                    <div class="row">
                        <!--                        <div class="img col-4">-->
                        <!--                            <img src='http://localhost/graph/--><?//= $count ?><!--/-->
                        <?//= $total ?><!--' alt='pie-chart'>-->
                        <!--                        </div>-->
                        <!--                        <div class="text col-8">-->
                        <!--                            <p>총 참관 가능 인원: --><?//= $total ?><!--</p>-->
                        <!--                            <p>예매인원: --><?//= $count ?><!--<br></p>-->
                        <!--                            <p>예매율(%): --><?//= $count / $total * 100 ?><!--%</p>-->
                        <!--                        </div>-->

                        <div class="img col-12">
                            <img src='http://localhost/graph/<?= $count ?>/<?= $total ?>' class="img-fluid"
                                 alt='pie-chart'>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <button type="submit" class="btn btn-custom2 w-100">예매하기</button>


        </form>

        <h3 class="mb-5">예매정보</h3>
        <table class="table table-light">
            <thead class="table-dark">
            <tr>
                <th>행사일정</th>
                <th>예매일</th>
                <th>참관인원 수정</th>
                <th>예매취소</th>
            </tr>
            </thead>
            <?php
            $ticketings = $pdo->query("select * from ticketings where user_idx = '$me->idx'")->fetchAll();

            if (!$ticketings) {
                echo "<tr><td colspan='3' class='tac'>정보가 없습니다.</td></tr>";
            }

            foreach ($ticketings as $ticketing) {
                $event = $pdo->query("select * from events where idx = '$ticketing->event_idx'")->fetch();
                ?>
                <tr>
                    <td><?= $event->start ?> ~ <?= $event->end ?></td>
                    <td><?= $ticketing->date ?></td>
                    <td>
                        <form action="/action/editTicketing" method="post" class="form-row">
                            <input type="text" name="idx" hidden value="<?= $ticketing->idx ?>">
                            <input type="number" class="form-control-sm col-2" name="num"
                                   value="<?= $ticketing->num ?>">
                            <button type="submit" class="btn btn-success btn-sm">참관인원 수정</button>
                        </form>
                    </td>

                    <td>
                        <?php
                        if ($date < $event->end) {
                            ?>

                            <a href="action/deleteTicketing?idx=<?= $ticketing->idx ?>"
                               class="btn btn-cancel-tickeitng btn-danger btn-sm">예매취소</a>
                        <?php } else {
                            echo '취소할 수 없습니다.';
                        } ?>
                    </td>
                </tr>
                <?php
            } ?>
        </table>
        <?php
        if ($ticketings) {
            ?>
            <form action="/action/download" method="post" enctype="multipart/form-data">
<!--                <input type="file" name="file">-->
                <button class="btn btn-primary w-100" type="submit">예매 정보 다운받기</button>
            </form>
        <?php } ?>
    </div>
</section>