
/**
 option: 'charge', 'btn'
 */
jQuery.ieCompatibility = function(option, dWidth, dHeight, dialogTitle, dialogUrl, openBtn) {

    var dialogModal = $('#open-protocol-dialog-modal');

    // verifica qual o tipo de chamada que abrirá o dialog
    if (option === 'charge') {
        mountDialog();
    } else if (option === 'btn') {
        $('#' + openBtn).click(function() {
            mountDialog();
        });
    }

    // carrega conteúdo no dialog, via ajax
    function chargeContent() {
        $.ajax({
            type: 'POST',
            url: dialogUrl,
            success: function(res) {
                dialogModal.html(res);
            }
        });
    }

    // remove background do title e atribui outros estilos ao dialog
    function setUpDialogStyle() {
        $('.ui-dialog-titlebar').css({
            'font-size': '11px',
            'background': '#ffffff',
            'color': '#5A6883',
            'border': 'none',
            'font-family': '"Helvetica Neue", Helvetica, Arial, sans-serif'
        });
    }

    // função monta dialog
    function mountDialog() {
        chargeContent(); // carrega conteúdo, via ajax, para corpo do dialog
        dialogModal.attr('title', dialogTitle);
        dialogModal.dialog({
            position: ['center', 'center'],
            resizable: false,
            width: dWidth,
            height: dHeight,
            modal: true,
            hide: {effect: 'fade', duration: 350},
            show: {effect: 'fade', duration: 350}
        });
        setUpDialogStyle();
    }

    // captura evento de clique no botão 'Não' e fecha dialog
    $('.closeDialog').click(function() {
        dialogModal.dialog('close');
    });

};