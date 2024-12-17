


//(function(d, s, id) {
//  var js, fjs = d.getElementsByTagName(s)[0];
//  if (d.getElementById(id)) return;
//  js = d.createElement(s); js.id = id;
//  js.src = "//connect.facebook.net/de_DE/all.js#xfbml=1";
//  fjs.parentNode.insertBefore(js, fjs);
//}(document, 'script', 'facebook-jssdk'));

$(".SocialButton").mouseenter(function(){
    $(this).addClass("SocialButtonHover");
})



/*$(".SocialButton").click(function(){
   
    alert("NAME");    
})*/


$(".SocialButton").mouseleave(function(){
    $(this).removeClass("SocialButtonHover");
})


$(".socialAdviseButton").click(function(){
    alert("Seite empfehlen");
})



$(".socialFacebookButton").click(function(){
    alert("Facebook");
})

$(".socialTwitterButton").click(function(){
    alert("Twitter");
})

$(".socialGooglePlusButton").click(function(){
    alert("GooglePlus");
})

$(".socialRssButton").click(function(){
    alert("Rss");
})


/*function social_twitter_include(){
    function(d,s,id){
        var js,fjs=d.getElementsByTagName(s)[0];
        if(!d.getElementById(id)){
            js=d.createElement(s);
            js.id=id;
            js.src='//platform.twitter.com/widgets.js';
            fjs.parentNode.insertBefore(js,fjs);
        }
    }
    (document,'script','twitter-wjs');


    //<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://cms.stefan-koelmel.com" data-via="skstefankoelmelcom" data-lang="de" data-size="large">Twittern</a>
    //<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

}*/

    window.___gcfg = {lang: 'de'};

          (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
          })();

$(document).ready(function(){
    // social_twitter_include();
      

   // alert(" startFlipTime();");
})