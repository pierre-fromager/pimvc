$('document').ready(function () {

    $('.block-profil').hover(
            function () {
                $(this).children('img').before(
                        '<span class="zoomer orangeColor upper">'
                        + 'Cliquer pour zoomer'
                        + '</span>'
                        );
                $('.zoomer').fadeIn(200);
            },
            function () {
                $('.zoomer').fadeOut(200);
                $('.zoomer').remove();
            }
    );
    
    $('.photo-profil').click(function () {
        var that = $(this);
        var url = that.attr('src');
        var html = '<img class="modal-photo-profil" src="' + url + '"></img>';
        $('#modal-body').html(html);
        $('#modal').modal('show');
    });
    
});