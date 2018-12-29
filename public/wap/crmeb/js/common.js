$(function(){
    var rightNavMove = function(){
        var mousex = 0, mousey = 0;
        var divLeft = 0, divTop = 0, left = 0, top = 0;
        document.getElementById("right-nav").addEventListener('touchstart', function(e){
            var offset = $(this).offset();
            divLeft = parseInt(offset.left,10);
            divTop = parseInt(offset.top,10);
            mousey = e.touches[0].pageY;
            mousex = e.touches[0].pageX;
        });
        document.getElementById("right-nav").addEventListener('touchmove', function(event){
            event.preventDefault();
            left = event.touches[0].pageX-(mousex-divLeft);
            top = event.touches[0].pageY-(mousey-divTop)-$(window).scrollTop();
            if(top < 1){
                top = 1;
            }
            if(top > $(window).height()-(50+$(this).height())){
                top = $(window).height()-(50+$(this).height());
            }else if(top < 40)
                top = 40;
            // if(left + $(this).width() > $(window).width()-5){
            //     console.log($(window).width());
            // }
            $(this).css({'top':top + 'px', 'position':'fixed'});
            return false;
        });
        document.getElementById("right-nav").addEventListener('touchend', function(event){
            if ((divLeft == left && divTop == top) || (top == 0 && left == 0)) {
                $(this).trigger('click');
            }
            return false;
        });
    };

    rightNavMove();
});