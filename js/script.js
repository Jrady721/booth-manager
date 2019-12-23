$.widget("ui.selectable", $.ui.mouse, {
    version: "1.10.4",
    options: {
        appendTo: "body",
        autoRefresh: true,
        distance: 0,
        filter: "*",
        tolerance: "touch",

        // callbacks
        selected: null,
        selecting: null,
        start: null,
        stop: null,
        unselected: null,
        unselecting: null
    },
    _create: function () {
        var selectees,
            that = this;

        this.element.addClass("ui-selectable");

        this.dragged = false;

        // cache selectee children based on filter
        this.refresh = function () {
            selectees = $(that.options.filter, that.element[0]);
            selectees.addClass("ui-selectee");
            selectees.each(function () {
                var $this = $(this),
                    pos = $this.offset();
                $.data(this, "selectable-item", {
                    element: this,
                    $element: $this,
                    left: pos.left,
                    top: pos.top,
                    right: pos.left + $this.outerWidth(),
                    bottom: pos.top + $this.outerHeight(),
                    startselected: false,
                    selected: $this.hasClass("ui-selected"),
                    selecting: $this.hasClass("ui-selecting"),
                    unselecting: $this.hasClass("ui-unselecting")
                });
            });
        };
        this.refresh();

        this.selectees = selectees.addClass("ui-selectee");

        this._mouseInit();

        this.helper = $("<div class='ui-selectable-helper'></div>");
    },

    _destroy: function () {
        this.selectees
            .removeClass("ui-selectee")
            .removeData("selectable-item");
        this.element
            .removeClass("ui-selectable ui-selectable-disabled");
        this._mouseDestroy();
    },

    _mouseStart: function (event) {
        var that = this,
            options = this.options;

        this.opos = [event.pageX, event.pageY];

        if (this.options.disabled) {
            return;
        }

        this.selectees = $(options.filter, this.element[0]);

        this._trigger("start", event);

        $(options.appendTo).append(this.helper);
        // position helper (lasso)
        this.helper.css({
            "left": event.pageX,
            "top": event.pageY,
            "width": 0,
            "height": 0
        });

        if (options.autoRefresh) {
            this.refresh();
        }

        this.selectees.filter(".ui-selected").each(function () {
            var selectee = $.data(this, "selectable-item");
            selectee.startselected = true;
            if (!event.metaKey && !event.ctrlKey) {
                selectee.$element.removeClass("ui-selected");
                selectee.selected = false;
                selectee.$element.addClass("ui-unselecting");
                selectee.unselecting = true;
                // selectable UNSELECTING callback
                that._trigger("unselecting", event, {
                    unselecting: selectee.element
                });
            }
        });

        $(event.target).parents().addBack().each(function () {
            var doSelect,
                selectee = $.data(this, "selectable-item");
            if (selectee) {
                doSelect = (!event.metaKey && !event.ctrlKey) || !selectee.$element.hasClass("ui-selected");
                selectee.$element
                    .removeClass(doSelect ? "ui-unselecting" : "ui-selected")
                    .addClass(doSelect ? "ui-selecting" : "ui-unselecting");
                selectee.unselecting = !doSelect;
                selectee.selecting = doSelect;
                selectee.selected = doSelect;
                // selectable (UN)SELECTING callback
                if (doSelect) {
                    that._trigger("selecting", event, {
                        selecting: selectee.element
                    });
                } else {
                    that._trigger("unselecting", event, {
                        unselecting: selectee.element
                    });
                }
                return false;
            }
        });

    },

    _mouseDrag: function (event) {

        this.dragged = true;

        if (this.options.disabled) {
            return;
        }

        var tmp,
            that = this,
            options = this.options,
            x1 = this.opos[0],
            y1 = this.opos[1],
            x2 = event.pageX,
            y2 = event.pageY;

        if (x1 > x2) {
            tmp = x2;
            x2 = x1;
            x1 = tmp;
        }
        if (y1 > y2) {
            tmp = y2;
            y2 = y1;
            y1 = tmp;
        }
        this.helper.css({left: x1, top: y1, width: x2 - x1, height: y2 - y1});

        this.selectees.each(function () {
            var selectee = $.data(this, "selectable-item"),
                hit = false;

            //prevent helper from being selected if appendTo: selectable
            if (!selectee || selectee.element === that.element[0]) {
                return;
            }

            if (options.tolerance === "touch") {
                hit = (!(selectee.left > x2 || selectee.right < x1 || selectee.top > y2 || selectee.bottom < y1));
            } else if (options.tolerance === "fit") {
                hit = (selectee.left > x1 && selectee.right < x2 && selectee.top > y1 && selectee.bottom < y2);
            }

            if (hit) {
                // SELECT
                if (selectee.selected) {
                    selectee.$element.removeClass("ui-selected");
                    selectee.selected = false;
                }
                if (selectee.unselecting) {
                    selectee.$element.removeClass("ui-unselecting");
                    selectee.unselecting = false;
                }
                if (!selectee.selecting) {
                    selectee.$element.addClass("ui-selecting");
                    selectee.selecting = true;
                    // selectable SELECTING callback
                    that._trigger("selecting", event, {
                        selecting: selectee.element
                    });
                }
            } else {
                // UNSELECT
                if (selectee.selecting) {
                    if ((event.metaKey || event.ctrlKey) && selectee.startselected) {
                        selectee.$element.removeClass("ui-selecting");
                        selectee.selecting = false;
                        selectee.$element.addClass("ui-selected");
                        selectee.selected = true;
                    } else {
                        selectee.$element.removeClass("ui-selecting");
                        selectee.selecting = false;
                        if (selectee.startselected) {
                            selectee.$element.addClass("ui-unselecting");
                            selectee.unselecting = true;
                        }
                        // selectable UNSELECTING callback
                        that._trigger("unselecting", event, {
                            unselecting: selectee.element
                        });
                    }
                }
                if (selectee.selected) {
                    if (!event.metaKey && !event.ctrlKey && !selectee.startselected) {
                        selectee.$element.removeClass("ui-selected");
                        selectee.selected = false;

                        selectee.$element.addClass("ui-unselecting");
                        selectee.unselecting = true;
                        // selectable UNSELECTING callback
                        that._trigger("unselecting", event, {
                            unselecting: selectee.element
                        });
                    }
                }
            }
        });

        return false;
    },

    _mouseStop: function (event) {
        var that = this;

        this.dragged = false;

        $(".ui-unselecting", this.element[0]).each(function () {
            var selectee = $.data(this, "selectable-item");
            selectee.$element.removeClass("ui-unselecting");
            selectee.unselecting = false;
            selectee.startselected = false;
            that._trigger("unselected", event, {
                unselected: selectee.element
            });
        });
        $(".ui-selecting", this.element[0]).each(function () {
            var selectee = $.data(this, "selectable-item");
            selectee.$element.removeClass("ui-selecting").addClass("ui-selected");
            selectee.selecting = false;
            selectee.selected = true;
            selectee.startselected = true;
            that._trigger("selected", event, {
                selected: selectee.element
            });
        });
        this._trigger("stop", event);

        this.helper.remove();

        return false;
    }

});

