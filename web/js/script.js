$(document).ready(function(){

    $('.search_result a').attr('target', '_blank');
    $('.search_result a').click(function(){
            var $this = $(this);
            $.ajax({
                url: "index.php?m=ajax",
                type: "POST",
                data: {url: $this.attr('href'), table: 'search_item'}
            }).done(function(data) {
                if(data.click!=false)
                {
                    var show  = $('span.show', $this.parent().prev()).text();
                    $('span.click', $this.parent().prev()).html(data.click);
                    $('span.ctr', $this.parent().prev()).html((100*data.click/show).toFixed(1));
                }
            });
            return true;
        });   

    $('.news_result a').attr('target', '_blank');
    $('.news_result a').click(function(){
            var $this = $(this);
            $.ajax({
                url: "index.php?m=ajax",
                type: "POST",
                data: {url: $this.attr('href'), table: 'news_item'}
            }).done(function(data) {
                if(data.click!=false)
                {
                    var show  = $('span.show', $this.parent().prev()).text();
                    $('span.click', $this.parent().prev()).html(data.click);
                    $('span.ctr', $this.parent().prev()).html((100*data.click/show).toFixed(1));
                }
            });
            return true;
        });   

    $('.youtube_result a').attr('target', '_blank');
    $('.youtube_result a').click(function(){
            var $this = $(this);
            $.ajax({
                url: "index.php?m=ajax",
                type: "POST",
                data: {url: $this.attr('data-url'), table: 'youtube_item'}
            }).done(function(data) {
                if(data.click!=false)
                {
                    var show  = $('span.show', $this.parent().prev()).text();
                    $('span.click', $this.parent().prev()).html(data.click);
                    $('span.ctr', $this.parent().prev()).html((100*data.click/show).toFixed(1));
                }
            });
            return true;
        });   
});

