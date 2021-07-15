  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
 <script language="javascript" type="text/javascript">
		function windowClose() {
		window.open('','_parent','');
		window.close();
	}
</script>
<style>
#fix {
	 table-layout: fixed;
}
table {
  border: 1px solid black;

  width: 100%;
}
td , th {
	border: 1px solid black;
	text-align: center;
	width: 100%;
	layout: fixed;
}
#box {
	display:none;
}
.a {
	display:none;
}
.show {
	display:block;
}
body {
	background-color:#f2f2f2;
}
</style>
 </head>
 <?php $con = new PDO("mysql:host=localhost;dbname=project;charset=utf8","root","");?>
 