let DB = null;
let json = null;
/* 무브를 체크하는 기능.. */
let moveOut = false;

/* 앱 실행 */
$(function () {
    console.log(page)
    if (page === 'admin') {
        App()

        $(window).on('beforeunload', function () {
            if (localStorage.close) {
                if (!chkReset) {
                    localStorage.html = $('body').html()
                    localStorage.scroll = $(window).scrollTop()
                } else {
                    localStorage.removeItem('html')
                    localStorage.removeItem('scroll')
                }
            }
            localStorage.close = 'true'
        })
    } else {
        initEvents()
    }
});

/* 앱 초기화 */
async function App() {
    console.time('load')

    if (localStorage.html) {
        $('body').html(localStorage.html)
        $(window).scrollTop(localStorage.scroll)

        await initDB()

        json = JSON.parse(localStorage.json)

        initEvents()
    } else {
        $.getJSON('/data/plan.json', async res => {
            json = res
            localStorage.json = JSON.stringify(json)

            /* DB 초기화 (비동기 적 처리) (이게 가끔 퍼모먼스를 많이 먹음 막 900ms 정도... */
            await initDB();

            /* 요소 초기화 */
            initElements();

            /* 이벤트 초기화 (element 끝나면 원래 알아서 한다..) */
            // initEvents()

            /* 로딩 화면 */
            $('.loading').hide();
        })
    }
    console.log('App Start')

    /* getJSON 콜백으로 처리 */
    /* 콜백으로 res에는 불러온 결과값이 저장된다.. (json) */


    /* C에서 작성된 부분 */
    /* 연혁 */
    /* [이미지데이터, 주제, 장소, 기간, 주최, 주관, 참가업체, 관람인원, 전시품목] */
    // $.getJSON('/data/data.json', res => {
    //
    //     // 연도 정렬
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
    // })
}

