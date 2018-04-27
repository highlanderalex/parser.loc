$(document).ready(function(){
	$("#run").click(function(){
	$("#result").css("display", "none");
     $.ajax({
         url: '/main/run',
         type: 'GET',
		 beforeSend: function(){
			$("#spinner").fadeIn(200);
		 },
         success: function(res){
			$("#spinner").css('display', 'none');
			
			if(res.success){
				$(".alert-success").text(res.success);
			}
				
			if(res.error){
				var error = '<strong>Ошибки</strong><br>';
				for(var item in res.error){
					error += res.error[item] + '</br>';
				}
				$(".alert-danger").css('display', 'block');	
				$(".alert-danger").html(error);
			}
			//console.log(res);
            $("#result").fadeIn(200);
         },
         error: function(){
			$("#spinner").css('display', 'none');
            alert('Сервер временно недоступен!');
         }
     });
	});
});
