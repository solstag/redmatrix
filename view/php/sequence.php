<!DOCTYPE html >
<html>
<head>
  <title><?php if(x($page,'title')) echo $page['title'] ?></title>
  <?php if(x($a->page,'htmlhead')) echo $a->page['htmlhead'] ?>
  <link rel="stylesheet" href="<?php echo $a->get_baseurl().'/addon/courses/jquery-ui/jquery-ui.css' ?>">
  <script src="<?php echo $a->get_baseurl().'/addon/courses/jquery-ui/jquery-ui.js' ?>"></script>
  <script>
  var baseurl="<?php echo $a->get_baseurl() ?>";
  var modulename="<?php echo argv(0) ?>";
  var channelname="<?php echo argv(1) ?>";
  var pagename="<?php echo argv(2) ?>";
  var makeTabs = function(selector) {
      var numtabs = $( selector ).find( "> ul a" ).length;
      var seqlastenabled = $( "#accordion [href='page/"+channelname+"/"+pagename+"']" ).attr("data").split(" ").length-1;

      $( selector ) // change hrefs to full URL as "base href" is set
          .find( "> ul a" ).each( function() {
              var href = $( this ).attr( "href" ),
                  newHref = window.location.protocol + '//' + window.location.hostname +
                      window.location.pathname + window.location.search + href;

              if ( href.indexOf( "#" ) == 0 ) {
                  $( this ).attr( "href", newHref );
              }
          })

      var updateSeqButtons = function(index) {
		  if(index==0 || index===false)
		      $(".sequence-button-previous").attr("disabled","disabled");
		  else
		      $(".sequence-button-previous").removeAttr("disabled");
		  if(index==numtabs-1 || index===false)
		      $(".sequence-button-next").attr("disabled","disabled");
		  else
		      $(".sequence-button-next").removeAttr("disabled");
      }

      if (seqlastenabled < 0) seqlastenabled = 0;
      var seqdisabled = [];
      for(var i=seqlastenabled+1; i<numtabs; i++){ seqdisabled.push(i); }

      $( selector ).tabs({
          show: true,
          disabled: seqdisabled,
          active: seqlastenabled,
          create: function( e, ui ) { updateSeqButtons(ui.tab.index()); },
          beforeActivate: function( e, ui ) { updateSeqButtons(ui.newTab.index()); },
          activate: function( e, ui ) { $.ajax(baseurl+'/courses/click/'+modulename+'/'+channelname+'/'+pagename+'/'+ui.oldTab.attr('id')); }
      });

      $( ".sequence-button-previous" ).click(function() {
          var newindex = $( selector ).tabs( "option", "active") - 1 ;
          $( selector ).tabs( "option", "active", newindex );
      });
      $( ".sequence-button-next" ).click(function() {
          var newindex = $( selector ).tabs( "option", "active") + 1 ;
          $( selector ).tabs( "enable", newindex );
          $( selector ).tabs( "option", "active", newindex );
      });
      var activeindex=$( selector ).tabs( "option", "active");
      for(var i=activeindex; i>0; i--){ $( selector ).tabs("enable", i ); }
  };
  var makeMenu = function(activeitem) {
      var activeheader = $(activeitem).parent().parent().prev().index()/2;
      if (activeheader < 0) activeheader = 0;
      $( "#accordion" ).accordion({
          active: activeheader,
          heightStyle: "content"
      });
      $(activeitem).parent().addClass("menu-item-active");
      $(activeitem).click(function(e){e.preventDefault()});
  };
  $(function() {
      $( "#rpost-data" ).appendTo( $("#" + pagename + "-seq-rpost") ).show();
      makeMenu( "#accordion [href='page/"+channelname+"/"+pagename+"']");
      makeTabs( "#" + pagename + "-seqtabs" );
   $('#region_2 video, #region_2 audio').bind('contextmenu',function() { return false; });
  });
  </script>
</head>
<body>
	<header><?php if(x($page,'header')) echo $page['header']; ?></header>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation"><?php if(x($page,'nav')) echo $page['nav']; ?></nav>
	<main>
		<aside id="region_1"><?php if(x($page,'aside')) echo $page['aside']; ?></aside>
		<section id="region_2">

			<?php if(x($page,'content')) echo $page['content']; ?>

			<div id="page-footer"></div>
			<div id="pause"></div>
		</section>
		<aside id="region_3"><?php if(x($page,'right_aside')) echo $page['right_aside']; ?></aside>
	</main>
	<footer><?php if(x($page,'footer')) echo $page['footer']; ?></footer>
</body>
</html>

<!--
JSON.parse()
JSON.stringify()
mod/courses.php
pconfig(local_user(),'courses',description)
description = {'some_module':'some_tab','another_module':'another_tab'}
-->

