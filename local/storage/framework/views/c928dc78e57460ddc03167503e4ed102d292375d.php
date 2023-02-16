<html><head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/id-card-test.css')); ?>">
    <script src="<?php echo e(asset('dist/js/jquery-1.11.1.js')); ?>"></script>
    <script>

        $(document).ready(function () {
//           alert(1);
            function getTextWidth(text, font) {
                // re-use canvas object for better performance
                var canvas = getTextWidth.canvas || (getTextWidth.canvas = document.createElement("canvas"));
                var context = canvas.getContext("2d");
                context.font = font;
                var metrics = context.measureText(text);
                return metrics.width;
            }
            var t = $("#ansar_name");
            var fs = 12;
            var v = 0;
            var font = window.getComputedStyle(document.getElementById('ansar_name'),null).getPropertyValue('font-family');
            while((v=getTextWidth(t.text(),"normal "+fs+"px "+font))>=t.width()){
                fs-=1;
                console.log(fs);

            }
//           t.css({fontSize:Math.floor(fs)+"px"})
        })
    </script>
</head>
<body>
<div id="ansar-id-card-front" style="font-family: 'SolaimanLipi',sans-serif; ">

    <div class="card-header" >
        <div class="card-header-top" style="width:100%; line-height:11px;">
            <p style="text-align:center; font-weight:600;margin: 0px; font-size:13px;"><?php echo e($rd['title']); ?></p>

        </div>
        <div class="card-header-top" style="background:<?php echo e($ad->card_color); ?>; color:#fff; text-align:center">
            <p style="text-align:center; font-size:11px; margin: 2px 0px 2px 0px;">ID No: <?php echo e($ad->geo_id); ?></p>
        </div>
        <div class="card-header-top" style="text-align:center;width:100%;">
            <img src="<?php echo URL::route('operation.info.image',['id'=>$ad->id]); ?>" style="width:100px; height: 110px;border:2px solid #000;">
        </div>
    </div>

    <div class="card-header">
        <div class="card-header-top">
            <p class="card-text"><?php echo e($rd['name']); ?> : <?php echo e($ad->name); ?></p>
            <p class="card-text"><?php echo e($rd['rank']); ?> : <?php echo e($ad->rank); ?></p>
            <p class="card-text"><?php echo e($rd['bg']); ?> : <?php echo e($ad->blood_group); ?></p>
            <p class="card-text"><?php echo e($rd['division']); ?> : <?php echo e($ad->division_name); ?></p>
            <p class="card-text"><?php echo e($rd['unit']); ?> : <?php echo e($ad->unit_name); ?></p>
            <p class="card-text"><?php echo e($rd['thana']); ?> : <?php echo e($ad->thana_name); ?></p>
            <p class="card-text"><?php echo e($rd['union']); ?> : <?php echo e($ad->union_word_text); ?></p>
            <p class="card-text"><?php echo e($rd['dob']); ?> : <?php echo e(\Carbon\Carbon::parse($ad->date_of_birth)->format('d/m/Y')); ?></p>
            <p class="card-text"><?php echo e($rd['id']); ?> : <?php echo e($id); ?></p>
            <p class="card-text"><?php echo e($rd['ed']); ?> : <?php echo e($ed); ?></p>
        </div>
    </div>

    <div class="card-header" style="overflow:hidden;margin-top:-7px; ">
        <div class="card-footer-sing" style="font-size: 11px;">
            <img src="<?php echo URL::route('operation.info.sign_image',['id'=>$ad->id]); ?>" style="width: 55px;height:30px;margin-left:8;">
            <div><?php echo e($rd['bs']); ?></div>
        </div>

        <div class="card-footer-sing" style="font-size: 11px;float: right;">
            <img src="<?php echo e(URL::to('/image').'?file=data/authority/Signature.jpg'); ?>" style="width: 55px;height:30px;left: 8">
            <div><?php echo e($rd['is']); ?></div>
        </div>
    </div>
    <div class="card-header" style="margin-top:-2px;">
        <div class="fotter_border" style=" background-color: green;"></div>
        <div class="fotter_border" style="background-color: yellow;"></div>
        <div class="fotter_border" style="background-color: darkred;"></div>
        <div class="fotter_border" style="background-color: #ffffff;"></div>
        <div class="fotter_border" style="background-color: red;"></div>
    </div>
</div>


<style>
    .card-text{
        text-align:left;
        font-size:10px;
        margin:0px;
        font-weight:bold;
    }

    .fotter_border{
        width:20%;
        float:left;
        height:10px;
    }
</style>

<style>
    @import  url('https://fonts.maateen.me/solaiman-lipi/font.css');
</style>



</body></html>