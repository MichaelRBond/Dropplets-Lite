<!-- jQuery & Required Scripts -->
<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>

<?php if (!IS_SINGLE && PAGINATION_ON_OFF !== "off") { ?>
<!-- Post Pagination -->
<script>
var infinite = true;
var next_page = 2;
var loading = false;
var no_more_posts = false;
$(function() {
    function load_next_page() {
        $.ajax({
            url: "index.php?page=" + next_page,
            beforeSend: function () {
                $('body').append('<article class="loading-frame"><div class="row"><div class="one-quarter meta"></div><div class="three-quarters"><img src="./templates/<?php echo(ACTIVE_TEMPLATE); ?>/loading.gif" alt="Loading"></div></div></article>');
                $("body").animate({ scrollTop: $("body").scrollTop() + 250 }, 1000);
            },
            success: function (res) {
                next_page++;
                var result = $.parseHTML(res);
                var articles = $(result).filter(function() {
                    return $(this).is('article');
                });
                        if (articles.length < 2) {  //There's always one default article, so we should check if  < 2
                            $('.loading-frame').html('You\'ve reached the end of this list.');
                        no_more_posts = true;
                    }  else {
                        $('.loading-frame').remove();
                        $('body').append(articles);
                    }
                    loading = false;
                },
                error: function() {
                    $('.loading-frame').html('An error occurred while loading posts.');
                        //keep loading equal to false to avoid multiple loads. An error will require a manual refresh
                    }
                });
}

$(window).scroll(function() {
    var when_to_load = $(window).scrollTop() * 0.32;
    if (infinite && (loading != true && !no_more_posts) && $(window).scrollTop() + when_to_load > ($(document).height()- $(window).height() ) ) {
                    // Sometimes the scroll function may be called several times until the loading is set to true.
                    // So we need to set it as soon as possible
                    loading = true;
                    setTimeout(load_next_page,500);
                }
            });
});
</script>
<?php } ?>