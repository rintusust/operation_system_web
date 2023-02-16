<table>

        <?php foreach($headers as $header): ?>
        <tr>
            <?php foreach($header as $h): ?>
                <th><?php echo e($h); ?></th>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>

    <?php foreach($error_datas as $error_data): ?>
        <tr>
            <?php foreach($error_data["dd"] as $key=>$data): ?>
                <?php if(in_array($key,$error_data["err"])): ?>
                    <td style="background-color: #ff000f !important;color:#ffffff !important;"><?php echo e($data); ?></td>
                <?php else: ?>
                    <td><?php echo e($data); ?></td>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>