$(document).ready(function(){
    $('a').click(function(){
        var url = $(this).attr('href');
        $.ajax({
          url: "/?m=ajax",
          type: "POST",
          data: {url: url}
        }).done(function(data) { 
          console.log(data);
        });
       return false;
    });   
});