/* 요소 초기화 */
function initElements() {
    // 가로 숫자 넣어주기.. <div></div> 는 맨 앞에 공백을 추가하기 위함이다.
    let colNum = '<div></div>';
    for (let i = 0; i < 80; i++) {
        colNum += `<div>${i + 1}</div>`
    }
    $('.col-num').html(colNum);

    /* 세로 숫자 넣어주기 */
    let rowNum = '';
    for (let i = 0; i < 40; i++) {
        rowNum += `<div>${i + 1}</div>`
    }
    $('.row-num').html(rowNum);

    // 기본 테이블의 뼈대 생성
    let table = '<table>'
    for (let i = 0; i < 40; i++) {
        table += `<tr class="tr${i}">`
        for (let j = 0; j < 80; j++) {
            table += `<td class="td${j}"></td>`
        }
        table += '</tr>'
    }
    table += '</table>'

    $('#layout .table-wrap').html(table)
    // 4개 붙이기
    $('.types').html(table + $(table).addClass('type1')[0].outerHTML + $(table).addClass('type2')[0].outerHTML + $(table).addClass('type3')[0].outerHTML)

    /* 길 템플릿 불러오기 (type) 이게 대략 2초더라... */
    loadTypes();

    // 부스 정보 불러오기
    loadBooth();

    /* 저장된 목록 불러오기 */
    loadSaves()
}

let chkReset = false

