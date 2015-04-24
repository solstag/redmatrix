<!--
Building a page:
*Menu entries must have ID "accmenu-$pagename"
*Sequence tabs are blocks named "$pagename-seq-$tabname"
*Tabs are ordered according to php comparison on $tabname strings
*You must place a [widget=coursetabs][/widget] in your layout
-->

<!DOCTYPE html >
<html>
<head>
  <title><?php if(x($page,'title')) echo $page['title'] ?></title>
  <?php if(x($a->page,'htmlhead')) echo $a->page['htmlhead'] ?>
  <link rel="stylesheet" href="//atencao-plena.rhcloud.com/custom/jquery-ui-1.11.4/jquery-ui.css">
  <script src="//atencao-plena.rhcloud.com/custom/jquery-ui-1.11.4/jquery-ui.js"></script>
  <script>
  var baseurl="<?php echo $a->get_baseurl() ?>";
  var pagename="<?php echo argv(2) ?>";
  var flagname="<?php echo local_channel() ? get_pconfig(local_channel(),'system','startpage') : ''; ?>";
  var flagseq='';
  flagname = end(explode('/',flagname));
  list(flagname,flagseq) = explode('#',flagname);
  var makeTabs = function(selector) {
      var numtabs = $( selector ).find( " ul a" ).length;
      var seqlastenabled = $( selector ).find("ul a[href="+flagname+"]").parent().index();

      $( selector ) // change hrefs to full URL as "base href" is set
          .find( "ul a" ).each( function() {
              var href = $( this ).attr( "href" ),
                  newHref = window.location.protocol + '//' + window.location.hostname +
                      window.location.pathname + href;

              if ( href.indexOf( "#" ) == 0 ) {
                  $( this ).attr( "href", newHref );
              }
          })

      if (seqlastenabled < 0) seqlastenabled = 0;
      var seqdisabled = [];
      for(var i=seqlastenabled+1; i<numtabs; i++){ seqenabled.push(i); }

      $( selector ).tabs({show: true, disabled: seqdisabled});

      $( "#rpost-data" ).appendTo( $("#" + pagename + "-seq-rpost") ).show();

      var updateSeqButtons = function(index) {
		  if(index==0)
		      $(".sequence-button-previous").attr("disabled","disabled");
		  else
		      $(".sequence-button-previous").removeAttr("disabled");
		  if(index==numtabs)
		      $(".sequence-button-next").attr("disabled","disabled");
		  else
		      $(".sequence-button-next").removeAttr("disabled");
      }
      $( ".sequence-button-previous" ).click(function() {
          var newindex = $( selector ).tabs( "option", "active") - 1 ;
          $( selector ).tabs( "option", "active", newindex );
          updateSeqButtons(newindex);
      });
      $( ".sequence-button-next" ).click(function() {
          var newindex = $( selector ).tabs( "option", "active") + 1 ;
          $( selector ).tabs( "enable", newindex );
          $( selector ).tabs( "option", "active", newindex );
          updateSeqButtons(newindex);
      });
  };
  var makeMenu = function(activeitem, flagitem) {
      var activeheader = $(activeitem).parent().prev().index()/2;
      if (activeheader < 0) activeheader = 0;
      $( "#accordion" ).accordion({
          active: activeheader,
          heightStyle: "content"
      });
      $(activeitem).addClass("menu-item-active");
      $(flagitem).addClass("menu-item-flag");
      $(activeitem+" a").click(function(e){e.preventDefault()});
  };
  $(function() {
      makeMenu( "#accmenu-" + pagename, "#accmenu-" + flagname );
      makeTabs( "#" + pagename + "-seqtabs" );
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

