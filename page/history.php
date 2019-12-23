<?php
$type = isset($type) ? $type : '1';
$order = isset($order) ? (int)$order : 4;

?>
<section id="histories">
    <div class="container">
        <h2 class="title-left">HISTORIES</h2>

        <!--	[이미지데이터, 주제, 장소, 기간, 주최, 주관, 참가업체, 관람인원, 전시품목]-->
        <form method="post" action="" class="form-inline justify-content-between mb-5">
            <div>유형</div>
            <select name="type" class="custom-select">
                <option value="1" <?= $type == '1' ? 'selected' : '' ?>>주제</option>
                <option value="7" <?= $type == '7' ? 'selected' : '' ?>>관람인원</option>
                <option value="3" <?= $type == '3' ? 'selected' : '' ?>>기간</option>
            </select>
            <div>정렬방식</div>
            <select name="order" class="custom-select">
                <option value="4" <?= $order == '4' ? 'selected' : '' ?>>오름차순</option>
                <option value="3" <?= $order == '3' ? 'selected' : '' ?>>내림차순</option>
            </select>
            <button type="submit" class="btn btn-primary">검색</button>
        </form>
        <div class="histories">
            <?php
            $file = file_get_contents('./data/data.json');
            $file = explode('*/', $file)[1];
            $file = json_decode($file, true);

            foreach ($file as $key => $value) {
                if ($type == '1') {
                    $sort[$key] = $value[$type];
                } else if ($type == '3') {
                    /* 그냥 연도별로 들어와서 이렇게 해도 상관 없.. */
                    $sort[$key] = $key;
                } else {
                    $v = preg_replace("/[^0-9]*/", "", $value[$type]);
                    $sort[$key] = $v;
                }
            }

            array_multisort($sort, $order, $file);

//            foreach ($file as $key => $value) {
//                echo $value[$type] . "<br>";
//            }


            //            var_dump($file);

            // 연도 정렬
            //     let keys = Object.keys(res);
            //     keys.sort((a, b) => {
            //         return a - b;
            //     })
            //
            //     // 알고 봤더니 따로 object는 정렬 안해도 작은 값이 먼저 돌려진다..
            //     // console.log(keys);
            //
            //     $.each(res, (k, v) => {
            //         // console.log(k, v);
            //
            //         let history = `<div class="history row">
            //                     <div class="img col-4">
            //                     <img src="${v[0]}" alt="history">
            //                     </div>
            //                     <div class="text col-8">
            //                     <p>${v[1]}</p>
            //                     <p>${v[2]}</p>
            //                     <p>${v[3]}</p>
            //                     <p>${v[4]}</p>
            //                     <p>${v[5]}</p>
            //                     <p>${v[6]}</p>
            //                     <p>${v[7]}</p>
            //                     <p>${v[8]}</p>
            //                     </div>
            //                 </div>`
            //         $('.histories').append(history)
            //
            //     })

            foreach ($file as $k => $v) { ?>
                <div class="history row mb-5">
                    <div class="img col-4">
                        <img src="<?= $v[0] ?>" alt="history" class="img-fluid">
                    </div>
                    <div class="text col-8">
                        <p><?= $v[1] ?></p>
                        <p><?= $v[2] ?></p>
                        <p><?= $v[3] ?></p>
                        <p><?= $v[4] ?></p>
                        <p><?= $v[5] ?></p>
                        <p><?= $v[6] ?></p>
                        <p><?= $v[7] ?></p>
                        <p><?= $v[8] ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>