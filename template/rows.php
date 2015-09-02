<meta charset="utf-8">

<style type="text/css" media="all">
    @import url("/public/css/style.css");
</style>

<? if(count($rows)){ ?>
    <table class="data-table">
        <? foreach($rows as $row){ ?>
            <tr>
                <? foreach($row as $rowItem){ ?>
                    <td><? echo $rowItem; ?></td>
                <? } ?>
            </tr>
        <? } ?>
    </table>
<? }else{ ?>
    <h2 class="no-records-found"><u>No records found</u></h2>
<? } ?>
