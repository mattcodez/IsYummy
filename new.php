<html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
	
	<script>
		function docReady(){
			$('#title').keyup(checkTitle);
		}
		
		function checkTitle(){
			$.getJSON(
				'helper.php',
				{
					action	:	'checkTitle',
					title	:	$(this).val()
				},
				setStatus
			);
		}
		
		function setStatus(data){
			var status = $('#status');
			if (data.titleExists){
				status.text('Already taken');
				status[0].style.color = '#FF0000';
			}
			else {
				status.text('Available');
				status[0].style.color = '#00FF00';
			}
		}
	</script>
</head>
<body>
<script>$(document).ready(docReady);</script>

<form name="upload" method="POST" action="addNew2.php" enctype="multipart/form-data">
	<fieldset>
		<label>Title: </label><input type="text" name="title" id="title"><span id="status"></span><br>
		<label>Text: </label><textarea name="text"></textarea><br>
		<label>Picture: </label><input type="file" name="file"><br>
		<input type="submit" value="Save"><br>
	</fieldset>
</form>
</body>
</html>