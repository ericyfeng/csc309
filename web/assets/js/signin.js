$(document).ready(function() {
	$("#signin").click(function() {
		var values = {
			"email": $("[name=logemail]").val(),
			"password": $("#password").val()
		};
		console.log($("[name=logemail]").val());
		console.log($("#password").val());
		$.ajax({
			url: "backend/login.php",
			type: "POST",
			data: values,
			success: function(result) {
				if (result == 0) {
					$("#login-status").text("please fill all information");
				}
				if (result == 1) {
					$("#login-status").text("login incorrect");
				} 
			}
		});
	});
	//using enter key to trigger click event
	$("#password").keypress(function(e) {
		if (e.which === 13){
			$("#signin").click();
		}
	});


} 	);