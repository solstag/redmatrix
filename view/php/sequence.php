<!DOCTYPE html >
<html>
<head>
  <title><?php if(x($page,'title')) echo $page['title'] ?></title>
  <?php if(x($a->page,'htmlhead')) echo $a->page['htmlhead'] ?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
  <!-- script src="//code.jquery.com/jquery-1.10.2.js"></script -->
  <script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
  <script>
  var baseurl="<?php echo $a->get_baseurl() ?>";
  var fullname="<?php echo argv(2) ?>";
  var makeTabs = function(selector) {
      $( selector )
          .find( "ul a" ).each( function() {
              var href = $( this ).attr( "href" ),
                  newHref = window.location.protocol + '//' + window.location.hostname +
                      window.location.pathname + href;

              if ( href.indexOf( "#" ) == 0 ) {
                  $( this ).attr( "href", newHref );
              }
          })
      $( selector ).tabs();
  };
  $(function() {
    $( "#accordion" ).accordion({
      active: $("#menu-header-"+fullname.split('-',1)[0]).index()/2,
      heightStyle: "content"
    });
    var activeid="#menu-header-"+fullname;
    $(activeid).addClass("menu-item-active");
    $(activeid+" a").click(function(e){e.preventDefault()});
    makeTabs( "#seqtabs" );
    $( "#rpost-data" ).appendTo( $("#seqtabs-rpost") ).show();
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
Building a page:
*Menu headers must have ID "menu-header-$headername"
*Menu entries must have ID "menu-header-$headername-$pagename"
*Page must be named "$headername-$pagename"
*Page has [content] and [sequence_$number] fields
-->