/* 이벤트 초기화 */
function initEvents() {
    /* 모든 이벤트 제거 (애초에 여러분 호출할 거 아니면 필요없는 코드 - 그러나 selectable, draggable 등을 초기화 하기 위해서 필요하다.) */
    $(document).unbind();

    /* 초기화 버튼 을 클릭할 시.. */
    $(document).on('click', '.btn-reset', function () {
        db('saves', 'clear').then(() => {
            chkReset = true
            alert('초기화가 완료되었습니다.')
            location.reload()
        })
    })

    $(document).on('click', '.btn-edit', function () {
        let idx = $('#layout .table-wrap').attr('data-idx')
        db('saves', 'put', {
            idx: Number(idx),
            html: $('#layout .table-wrap')[0].outerHTML
        }).then(() => {
            alert('수정되었습니다');
            loadSaves()
        })
    })

    $(document).on('input', 'select', function () {
        $(this).find('option').attr('selected', false)
        $(this).find('option:selected').attr('selected', true)
    })

    // 템플릿 선택
    $(document).on('click', '.types table', function () {
        selectType($(this))
        reset()
    });

    // 부스 선택
    $(document).on('change', '#booth', function () {
        setOption($(this).find('option:selected').text())
    });

    /* 셀렉터블 */
    /* autoRefresh 속성을 사용하면 요소가 변경 되었을 때 정상 작동 안할 수 도 있음.. 절대 하지말자.. 시발ㅇ ㄴ마ㅣ라ㅓㅇㅁ너라ㅣㅁㅇ너라 그것때문에 3시간씀.. 후 */
    $(document).find('#layout .table-wrap').selectable({
        filter: 'td',
        stop: function () {
            addBooth()
        }
    });

    // initSelectable()

    // $(document).find('.types table').draggable({
    //     zIndex: 200,
    //     stop: function () {
    //         $(this).click()
    //     }
    // })

    // $(document).on('dragend', '.types table', function (e) {
    //     console.log(e);
    // })

    // $(".types table").draggable({
    //     helper: 'clone',
    //     revert: 'invalid'
    // });
    //
    // $(".saves .table-wrap").draggable({
    //     helper: 'clone',
    //     revert: 'invalid'
    // });

    $('.types table').draggable({
        helper: "clone",
        revert: 'invalid',
        addClasses: false
    })

    $('.saves .table-wrap').draggable({
        helper: "clone",
        // revert: 'invalid',
        addClasses: false
    })

    // 부스를 드래그할경우
    $(document).find('#layout .box').draggable({
        grid: [12, 12], // 12, 12만큼 격자를 맞춘다..
        // 박스의 z-index를 200으로 설정
        zIndex: 100,
        stop: function () {
            // 부스 이동
            moveBooth()
        }
    });

    /* 부스 드랍 () */
    $(document).find('#layout table').droppable({
        tolerance: "fit", // 드롭가능한 범위를 #layout table이랑 딱 맞게 설정한다..
        drop: function (e, ui) {
            // console.log();
            if (ui.helper[0].tagName === 'TABLE') {
                ui.helper.prevObject.click()
            } else if (ui.helper.hasClass('table-wrap')) {
                ui.helper.prevObject.click()
            } else {
                // ui의 정보를 준다. (현재 작동되는 요소를)
                console.time('chk')
                moveBoothChk(ui)
                console.timeEnd('chk')
            }
        },
        out: function () { // 삐져 나가면 out!
            moveOut = true
        },
        over: function () {
            moveOut = false
            // over 는 범위 내 안착 되었을 때를 의미한다.
        }
    });

    /* 사용자 편의를 위한 나의 추가... (박스 클릭시 그 정보를 가져온다.) */
    $(document).on('click mousedown', '#layout .box', function () {
        setOption($(this).text())
    });

    // 모든 내용 비우기
    $(document).on('click', '.btn-delete', function (e) {
        reset();
        alert('삭제되었습니다.');
    });

    // 저장
    $(document).on('click', '.btn-save', function (e) {
        save()
    });

    // save 목록 삭제
    $(document).on('click', '#save .save-delete', function () {
        let idx = Number($(this).parent().attr('data-idx'));
        db('saves', 'delete', idx).then(() => {
            alert("삭제되었습니다.");
            loadSaves()
        })
    });

    // save 목록 로드
    $(document).on('click', '#save .table-wrap', function (e) {
        let idx = Number($(this).parent().attr('data-idx'));
        db('saves', 'get', idx).then(save => {
            $('#layout .table-wrap').replaceWith($(save.html).attr('data-idx', idx)[0].outerHTML);

            initEvents()

            // 첫번째 박스를 클릭한다..

            $('#layout .box').click();
        });
    });

    // C
    /* 예매 페이지 */
    $(document).on('change', '.page-ticketing #event', function () {
        console.log('22', $(this).val())

        $('.event-data').addClass('d-none')

        if ($(this).val()) {
            $(`.event-data[data-idx='${$(this).val()}']`).removeClass('d-none')
        }
    })

    if (page === 'booth') {
        // 첫번째 옵션을 선택해준다.
        $('#event option:nth-child(2)').attr('selected', true)

        let booths = $('#event option:selected').attr('data-booths')
        booths = booths.split('/')
        booths.pop()
        booths.sort()
        $('.box').css('visibility', 'hidden')

        booths.filter(function (e, i) {
            let val = e.split(':')[0]
            let disabled = e.split(':')[1]
            if (disabled === 'disabled') {
                $(`.box[data-booth='${val}']`).css('visibility', 'visible')
            }
        })

        // $('.booth').addClass('d-none')
        $(`.booth[data-idx='${$('#event').val()}']`).removeClass('d-none')
    }

    // 참가업체 부스 배치도
    $(document).on('change', '.page-booth #event', function () {
        $('.booth').addClass('d-none')

        // 박스 지우기
        $('.box').css('visibility', 'hidden')

        $('.layouts > div').hide()

        if ($(this).val()) {
            $(`.layouts > div[data-idx='${$(this).val()}']`).show()

            $(`.booth[data-idx='${$(this).val()}']`).removeClass('d-none')

            let booths = $('#event option:selected').attr('data-booths')
            booths = booths.split('/')
            booths.pop()
            booths.sort()

            booths.filter(function (e, i) {
                let val = e.split(':')[0]
                let disabled = e.split(':')[1]
                if (disabled === 'disabled') {
                    $(`.box[data-booth='${val}']`).css('visibility', 'visible')
                }
            })
        }
    })

    /* 참거업체부스신청 */

    if (page === 'request-booth') {
        $('#rBooth').html('<option value="">선택</option>')
        let booths = $('#event option:selected').attr('data-booths')

        console.log(booths);
        booths = booths.split('/')
        console.log(booths);
        booths.pop()
        booths.sort()


        booths.filter(function (e, i) {
            let val = e.split(':')[0]

            let disabled = e.split(':')[1]

            let opt = ''
            if (disabled === 'disabled') {
                opt = `<option value="${val}" disabled>${val}</option>`
            } else {
                opt = `<option value="${val}">${val}</option>`
            }
            $('#rBooth').append(opt)
        })
    }

    $(document).on('change', '.page-request-booth #event', function () {
        $('.layouts > div').hide()
        // 행사일정의 모든 부스 정보 불러오기
        $('#rBooth').html('<option value="">선택</option>')

        if ($(this).val()) {
            // 올바른 행사의 부스배치도 보여주기
            $(`.layouts > div[data-idx='${$(this).val()}']`).show()

            let booths = $('#event option:selected').attr('data-booths')
            booths = booths.split('/')
            // console.log();
            booths.pop()
            booths.sort()

            booths.filter(function (e, i) {
                let val = e.split(':')[0]

                let disabled = e.split(':')[1]

                let opt = ''
                if (disabled === 'disabled') {
                    opt = `<option value="${val}" disabled>${val}</option>`
                } else {
                    opt = `<option value="${val}">${val}</option>`
                }
                $('#rBooth').append(opt)
            })
        }
    })

    // 부스를 선택해서 사이즈값 받아오기
    $(document).on('change', '.page-request-booth #rBooth', function () {
        let idx = $('.page-request-booth #event').val()

        let size = $(`.layouts > div[data-idx='${idx}'] .box[data-booth='${$(this).val()}']`).attr('data-size')


        console.log(size);

        $('.page-request-booth #size').val(size)
    })

    // $(document).on('submit', '.page-request-booth form', function (e) {
    //     e.preventDefault()
    //
    //     $('#boo')
    // })

    /* 사이트 관리자 */
    $(document).on('submit', '.page-admin form', function (e) {
        e.preventDefault();
        let table = $('#layout .table-wrap').clone().find('table, table *').removeAttr('class').parents('.table-wrap')

        $('#html').val(table.html())

        let booths = ''
        $(table).find('.box').filter(function (i, e) {
            booths += $(e).text() + '/'
        })
        $('#booths').val(booths)

        $(this)[0].submit()
    });
}

