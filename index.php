<!doctype html>
<html lang="ru">
	<head>
		<meta charset="utf-8" />
		<title>Определение расстояния до ближайшего отделения выдачи посылок</title>
		<link rel="stylesheet" href="/public/css/style.css" />
		<link rel="stylesheet" href="/public/css/bootstrap.min.css" />
		<script src="/public/js/controlForm.js"></script>
	</head>
	<body style="background-color: #B0E0E6;">
		<div style="width: 400px; margin: 150px 700px;">
			<div id="textResponce" class="alert alert-light"  role="alert"></div>
			<form id="formWithData">
				<div class="form-group">
					<label for="inputFIO">ФИО:</label>
					<input type="text" class="form-control" id="inputFIO" placeholder="ФИО">
					<div id='messageInputFIO' class="alert alert-danger" role="alert"></div>
				</div>
				<div class="form-group">
					<label for="inputTel">Телефон:</label>
					<input type="tel" class="form-control" id="inputTel" placeholder="Телефон">
					<div id='messageInputTel' class="alert alert-danger" role="alert"></div>
				</div>
				<div class="form-group">
					<label for="inputAddress">Адрес:</label>
					<input type="text" class="form-control" id="inputAddress" placeholder="Адрес">
					<div id='messageInputAddress' class="alert alert-danger" role="alert"></div>
				</div>
				<button type="submit" class="btn btn-primary" id="sendAjax">Найти пункт выдачи</button>
			</form>
		</div>
	</body>
</html>