document.addEventListener('DOMContentLoaded', function () {
	let formWithData = document.getElementById('formWithData');
	let inputFIO = document.getElementById('inputFIO');
	let messageInputFIO = document.getElementById('messageInputFIO');
	let inputTel = document.getElementById('inputTel');
	let messageInputTel = document.getElementById('messageInputTel');
	let inputAddress = document.getElementById('inputAddress');
	let messageInputAddress = document.getElementById('messageInputAddress');
	let textResponce = document.getElementById('textResponce');

	inputFIO.value = "Иванов Иван Иванович";
	inputTel.value = "12345678910";
	inputAddress.value = "Россия, Санкт-Петербург, ул. Профессора Попова, дом 5";

	refreshForm();
	
	formWithData.addEventListener('submit', sandAjax);
	
	function sandAjax(e){
		e.preventDefault();
		refreshForm();

		if (validateForm()) {
			const request = new XMLHttpRequest();
			const url = "/project/ajax.php";
			const clientFio = inputFIO.value;
			const clientTel = inputTel.value;
			const clientAddress = inputAddress.value;
			const params = "clientFio=" + clientFio + "&clientTel=" + clientTel + "&clientAddress=" + clientAddress;
			request.open("POST", url);
			request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			request.addEventListener("readystatechange", () => {
				if(request.readyState === 4 && request.status === 200) {
					let responseData = JSON.parse(request.responseText);
					let message = "";
					
					// Если на сервере не выявлено ошибок и определено расстояние до пункта выдачи посылок,
					// то выводится название пункта выдачи и расстояние до него, иначе выводятся ошибки.
					if (responseData['status'] === 0) {
						message = responseData['data']['clientFio'] + " (" + responseData['data']['clientTel'] + "): ближайший пункт выдачи " 
						+ responseData['data']['nearestDistribution']['name'] + " находится на расстоянии " 
						+ responseData['data']['nearestDistribution']['distance'] + "км.";

						textResponce.innerHTML = message;
						textResponce.classList.remove("hidden");
					} else {
						responseData['data'].forEach(function(error, i, arr){
							if (error['type'] === "errorTel") {
								showError(messageInputTel, error['text']);
							}
							if (error['type'] === "errorAddress") {
								showError(messageInputAddress, error['text']);
							}
						});
					}
				}
			});
			request.send(params);
		}

	}

	/**
	* Проверяет введенные данные и сообщает об некоректностях.
	* Возращает признак о валидности данных.
	*
	* @return boolean 
	*/
	function validateForm() {
		let accessSend = true;

		if (inputFIO.value === "") {
			showError(messageInputFIO, "Поле не заполнено!");

			accessSend = false;
		};



		if (inputTel.value === "") {
			showError(messageInputTel,"Поле не заполнено!");

			accessSend = false;
		} else if (inputTel.value.match(/^\+?(\d+|\(|\)|\-)$/) === null) {
			showError(messageInputTel,"Номер телефона введен не корректно!");

			accessSend = false;
		};


		if (inputAddress.value === "") {
			showError(messageInputAddress,"Поле не заполнено!");

			accessSend = false;
		};

		return accessSend;
	}

	/**
	* Вывод ошибки.
	*/
	function showError(element, message) {
		element.classList.remove("hidden");
		element.innerHTML = message;
	}

	/**
	* Чистка формы.
	*/
	function refreshForm() {
		textResponce.classList.add("hidden");
		textResponce.innerHTML = "";
		
		messageInputFIO.classList.add("hidden");
		messageInputFIO.innerHTML = "";

		messageInputTel.classList.add("hidden");
		messageInputTel.innerHTML = "";

		messageInputAddress.classList.add("hidden");
		messageInputAddress.innerHTML = "";

		textResponce.classList.add("hidden");
	}
});	



