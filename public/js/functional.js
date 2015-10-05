Data = {
    showAll: function (elem) {
        $('table.table tr.data').show();
        Data.selectElement(elem);
    },

    showDiff: function (elem) {
        $('table.table tr.data').hide();
        $('table.table li.new').parent().parent().parent().show();
        Data.selectElement(elem);
    },

    selectElement: function (elem) {
        $('.panel .sp a').removeClass('active');
        $(elem).addClass('active');
    },

    getTableData: function (url) {
        $('div.modal-background iframe').attr('src', url);
        $('div.modal-background').addClass('visible');
    },

    hideTableData: function () {
        $('div.modal-background').removeClass('visible');
    }
}