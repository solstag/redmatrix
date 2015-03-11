<!DOCTYPE html >
<html>
<head>
  <title><?php if(x($page,'title')) echo $page['title'] ?></title>
  <?php if(x($page,'htmlhead')) echo $page['htmlhead'] ?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
  <!-- script src="//code.jquery.com/jquery-1.10.2.js"></script -->
  <script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
  <script>
  var baseurl="<?php echo $a->get_baseurl() ?>";
  var fullname="<?php echo argv(2) ?>";
  $(function() {
    $( "#accordion" ).accordion({
      active: $("#menu-header-"+fullname.split('-',1)[0]).index()/2,
      heightStyle: "content"
    });
    var activeid="#menu-header-"+fullname;
    $(activeid).addClass("menu-item-active");
    $(activeid+" a").click(function(e){e.preventDefault()});
    $("#tabs").tabs();
  });
  </script>
</head>
<body>
	<header><?php if(x($page,'header')) echo $page['header']; ?></header>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation"><?php if(x($page,'nav')) echo $page['nav']; ?></nav>
	<main>
		<aside id="region_1"><?php if(x($page,'aside')) echo $page['aside']; ?></aside>
		<section id="region_2">

			<div id="tabs">
				<ul>
					<?php
					for($i=1;;$i++){
						if(x($page,'sequence_'+$i)){
							echo '<li><a href="#sequence-' . $i . '">$i</a></li>';
						}
						else break;
					}
					?>
				</ul>
				<?php
				if(x($page,'content')) echo $page['content'];
				for($i=1;;$i++){
					if(x($page,'sequence_'+$i)){
						echo '<div id="sequence-' . $i . '">';
						echo $page['sequence_' . $i];
						echo '</div>';
					}
					else break;
				}
				?>
			</div>
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
*Page must be named "$headername-$pagename"
*Page has [content] and [sequence_$number] fields
-->
