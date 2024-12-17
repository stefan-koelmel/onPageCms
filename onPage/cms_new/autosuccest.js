  //<![CDATA[

  var a1;
  alert("Hier");
  jQuery(function() {

    var options = {
      serviceUrl: $('#queryCat').attr('url'), //'cms_base/getData/category.php',
      width: 300,
      delimiter: /(,|;)\s*/,
      deferRequestBy: 0, //miliseconds
      params: { country: 'Yes' },
      noCache: false //set to true, to disable caching
    };

    a1 = $('#queryCat').autocomplete(options);

   /* $('#navigation a').each(function() {
      $(this).click(function(e) {
        var element = $(this).attr('href');
        $('html').animate({ scrollTop: $(element).offset().top }, 300, null, function() { document.location = element; });
        e.preventDefault();
      });
    });*/

  });

//]]>