/* functions */
function reset() {
    /* 선택된 부스를 선택해제함.. */
    $('option').first().prop('selected', true);

    /* 선택된 배경색을 없애버림 */
    $('.bg-color').css('background', 'none');

    /* 각 data-booth 정보를 없애버린다.. */
    $('#layout td').removeAttr('data-booth');

    /* 사이즈를 0으로 */
    $('.size span').text('0');

    /* 생성되어있던 박스(부스)들을 삭제함 */
    $('#layout .box').remove();
}

function save() {
    var table = $('#layout .table-wrap')[0].outerHTML;

    let data = {html: table};

    // 저장
    db('saves', 'put', data).then(() => {
        alert("저장되었습니다.");

        // 목록 불러오기
        loadSaves()
    })
}

// selectable 이벤트만 따로 하려했지만 그냥.. 안하기로함
function initSelectable() {

}

// 저장 목록 불러오기
function loadSaves() {
    // then
    db('saves').then(saves => {
        // 저장된 부스 배치도를 비워준다..
        // $('.saves').empty();

        let temp = ``

        // style 을 만들기
        let style = '<style>';
        saves.filter(function (e, i) {
            $(e.html).find('.box').filter(function (bi, box) {
                let w = $(box).width() / 3;
                let h = $(box).height() / 3;
                let l = $(box).attr('data-left') * 4;
                let t = $(box).attr('data-top') * 4;

                style += `.saves .save:nth-child(${i + 1}) .box:nth-of-type(${bi + 1}) { width: ${w}px !important; height: ${h}px !important; top: ${t}px !important; left: ${l}px !important; }`
            });

            temp += `<div class="save" data-idx="${e.idx}"> <span class="save-delete">&times;</span> ${e.html} </div>`;
        });


        style += '</style>';

        $('.saves').html(temp);
        $('body').append(style);

        /* 이벤트 초기화 */
        initEvents()
    })
}

