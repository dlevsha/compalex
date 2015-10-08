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
        <iframe src="" frameborder="0"></iframe>
    </div>
</div>

<div class="compare-database-block">
    <h1>Compalex</h1>

    <h3>Database schema compare tool</h3>
    <table class="table">
        <tr class="panel">
            <td>
                <?
                switch (DRIVER) {
                    case 'mysql':
                    case 'mssql':
                    case 'dblib':
                        $buttons = array('tables', 'views', 'procedures', 'functions', 'indexes');
                        break;
                    case 'pgsql':
                        $buttons = array('tables', 'views', 'functions', 'indexes');
                        break;
                }

                if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'tables';
                foreach ($buttons as $li) {
                    echo '<a href="/index.php?action=' . $li . '"  ' . ($li == $_REQUEST['action'] ? 'class="active"' : '') . '>' . $li . '</a>&nbsp;';
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
                <h2><? echo FIRST_DATABASE_NAME ?></h2>
                <span><? $spath = explode("@", FIRST_DSN);
                    echo end($spath); ?></span>
            </td>
            <td>
                <h2><? echo SECOND_DATABASE_NAME ?></h2>
                <span><? $spath = explode("@", SECOND_DSN);
                    echo end($spath); ?></span>
            </td>
        </tr>
        <? foreach ($tables as $tableName => $data) { ?>
            <tr class="data">
                <? foreach (array('fArray', 'sArray') as $blockType) { ?>
                <td>
                    <h3><? echo $tableName; ?> <sup style="color: red;"><? echo count($data[$blockType]); ?></sup></h3>
                    <div class="table-additional-info">
                        <? if(isset($additionalTableInfo[$tableName][$blockType])) {
                                foreach ($additionalTableInfo[$tableName][$blockType] as $paramKey => $paramValue) {
                                    if(strpos($paramKey, 'ARRAY_KEY') === false) echo "<b>{$paramKey}</b>: {$paramValue}<br />";
                                }
                            }
                        ?>
                    </div>
                    <? if ($data[$blockType]) { ?>
                        <ul style="margin-left: 20px;">
                            <? foreach ($data[$blockType] as $fieldName => $tparam) { ?>
                                <li <? if (isset($tparam['isNew']) && $tparam['isNew']) {
                                    echo 'style="color: red;" class="new" ';
                                } ?>><b><? echo $fieldName; ?></b> <? echo $tparam['dtype']; ?> </li>
                            <? } ?>
                        </ul>
                    <? } ?>
                    <? if (count($data[$blockType]) && in_array($_REQUEST['action'], array('tables', 'views'))) { ?><a
                        target="_blank"
                        onclick="Data.getTableData('/index.php?action=rows&baseName=<? echo FIRST_BASE_NAME ?>&tableName=<? echo $tableName; ?>'); return false;"
                        href="#" class="sample-data">Sample data (<? echo SAMPLE_DATA_LENGTH; ?> rows)</a><? } ?>
                </td>
                <? } ?>
            </tr>
        <? } ?>
    </table>

</div>
</body>