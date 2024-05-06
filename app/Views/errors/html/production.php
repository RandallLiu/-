<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex">

	<title>出错啦!</title>

	<style type="text/css">
		<?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
	</style>
</head>
<body>

	<div class="container text-center">

		<h1 class="headline">出错啦!</h1>
		<p class="lead">程序出错,请联系管理员,稍后再试...</p>
	</div>

</body>

</html>