/*  부스 이동 */
function moveBooth() {
    // 박스를 전부 돌아보면서 위치를 재 조정한다.
    $('#layout .box').filter(function (i, e) {
        // 위치를 재조정한다..
        $(e).css({
            left: Number($(e).attr('data-left')) * 12,
            top: Number($(e).attr('data-top')) * 12,
        });
    });

    // droppable 에서 영역을 넘길경우...
    if (moveOut) {
        alert("LAYOUT 영역 안에서만 이동 가능합니다.");
    }
}

// 부스 이동 가능함을 체크한다.
function moveBoothChk(ui) {
    // 드래그된 요소를 가져온다..
    let el = $(ui.draggable);

    let left = ui.position.left / 12;
    let top = ui.position.top / 12;

    // 부스 번호를 가져온다..
    let booth = $(el).attr('data-booth');

    let width = el.width() / 12;
    let height = el.height() / 12;

    let error = '';

    for (let h = 0; h < height; h++) {
        for (let w = 0; w < width; w++) {
            // BOX가 이동되는 위치의 모든 td를 가져온다.
            // 1을 더하는 이유는 index 값이 아닌 nth-child로 몇 번째 인지를 구하기 때문이다.!
            let e = $(`#layout .tr${top + h} .td${left + w}`);

            let eBooth = $(e).attr('data-booth');
            let chkRoad = $(e).hasClass('road');

            // 드롭된 부분이 겹칠경우
            if (booth !== eBooth && eBooth) {
                error = '다른 부스와 겹치기 않게 이동해주세요.\n';
            }

            /* 길이랑 겹칠 경우 */
            if (chkRoad) {
                error = '통로와 겹치기 않게 이동해주세요.\n';
            }
        }
    }

    // 에러 표시
    if (error) {
        alert(error);
    } else {
        // 데이터 변경
        $(el).attr({
            'data-left': left,
            'data-top': top,
        });

        // 기존의 값을 초기화 해주고
        $(`#layout td[data-booth="${booth}"]`).removeAttr('data-booth');

        // 바뀐 위치로 값을 이전 시켜준다.
        for (let h = 0; h < height; h++) {
            for (let w = 0; w < width; w++) {
                $(`#layout .tr${top + h} .td${left + w}`).attr('data-booth', booth);
            }
        }
    }
}

/* 템플릿 생성.. */
function loadTypes() {
    // 이 부분을 위에서 했다.. (initElements 참조)

    /* 길 추가 */
    /* 다 비동기적으로 처리했다.. */

    setTimeout(() => {
        json.road1.filter(function (e) {
            /* 이렇게 nth-child 형식으로 찾거나 eq로 찾으면 속도가 느리다 eq가 nth-child 모다 빠르면 클래스로 바로 접근하는게 더욱 빠르다... */
            $(`.types .type1 .tr${e[1] - 1} .td${e[0] - 1}`).addClass('road')
        });
    }, 0)

    setTimeout(() => {
        json.road2.filter(function (e) {
            $(`.types .type2 .tr${e[1] - 1} .td${e[0] - 1}`).addClass('road')
        });
    }, 0)

    setTimeout(() => {
        json.road3.filter(function (e) {
            $(`.types .type3 .tr${e[1] - 1} .td${e[0] - 1}`).addClass('road')
        })
    }, 0)
}

/* 템플릿 불러오기 */
function selectType(table) {
    $('#layout .table-wrap').html(table.clone());

    // // 이벤트를 초기화 ( 이벤트를 초기화 해야지 다시 selectable 이벤트 먹게 된다.. $(document).on('selectable') 이런것이 존재하지 않아서..)
    initEvents()

    // 모든 이벤트를 초기화 하는 방법은 멍청한 것 같아서 selectable 만 다시 이벤트를 달아주었다. X 안할거임
}

// 부스 불러오기
function loadBooth() {
    // 부스 정보 불러오기... (filter 는 array 형태만 참조가능 object {k : v} 형태는 $.each를 사용해야한다. )
    let temp = ``
    $.each(json.color, function (e) {
        temp += `<option value="${json.color[e]}">${e}</option>`
    })
    $('#booth').append(temp)
}

