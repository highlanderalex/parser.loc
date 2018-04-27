<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<?php if(!empty($canonical)): ?>
	<link rel="canonical" href="<?=$canonical;?>">
	<?php endif;?>
	<link rel="icon" type="image/png" href="images/star.png">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<?=$this->getMeta();?>
	<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div style="width: 800px; margin: 30px auto;">
	<button id="run" class="btn btn-primary" style="width: 100%;">Запустить парсер</button>
</div>
<?=$content;?>

<!--spinner-->
<div id="spinner">
	<div class="loader"></div>
</div>

<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/main.js"></script>

<?php if (DEBUG) :?>
		<div id="console">
			<header><strong>Console>></strong></header>
			<div id="console-content" style="display: none;">
			<?php $logs = \R::getDatabaseAdapter()
					->getDatabase()
					->getLogger();

			debug( $logs->grep( 'SELECT' ) );
			?>
			</div>
		</div>
		<script>
			$('#console').on('click', function(e) {
				$('#console-content').slideToggle(function() {
					$(e.target).text($(this).is(':visible') ? 'Console<<' : 'Console>>');
				});
			});
		</script>
	<?php endif;?>

</body>
</html>