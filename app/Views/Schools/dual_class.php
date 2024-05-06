
<?php if( $data['dual_class_data'] ) :?>
    <h3><span  class="label border-left-primary label-striped" style="font-size: 16px">双一流专业</span></h3>
    <?php foreach ( $data['dual_class_data']  as $key=>$dual) :?>
        <span style="font-size: 12px;"><?=$key+1?></span> .<span style="margin-right: 15px;"><?=$dual['class']?></span>
    <?php endforeach;?>
<?php endif;?>

<?php if( $data['specials_data'] ):?>

    <h3><span class="label border-left-primary label-striped" style="font-size: 16px"> 开设专业 </span></h3>
    <?php foreach ( $data['specials_data']  as $key=>$item) :?>
        <span style="font-size: 12px;"><?=$key+1?></span> . <span style="margin-right: 15px;"><?=$item['special_name']?></span>
    <?php endforeach;?>
<?php endif;?>
