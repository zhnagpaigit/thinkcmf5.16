/**
 * Created by Administrator on 2021/2/3.
 */

fnResize();
window.onresize = function() {
    fnResize();
};

function fnResize() {

    var maxWidth = window.innerWidth > 1200 ? 1200 : window.innerWidth;
   if(maxWidth == 1200)
   {
        $("#main").css("width","1200px");
   }
    document.documentElement.style.fontSize = 10/ 1200 * maxWidth + "px"; //28/2为实际的尺寸
}
// 禁止双击放大
document.documentElement.addEventListener(
    "touchstart",
    function(event) {
        if (event.touches.length > 1) {
            event.preventDefault();
        }
    },
    false
);
var lastTouchEnd = 0;
document.documentElement.addEventListener(
    "touchend",
    function(event) {
        var now = Date.now();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    },
    false
);
