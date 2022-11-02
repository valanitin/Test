require(['jquery'],function($){
        $(document).ready(function(){
            $('#txt_search').on('input', function() {
                var searchTerm = $.trim(this.value);
                searchTerm = searchTerm.toLowerCase();
                //alert(searchTerm);
                $(".cls_filter_data .items > li").each(function(){
                    if ($(this).text().search(searchTerm) > -1){
                        $(this).show();
                    }
                    else{
                        $(this).hide();
                    }
                });
            });
        });
});

