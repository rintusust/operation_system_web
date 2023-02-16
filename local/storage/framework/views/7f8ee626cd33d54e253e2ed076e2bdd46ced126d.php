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
    <div id="ansar-id-card-front" style="/* font-family: 'Times New Roman',sans-serif; */">
    
        <div class="card-header">
            <div class="card-header-left-part">
                <img src="<?php echo e(asset('dist/img/idlogo.png')); ?>" class="img-responsive" style="width: 53px;margin-left: 10;/* margin-top: 5; *//* position: absolute; */">
                <img src="data:image/png;base64,<?php echo e(DNS2D::getBarcodePNG(GlobalParameter::generateSmartCard($ad->unit_code,$ad->ansar_id).'%0DRintu Chowdhury','QRCODE')); ?>" style="width: 45px;height: 45px;position: absolute;z-index: 3000;margin-left: 103;margin-top: -18.5%;">
                <img src="<?php echo e(URL::to('/image').'?file='.$ad->profile_pic); ?>" style="width: 55px;height: 65px;margin-left: 152px;margin-top: -280%;/* float: right; */position: relative;">
                <img src="data:image/png;base64,<?php echo e(DNS1D::getBarcodePNG(GlobalParameter::generateSmartCard($ad->unit_code,$ad->ansar_id),'C128')); ?>" style="margin-top: 15%;margin-left: 5">
            </div>
            <div class="card-header-right-part">
                <h4 style="margin-left: 15%;">Bangladesh Ansar and Village Defence Party</h4>
                
            </div>
        </div>
        <div class="id-no" style="/* position: relative; */text-align: center;font-size: 13px; margin-left: 35;background: #A52A2A;color: white;width: 65%;margin-top: 105;">
            <h5><?php echo e($rd['id_no']); ?>

                : <?php echo e(strcasecmp($type,'bng')==0?LanguageConverter::engToBng(GlobalParameter::generateSmartCard($ad->unit_code,$ad->ansar_id)):GlobalParameter::generateSmartCard($ad->unit_code,$ad->ansar_id)); ?></h5>
        </div>
        <div class="card-body" style="">
            
            <div class="card-body-left" style="font-size: 13px;/* font-weight: 300; */">
                <ul>
                    <li><?php echo e($rd['name']); ?><span class="pull-right"></span></li>
                    <li><?php echo e($rd['rank']); ?><span class="pull-right"></span></li>                    
                    <li><?php echo e($rd['unit']); ?><span class="pull-right"></span></li>
                    <li>Validity<span class="pull-right"></span></li>
                    <li><?php echo e($rd['bg']); ?> :</li>
                </ul>
            </div>
            <div class="card-body-middle" style="font-size: 13px;margin-left: 48;">
                <ul>
                    <li id="ansar_name" style="white-space: nowrap !important;">: <?php echo e($ad->name); ?></li>
                    <?php if($ad->rank=='Assistant Platoon Commander'): ?>
                    <li>: Asst. Platoon Commander</li>
                    <?php else: ?>
                    <li>: <?php echo e($ad->rank); ?></li>
                    <?php endif; ?>
                    <li >:<?php echo e(ucfirst(strtolower($ad->unit_name))); ?></li>
                    <li>: <?php echo e(strcasecmp($type,'bng')==0?LanguageConverter::engToBng($ed):$ed); ?></li>
                    <li style="margin-left: 26px;">   <?php echo e($ad->blood_group); ?></li>
                </ul>
            </div>
            
            
        </div>
        <div class="card-footer" style="margin-top: 60px;position: absolute;">
            <div class="card-footer-sing" style="font-size: 13px;/* position: absolute; */">
                <img src="<?php echo e(URL::to('/image').'?file='.$ad->sign_pic); ?>" style="width: 55px;height:30px;margin-left:8;/* position: absolute; */">
                <div><?php echo e($rd['bs']); ?></div>
            </div>
            
            <div class="card-footer-sing" style="font-size: 13px;float: right;/* position: absolute; */margin-left: 60px;">
                <img src="<?php echo e(URL::to('/image').'?file=data/authority/Signature.jpg'); ?>" style="width: 55px;height:30px;position: relative;left: 8">
                <div><?php echo e($rd['is']); ?></div>
            </div>
        </div>
        <h5 style="margin-top: 130px;font-size: 12px;/* margin-left: 5; */">Authority: Bangladesh Ansar &amp; VDP</h5>
    </div>
    <div>
    
    </div>
    
    
    


</body></html>