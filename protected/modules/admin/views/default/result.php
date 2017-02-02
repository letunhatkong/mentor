<?php $this->pageTitle=Yii::app()->name . ' - Search User'?>
<div class="clearfix margin-top"></div>
<div class="container container-admin">
    <?php 
    $size = 0;
    if ( isset($result) && is_array($result) ) { $size = count($result); }
    ?>
    <p>Result: <?php echo $size ; ?> users</p>
    <table class="table table-responsive">
        <thead>
            <tr>
                <th>Username</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>Created Date</th>
                <th>Last seen</th>
            </tr>
        </thead>
        <tbody>
        <?php if($size > 0): ?>
            <?php foreach($result as $item): ?>
                <tr>
                    <td><?php echo $item->username ?></td>
                    <td><?php echo $item->firstName ?></td>
                    <td><?php echo $item->lastName ?></td>
                    <td><?php echo $item->email ?></td>
                    <td><?php echo date('y.m.d',STRTOTIME($item->dateCreate) ) ?></td>
                    <td><?php echo $item->lastSeen ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>