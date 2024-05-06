<li class="active" data-field="all" data-v="">
    <a href="#">
        <span>全部学校</span>
    </a>
</li>

<li class="" data-field="level" data-v="1">
    <a href="#">
        <span>普通本科</span>
    </a>
</li>

<li class="" data-field="level" data-v="2">
    <a href="#">
        <span>专科(高职)</span>
    </a>
</li>

<li class="" data-field="school_nature" data-v="0">
    <a href="#">
        <span>公办</span>
    </a>
</li>

<li class="" data-field="school_nature" data-v="1">
    <a href="#">
        <span>民办</span>
    </a>
</li>

<li class="" data-field="school_nature" data-v="2">
    <a href="#">
        <span>中外合作办学</span>
    </a>
</li>

<li class="" data-field="school_nature" data-v="3">
    <a href="#">
        <span>港澳台地区合作办学</span>
    </a>
</li>


<li class="" data-field="f985" data-v="1">
    <a href="#">
        <span>985</span>
    </a>
</li>

<li class="" data-field="f211" data-v="1">
    <a href="#">
        <span>211</span>
    </a>
</li>

<li class="" data-field="doublehigh" data-v="77004">
    <a href="#">
        <span>双高计划</span>
    </a>
</li>

<li class="" data-field="dual_class_name" data-v="双一流">
    <a href="#">
        <span>双一流</span>
    </a>
</li>



<li class="">
    <a href="#">
        <span>我的志愿</span>
    </a>
</li>
<script>
    $('.sub_menu li').on('click',function (e) {
        e.preventDefault();
        $('.sub_menu li').removeClass('active') && $(this).toggleClass('active');
        var filed = $(this).data('field'), v = $(this).data('v');
        if (filed) {
            $('.frm_search input[type=hidden]').map((k,item)=>$(item).val(''))
            if ( filed != 'all') {
                $(`.frm_search input[name=${filed}]`).val(v);
            }
            load_data();
        }
    })
</script>