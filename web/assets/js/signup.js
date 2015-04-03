$(document).ready(function() {
	$("#register").click(function() {
		var values = {
			"fname": $("[name=fname]").val(),
			"lname": $("[name=lname]").val(),
			"email": $("[name=email]").val(),
			"password": $("[name=password]").val(),
			"confirm": $("[name=confirm]").val()
		};
		console.log($("[name=fname]").val());
		$.ajax({
			url: "backend/signup.php",
			type: "POST",
			data: values,
			success: function(result) {
				if (result == 0) {
					$("#reg-status").text("please fill all information");
				}
				if (result == 1) {
					$("#reg-status").text("passwords don't match");
				} 
				if (result == 2) {
					window.location.href="index.php";
				}
				if (result == 3) {	
					$("#reg-status").text("Email is already registered");
				}
			}
		});
	});
	//using enter key to trigger click event
	$("[name=confirm]").keypress(function(e) {
		if (e.which === 13){
			$("#register").click();
		}
	});


} 	);