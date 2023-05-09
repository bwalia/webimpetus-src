<style>
    .page-display {
    float: right;
}
.pagination b {
    border: 1px solid #999999;
    transition: background-color .3s;
    text-decoration: none;
    padding: 8px 16px;
    color: #fff;
    background: #999999;
    float: left;

}
.pagination a:first-child {
    border-bottom-left-radius: 6px;
    border-top-left-radius: 6px;
}
.pagination a:last-child {
    border-bottom-right-radius: 6px;
    border-top-right-radius: 6px;
}
.pagination .label-pagination {
    float: left;
}
.pagination a:hover {
    background: #999999;
}

.pagination {
    display: inline-block;
    width: 100%;
}

.pagination a {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: none;
    transition: background-color .3s;
    border: 1px solid #ddd;
}
.pagination span {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: none;
    transition: background-color .3s;
    border: 1px solid #ddd;
}
.pagination a.active {
    background-color: #4CAF50;
    color: white;
    border: 1px solid #4CAF50;
}

.pagination a:hover:not(.active) {background-color: #ddd;}

.pagination-btns {
float: right;
}


</style>


<script>
    $.fn.pageMe = function (opts) {
        var $this = this,
            defaults = {
                perPage: 10,
                total: 0,
                showPrevNext: false,
                hidePageNumbers: false
            },
            settings = $.extend(defaults, opts);

        var listElement = $this;
        var perPage = settings.perPage;
        var children = listElement.children();
        var pager = $('.pager');

        if (typeof settings.childSelector != "undefined") {
            children = listElement.find(settings.childSelector);
        }

        if (typeof settings.pagerSelector != "undefined") {
            pager = $(settings.pagerSelector);
        }

        var numItems = parseInt(settings.total);
        var numPages = Math.ceil(numItems / perPage);
        //alert(numPages)

        pager.data("curr", 0);

        if (settings.showPrevNext) {
            $('<li><a href="#" class="prev_link">«</a></li>').appendTo(pager);
        }

        var curr = 0;
        // Added class and id in li start
        while (numPages > curr && (settings.hidePageNumbers == false)) {
            $('<li id="pg' + (curr + 1) + '" class="pg"><a href="#" class="page_link">' + (curr + 1) + '</a></li>').appendTo(pager);
            curr++;
        }
        // Added class and id in li end

        if (settings.showPrevNext) {
            $('<li><a href="#" class="next_link">»</a></li>').appendTo(pager);
        }

        pager.find('.page_link:first').addClass('active');
        pager.find('.prev_link').hide();
        if (numPages <= 1) {
            pager.find('.next_link').hide();
        }
        pager.children().eq(1).addClass("active");

        children.hide();
        children.slice(0, perPage).show();
        if (numPages > 3) {
            $('.pg').hide();
            $('#pg1,#pg2,#pg3').show();
            $("#pg3").after($("<li class='ell'>").html("<span>...</span>"));
        }

        pager.find('li .page_link').click(function () {
            var clickedPage = $(this).html().valueOf() - 1;
            goTo(clickedPage, perPage);
            return false;
        });
        pager.find('li .prev_link').click(function () {
            previous();
            return false;
        });
        pager.find('li .next_link').click(function () {
            next();
            return false;
        });

        function previous() {
            var goToPage = parseInt(pager.data("curr")) - 1;
            goTo(goToPage);
        }

        function next() {
            goToPage = parseInt(pager.data("curr")) + 1;
            goTo(goToPage);
        }

        function goTo(page) {
            var startAt = page * perPage,
                endOn = startAt + perPage;

            // Added few lines from here start
            pager.children().removeClass("active");
            $('.pg').hide();
            $(".ell").remove();
            var prevpg = $("#pg" + page).show();
            var currpg = $("#pg" + (page + 1)).show();
            var nextpg = $("#pg" + (page + 2)).show();
            if (prevpg.length == 0) nextpg = $("#pg" + (page + 3)).show();
            if (prevpg.length == 1 && nextpg.length == 0) {
                prevpg = $("#pg" + (page - 1)).show();
            }
            $("#pg1").show()
            if (curr > 3) {
                if (page > 1) prevpg.before($("<li class='ell'>").html("<span>...</span>"));
                if (page < curr - 2) nextpg.after($("<li class='ell'>").html("<span>...</span>"));
            }

            if (page <= numPages - 3) {
                $("#pg" + numPages.toString()).show();
            }
            $('#pg1 a, li a').removeClass("active");
            $("#pg" + (page + 1)+' a').addClass("active");
            currpg.addClass("active").siblings().removeClass("active");
            // Added few lines till here end


            //children.css('display', 'none').slice(startAt, endOn).show();

            if (page >= 1) {
                pager.find('.prev_link').show();
            } else {
                pager.find('.prev_link').hide();
            }

            if (page < (numPages - 1)) {
                pager.find('.next_link').show();
            } else {
                pager.find('.next_link').hide();
            }

            pager.data("curr", page);
            window.createPagination(page)
            // $('li.active').removeClass('active');
           
            // pager.children().eq(page + 1).addClass("active");

        }

        
    };

    
</script>