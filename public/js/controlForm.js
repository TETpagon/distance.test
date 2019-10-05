document.addEventListener('DOMContentLoaded', function () {
	const formWithData = document.getElementById('formWithData');
	const inputFIO = document.getElementById('inputFIO');
	const inputTel = document.getElementById('inputTel');
	const inputAddress = document.getElementById('inputAddress');
	const messageInputFIO = document.getElementById('messageInputFIO');
	const messageInputTel = document.getElementById('messageInputTel');
	const messageInputAddress = document.getElementById('messageInputAddress');
	const textResponce = document.getElementById('textResponce');
	const areaResponce = document.getElementById('areaResponce');
	const buttonBack = document.getElementById('buttonBack');
	
	refreshForm();
	
	formWithData.addEventListener('submit', sandAjax);
	buttonBack.addEventListener('click', refreshForm);

	function sandAjax(e){
		e.preventDefault();
		refreshForm();
		
		// Если форма проходит валидацию, то оправляем запрос, иначе показываем ошибки
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
						areaResponce.classList.remove("hidden");
						formWithData.classList.add("hidden");
					} else {
						responseData['data'].forEach(function(error, i, arr){
							if (error['type'] === "errorTel") {
								showError(messageInputTel, error['text']);
							}
							if (error['type'] === "errorAddress") {
								showError(messageInputAddress, error['text']);
							}
							if (error['type'] === "errorTimeout") {
								textResponce.innerHTML = error['text'];
								areaResponce.classList.remove("hidden");
								formWithData.classList.add("hidden");
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
		formWithData.classList.remove("hidden");
		
		areaResponce.classList.add("hidden");
		textResponce.innerHTML = "";
		
		messageInputFIO.classList.add("hidden");
		messageInputFIO.innerHTML = "";

		messageInputTel.classList.add("hidden");
		messageInputTel.innerHTML = "";

		messageInputAddress.classList.add("hidden");
		messageInputAddress.innerHTML = "";
	}
});	



