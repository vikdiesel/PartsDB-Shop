<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>The Result Page</title>
<script language="javascript" type="text/javascript">
	window.parent.window.uploadFinish({
		result: '<?php echo $result; ?>',
		resultCode: '<?php echo $resultcode; ?>'
	});
</script>
</head>

<body>

Result: <?php echo $result; ?>

</body>
</html>