/* 옵션 변경 */
function setOption(booth) {
    let size = '0';

    let box = $(`.box[data-booth='${booth}']`);
    let color = json.color[booth];

    // 이미 박스(부스)가 존재할 경우 그 부스의 영역을 가져온다.
    if (box.length) {
        size = box.attr('data-size');
        $(`#booth option[value='${json.color[booth]}']`).prop('selected', true)
    }

    $('.bg-color').css('background', color);
    $('.size span').text(size)
}

// 부스 추가하기
function addBooth() {
    // 색상 값과, 부스의 번호 가져오기
    let color = $('#booth').val();
    let booth = $('#booth option:selected').text();

    let error = '';

    // 부스 정보가 없을 경우
    if (!color) error += "부스 번호를 선택해주세요.\n";

    // 선택된 부분에 길이 포함되어있을경우
    let roadChk = $('#layout .ui-selected.road').length;
    if (roadChk) error += "통행로는 선택할 수 없습니다.\n";

    // 다른 부스와 겹칠경우 (여기서 filter 를 사용하지 않은이유는 filter 같은경우 return false 로 루프를 벗어날수 없다... / 속도는 filter 가 위)
    $('#layout .ui-selected').each(function (i, e) {
        // 데이터 속성으로 접근할 수 있으나 데이터 속성으로 접근하면 실제 HTML 상에서는 적용이 안되어있어 확인하기 어렵다..
        // let eBooth = $(e).data('booth');
        let eBooth = $(e).attr('data-booth');

        if (booth !== eBooth && eBooth) {
            error += "다른 부스와 영역이 겹치지 않게 선택해주세요.\n";
            return false
        }
    });

    // 맨 처음 부분과 맨 마지막 부분을 가져오기
    let first = $('#layout .ui-selected').first();
    let last = $('#layout .ui-selected').last();

    // 시작 지점 설정
    let left = first.index();
    let top = first.parent().index();

    // 가로, 세로
    let width = last.index() - left + 1;
    let height = last.parent().index() - top + 1;

    let size = width * height;

    if (width < 2 || height < 2) error += "부스의 크기를 최소 2X2 이상으로 선택해주세요.\n";

    // console.log(left, top, width, height, size);

    // 에러를 처리할떄 두가지 방법을 주로 사용하는데 이렇게 error라는 것을 동시에 받아서 처리하는 방법과 즉각적으로 alert를 띄우고 return false로 처리하는 방법이 있다..
    // 그러나 이렇게 일괄적으로 처리하는 방법이 문제점은 ,, 부스를 선택 하지 않고 2x1으 부스를 그리면 2x2 라는 문구가 덮어씌어져서 error를 호출한다...
    // 즉 error 변수를 사용해서 만드는 방법은 여러 에러 문구를 동시 출력할 때 쓰는게 가장 적절하다. (단일 에러는 alert() + return false)
    if (error) {
        alert(error);
    } else {
        // 이미 그려진 박스를 삭제해준다.
        $(`#layout .box[data-booth="${booth}"]`).remove();
        $(`#layout td[data-booth="${booth}"]`).removeAttr('data-booth');

        // 새로 박스 그리기
        $('#layout .ui-selected').attr('data-booth', booth);

        // 사이즈 설정
        $('.size span').text(size);

        let box = `<div class="box" data-booth="${booth}" data-left="${left}" data-top="${top}" data-size="${size}">${booth}</div>`;

        let unit = 12;

        $(box).css({
            width: width * unit,
            height: height * unit,
            left: left * unit,
            top: top * unit,
            background: color,
        }).appendTo('#layout .table-wrap');
    }

    /* draggable 을 추가하기 위해서.. */
    initEvents()
}

// DB 초기화
function initDB() {
    return new Promise(resolve => {
        // db 열기
        let req = indexedDB.open('gangwon', 1);
        /* db 생성 */
        req.onupgradeneeded = () => {
            req.result.createObjectStore('saves', {keyPath: 'idx', autoIncrement: true})
        }
        req.onsuccess = () => {
            resolve(DB = req.result)
        }
    })
}

/* db exec */
function db(table, action = 'getAll', data = null) {
    return new Promise(resolve => {
        let req = DB.transaction(table, 'readwrite').objectStore(table)[action](data);
        // promise 객체를 이용하여 await 으로 비동기 / then 으로 동기 처리가 가능하다 (콜백 대신)
        req.onsuccess = () => resolve(req.result)
    })
}