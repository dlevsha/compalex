<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>COMPALEX - database schema compare tool</title>
    <script src="/public/js/jquery.min.js"></script>
    <script src="/public/js/functional.js"></script>
    <style type="text/css" media="all">
        @import url("/public/css/style.css");
    </style>
</head>

<body>
    <div class="modal-background" onclick="Data.hideTableData(); return false;">
        <div class="modal">
            <iframe src="" frameborder="0" style="width: 100%; height: 100%;"></iframe>
        </div>
    </div>

    <div class="compare-database-block">
        <h1>Compalex</h1>
        <h3>Database schema compare tool</h3>
        <table class="table">
            <tr class="panel">
                <td>
                    <?php
                    switch(DRIVER){
                        case 'mysql':
                        case 'mssql':
                        case 'dblib':
                            $buttons = array('tables', 'views', 'procedures', 'functions', 'keys');
                            break;
                        case 'pgsql':
                            $buttons = array('tables', 'views', 'functions', 'keys');
                            break;
                    }

                    if(!isset($_REQUEST['action'])) $_REQUEST['action'] = 'tables';
                    foreach($buttons as $li){
                        echo '<a href="/index.php?action='.$li.'"  '.($li == $_REQUEST['action'] ? 'class="active"' : '').'>'.$li.'</a>';
                    }
                    ?>

                </td>
                <td class="sp">
                    <a href="#" onclick="Data.showAll(this); return false;" class="active">all</a>
                    <a href="#" onclick="Data.showDiff(this); return false;">changed</a>

                </td>
            </tr>
            <tr class="header">
                <td>
                    <h2><?php echo FIRST_DATABASE_NAME ?></h2>
                    <span><?php $spath = explode("@", FIRST_DSN); echo end($spath); ?></span>
                </td>
                <td>
                    <h2><?php echo SECOND_DATABASE_NAME ?></h2>
                    <span><?php $spath = explode("@", SECOND_DSN); echo end($spath); ?></span>
                </td>
            </tr>
            <?php foreach($tables as $tableName=>$data){ ?>
                <tr class="data">
                    <td>
                        <h3><?php echo $tableName; ?><sup style="color: red;"><?php echo count($data['fArray']); ?></sup></h3>
                        <?php if($data['fArray']) { ?>
                            <ul style="margin-left: 20px;">
                                <?php foreach($data['fArray'] as $fieldName=>$tparam) { ?>
                                    <li <?php if(isset($tparam['isNew']) && $tparam['isNew']){ echo 'style="color: red;" class="new" '; } ?>> <b><?php echo $fieldName; ?></b> <?php echo $tparam['dtype']; ?> </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                        <?php if(count($data['fArray']) && in_array($_REQUEST['action'], array('tables', 'views'))){ ?><a target="_blank" onclick="Data.getTableData('/index.php?action=rows&baseName=<?php echo FIRST_BASE_NAME ?>&tableName=<?php echo $tableName; ?>'); return false;" href="#" class="sample-data">Sample data (<?php echo SAMPLE_DATA_LENGTH; ?> rows)</a><?php } ?>
                    </td>
                    <td>
                        <h3><?php echo $tableName; ?> <sup style="color: red;"><?php echo count($data['sArray']); ?></sup></h3>
                        <?php if($data['sArray']) { ?>
                            <ul style="margin-left: 20px;">
                                <?php foreach($data['sArray'] as $fieldName=>$tparam) { ?>
                                    <li <?php if(isset($tparam['isNew']) && $tparam['isNew']){ echo 'style="color: red;"  class="new"'; } ?>> <b><?php echo $fieldName; ?></b> <?php echo $tparam['dtype']; ?> </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                        <?php if(count($data['sArray']) && in_array($_REQUEST['action'], array('tables', 'views'))){ ?><a target="_blank" onclick="Data.getTableData('/index.php?action=rows&baseName=<?php echo SECOND_BASE_NAME ?>&tableName=<?php echo $tableName; ?>'); return false;" href="#" class="sample-data">Sample data (<?php echo SAMPLE_DATA_LENGTH; ?> rows)</a><?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

    </div>
</body>