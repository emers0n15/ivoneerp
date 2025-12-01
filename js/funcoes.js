/*FUNCAO PARA MOSTRAR O TOTAL DE PRODUTOS NO CARINHO*/
function totalProduto() {
	$.ajax({
		url: '../dao/mostrarValorTotal/mostrarValorTotal.php',
		type: 'GET',
		success: function(data) {
			$(".total div").html(data);
		},
		error: function() {
			$(".total div").html("Ocorreu um erro na tentativa de mostrar a qtd dos produtos!");
		}
	});
}

/*FUNCAO PARA MOSTRAR OS PRODUTOS NA TABELA*/
function mostrarProdutos() {
	$.ajax({
		url: '../dao/mostrarProduto/mostrarProduto.php',
		type: 'GET',
		success: function(data) {
			$(".tbody").html(data);
		},
		error: function() {
			$(".tbody").html("Ocorreu um erro na tentativa de mostrar os produtos na tabela!");
		}
	});
}

/*FUNCAO PARA AUMENTAR A QTD DOS PRODUTOS*/
function aumentarQtdProdutos() {
	$.ajax({
		url: '../dao/aumentarQtdProduto/aumentarQtdProduto.php',
		type: 'POST',
		data: {
			produto: $("#prdto").val(),
		},
		success: function(data) {
			
		},
		error: function() {
			alert("Ocorreu um erro na tentativa de aumentar o produto!");
		}
	});
}

/*FUNCAO PARA DIMINUIR A QTD DOS PRODUTOS*/
function diminuirQtdProdutos() {
	$.ajax({
		url: '../dao/diminuirQtdProduto/diminuirQtdProduto.php',
		type: 'POST',
		data: {
			produto: $("#prdto").val(),
		},
		success: function(data) {
			
		},
		error: function() {
			alert("Ocorreu um erro na tentativa de diminuir o produto!");
		}
	});
}

/*FUNCAO PARA APAGAR OS PRODUTOS*/
function apagarProdutos() {
	$.ajax({
		url: '../dao/apagarProduto/apagarProduto.php',
		type: 'POST',
		data: {
			produto: $("#prdto").val(),
		},
		success: function(data) {
			
		},
		error: function() {
			alert("Ocorreu um erro na tentativa de diminuir o produto!");
		}
	});
}




/*FUNCAO PARA MOSTRAR O NUMERO DO PEDIDO*/
function mostrarNumeroPedido() {
	$.ajax({
		url: '../dao/mostrarNumeroFactura/mostrarNumeroFactura.php',
		type: 'GET',
		success: function(data) {
			$(".factura div").html(data);
		},
		error: function() {
			$(".factura div").html("Ocorreu um erro na tentativa de mostrar o numero da factura!");
		}
	});
}

/*FUNCAO PARA MOSTRAR O NUMERO DO PEDIDO*/
function fecharSistema() {
	$.ajax({
		url: '../dao/fecharSistema/index.php',
		type: 'GET',
		success: function(data) {
			
		},
		error: function() {
			alert("Ocorreu um erro ao sair da sessao! Contacte o seu administrador.");
		}
	});
}

/*FUNCAO PARA MOSTRAR O VALOR TOTAL A PAGAR*/
function mostrarValorTotal() {
	$.ajax({
		url: '../dao/mostrarTotalPagamento/mostrarTotalPagamento.php',
		type: 'GET',
		success: function(data) {
			$(".valorP").html(data);
		},
		error: function() {
			$(".valorP").html("Ocorreu um erro ao mostrar o valor total! Contacte o seu administrador.");
		}
	});
}

/*FUNCAO PARA MOSTRAR O ULTIMO NUMERO DE FACTURA DO USUARIO*/
function lastFactura() {
	$.ajax({
		url: '../dao/fact/fact.php',
		type: 'GET',
		success: function(data) {
			$(".pd").html(data);
		},
		error: function() {
			$(".pd").html("Ocorreu um erro ao mostrar o valor total! Contacte o seu administrador.");
		}
	});
}

/*FUNCAO PARA MOSTRAR O CLIENTE*/
function cliente() {
	$.ajax({
		url: '../dao/cliente/cliente.php',
		type: 'GET',
		data: {
			cliente: $("#search").val()
		},
		success: function(data) {
			$(".fatura .body").append(data);
		},
		error: function() {
			$(".fatura .body").html("Ocorreu um erro ao mostrar o cliente! Contacte o seu administrador.");
		}
	});
}

/*FUNCAO PARA MOSTRAR OS PRODUTOS NA FACTURA*/
function prodFactura() {
	$.ajax({
		url: '../dao/prodFactura/prodFactura.php',
		type: 'GET',
		data: {
			cliente: $("#search").val()
		},
		success: function(data) {
			$(".fatura .prodFactura").append(data);
		},
		error: function() {
			$(".fatura .prodFactura").html("Ocorreu um erro ao mostrar o cliente! Contacte o seu administrador.");
		}
	});
}

/*FUNCAO PARA MOSTRAR O VALOR TOTAL A PAGAR*/
function mostrarValorTotalFactura() {
	$.ajax({
		url: '../dao/mostrarValorTotalFactura/mostrarValorTotalFactura.php',
		type: 'GET',
		data: {
			cliente: $("#search").val()
		},
		success: function(data) {
			$(".totall").html(data);
		},
		error: function() {
			$(".totall").html("Ocorreu um erro ao mostrar o valor total! Contacte o seu administrador.");
		}
	});
}

/*FUNCAO PARA MOSTRAR O VALOR TOTAL A PAGAR*/
function mostrarValor() {
	$.ajax({
		url: '../dao/mostrarValor/mostrarValor.php',
		type: 'GET',
		success: function(data) {
			$("#totl").html(data);
		},
		error: function() {
			$("#totl").html("Ocorreu um erro ao mostrar o valor total! Contacte o seu administrador.");
		}
	});
}

/*FUNCAO PARA MOSTRAR E OCULTAR SOMENTE A FATURA */
function displayNone() {
	$(".principal").css({
		display: "none"
	});
}

function displayBlock() {
	$(".principal").css({
		display: "block"
	});
}

/*FUNCAO PARA MOSTRAR O VALOR EM CAIXA*/
function mostrarCaixa() {
	$.ajax({
		url: '../dao/mostrarCaixa/mostrarCaixa.php',
		type: 'GET',
		success: function(data) {
			$(".inf").append(data);
		},
		error: function() {
			$(".inf").append("Ocorreu um erro ao mostrar o valor em caixa!");
		}
	});
}
