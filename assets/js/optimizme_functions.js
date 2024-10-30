(function($){
    $(document).ready(function(){

        /**
         * disable - confirm with click
         */
        $(document).on('click', '.disableConfirm', function(){
            return confirm("Are you sure you want to disable this element?");
        });


        /**
         * delete - confirm with click
         */
        $(document).on('click', '.deleteConfirm', function(){
            return confirm("Are you sure you want to delete this element?");
        })

    })
})(jQuery);