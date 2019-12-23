<?php $date = date('y-m-d');
?>
<section>
    <div class="container">
        <h2 class="title-left">참가업체부스배치도</h2>

        <div class="form-group">
            <label for="event">행사일정</label>
            <select name="event" class="custom-select" id="event">
                <option value="">선택</option>
                <?php
                $events = $pdo->query("select * from events order by start asc")->fetchAll();
                foreach ($events as $index => $event) {
                    if ($date < $event->end) {
                        ?>
                        <option data-booths="<?= $event->booths ?>"
                                value="<?= $event->idx ?>" <?php if ($index == 0) echo 'selected'; ?>>
                            <?= $event->start ?> ~ <?= $event->end ?></option>
                        <?php
                    }
                } ?>
            </select>
        </div>

        <div class="layouts d-flex justify-content-center">
            <?php
            $events = $pdo->query("select * from events order by start asc")->fetchAll();
            foreach ($events as $event) {
                ?>
                <?php
                if ($date < $event->end) {
                    echo "<div class='layout' data-idx='$event->idx'>$event->html</div>";
                }
            }
            ?>
        </div>

        <table class="table table-light">
            <thead class="table-dark">
            <th>참가업체명</th>
            <th>부스번호</th>
            <th>전시품목</th>
            </thead>
            <tbody>
            <?php
            $requestBooths = $pdo->query("select * from requestBooths")->fetchAll();

            foreach ($requestBooths as $requestBooth) {
                $user = $pdo->query("select * from users where idx = '$requestBooth->user_idx'")->fetch()->name;
                ?>
                <tr class="booth d-none" data-idx="<?= $requestBooth->event_idx ?>">
                    <td><?= $user ?></td>
                    <td><?= $requestBooth->booth ?></td>
                    <td><?= $requestBooth->item ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</section>