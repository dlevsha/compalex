<meta charset="utf-8">

<style type="text/css" media="all">
    @import url("public/css/style.css");
</style>

<? if (count($rows)): ?>
    <table class="data-table">
        <? foreach ($rows as $row): ?>
            <tr>
                <? foreach ($row as $rowItem): ?>
                    <td><?= $rowItem; ?></td>
                <? endforeach; ?>
            </tr>
        <? endforeach; ?>
    </table>
<? else: ?>
    <h2 class="no-records-found"><span style="text-decoration: underline;">No records found</span></h2>
<? endif; ?>
