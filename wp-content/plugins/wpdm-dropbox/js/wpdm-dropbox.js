jQuery(function ($) {

    $('body').on('click', '.btn-dbexpore', function (e) {
        e.preventDefault();
        var fileid = $(this).data('fileid');
        var pid = $(this).data('pid');
        wpdm_bootModal("Explore", "<div id='explorec' style='max-height: 400px;overflow: auto'><div class='text-center'><i class='fas fa-sun fa-spin'></i> Loading...</div></div>", 500);
        /* ?pid=507606&fileid=P_INjle0jJEAAAAAAAAHZg */
        $.get(wpdm_url.site+"/wp-json/wpdmdropbox/v1/explore", {pid: pid, fileid: fileid}, function (res) {
            $('#explorec').html(res.html);
        });

    });

});
