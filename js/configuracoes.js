$(function() {
		prodFactura();
		cliente();
		lastFactura();
		totalProduto();
		mostrarValor();
		mostrarCaixa();
		$("#fecharCaixa").on("click", function() {
			$(".caixa").css({
				display: "none"
			});
		});
		$(".fatura").on('click', function() {
			window.print();
			location.reload();
		});
		$("#fecharOCaixa").on("click", function() {
			$(".fechoDeCaixa").css({
				display: "none"
			});
		});
		$("#relatorio").on("click", function() {
			$(".rel").css({
				display: "block"
			});
		});
		$(".fecharRel").on("click", function() {
			$(".rel").css({
				display: "none"
			});
		});
		
		$("#fecharAberturaCaixa").on("click", function() {
			$(".aberturaCaixa").css({
				display: "none"
			});
		});
		$("#fecharFechoCaixa").on("click", function() {
			$(".fechoCaixa").css({
				display: "none"
			});
		});
		$("#caixa").on("click", function() {
			$(".caixa").css({
				display: "block"
			});
		});
		$("#fecharPagamento").on("click", function() {
			$("#trocoPrint").css({
				display: "none"
			});
		});
		
		$("#openCaixa").on("click", function() {
			$(".aberturaCaixa").css({
				display: "block"
			});

			$(".flts").css({
				display: "flex"
			});

			$(".aberturaCaixa #semana").focus();
		});
		$("#devolucao").on("click", function() {
			$(".devolver").css({
				display: "block"
			});
		});
		$("#ferra").on("click", function() {
			$(".ferramentas").css({
				display: "block"
			});

			$(".flun").css({
				display: "flex"
			});
		});
		$("#closeCaixa").on("click", function() {
			$(".fechoCaixa").css({
				display: "block"
			});
			$(".fltss").css({
				display: "flex"
			});
		});

		$("#salvarFecho").on("click", function() {
			$.ajax({
				url: '../dao/fechoCaixa/fechar.php',
				type: 'POST',
				data: {
					semana: $("#semanaF").val(),
					status: $("#statusPeriodoF").val()
				},
				success: function(data) {
					$("#messageFecho").html(data);
				},
				error: function() {
					$("#messageFecho").html("Ocorreu um erro na tentativa de fechar o caixa!");
				}
			});
		});
		$("#salvarAbertura").on("click", function() {
			$.ajax({
				url: '../dao/aberturaCaixa/abrir.php',
				type: 'POST',
				data: {
					semana: $("#semana").val(),
					status: $("#statusPeriodo").val()
				},
				beforeSend: function() {
					$("#message").html("Abrindo o Periodo...");
				},
				success: function(data) {
					$("#message").html(data);
				},
				error: function() {
					$("#message").html("Ocorreu um erro na tentativa de abrir o periodo!");
				}
			});
		});
		$(".produtoAdd").on('click', function() {
			var prdt = $(this).children("p").text();
			$("#prod").text(prdt);
			$("#produto").val(prdt);
			$(".qtdProduto").css({
				display: "block"
			});
			
			$(".floten").css({
				display: "flex"
			});
			$("#qtd").focus();
            		$("#qtdd").focus();
		});
		$("#fecharProduto").on('click', function() {
			var prdt = $(this).children("p").text("");
			$("#prod").text("");
			$("#produto").val("");
			$(".qtdProduto").css({
				display: "none"
			});
		});
		$(".aumentarQtdProduto").on('click', function() {
			aumentarQtdProdutos();
			mostrarNumeroPedido();
		});
		$(".diminuirQtdProduto").on('click', function() {
			diminuirQtdProdutos();
			mostrarNumeroPedido();
		});
		$(".apagarProduto").on('click', function() {
			apagarProdutos();
			totalProduto();
			mostrarNumeroPedido();
		});
		$("#qtd").on("change", function() {
			$.ajax({
				url: '../dao/produtoAdd/produtoAdd.php',
				type: 'POST',
				data: {
					produto: $("#produto").val(),
					qtd: $("#qtd").val()
				},
				success: function(data) {
					if (data == 1) {
						alert('Quantidade invalida!');
					}else if (data == 2) {
						alert('A quantidade informada é superior ao stock do produto.');
					}
					$("#tot").html(data);
					$("#qtd").val("");
					$(".qtdProduto").css({
						display: "none"
					});
					totalProduto();
					mostrarValor();
					
				},
				error: function() {
					$("#messageQtd").html("Ocorreu um erro ao adicionar o epi!");
				}
			});
			mostrarNumeroPedido();
			location.reload();
		});
		mostrarNumeroPedido();

		$(".efetuarPedido").on("click", function() {
			$.ajax({
			url: '../dao/pedido/pedido.php',
			type: 'GET',
			success: function(data) {
				$(".pedido").css({
					display: "block"
				});
				$(".pedido").append(data);
			},
			error: function() {
				$(".pedido").append("Ocorreu um erro na tentativa de mostrar o numero da factura!");
			}
			});
		});
		$(".fecharPedido").on("click", function() {
			$(this).parents(".todos").hide();
			mostrarNumeroPedido();
			location.reload();
		});
		$("#pedido").on('change', function() {
			$.ajax({
				url: '../dao/mostrarTotalPagamento/mostrarTotalPagamento.php',
				type: 'GET',
				data: {
					factura: $("#pedido").val()
				},
				success: function(data) {
					$("#val").html(data);
					var getVal = $("#val").text();
					$("#valorPay").val(getVal);
				},
				error: function() {
					$("#val").html("Ocorreu um erro na tentativa de mostrar os produtos na tabela!");
				}
			});
		});
		$(".pagamentoFactura").on("click", function() {
			$("#pagamentoFactura").css({
				display: "block"
			});
			$("#search").focus().on('change', function() {
				$("#valorPago").focus();
				$("#pagamentoFactura .info").html("");
			});
			mostrarNumeroPedido();
			mostrarValorTotal();
			$(".flot").css({
				display: "flex"
			});
		});
		$("#fecharFatura").on("click", function() {
			window.refresh();
			$(".fatura").css({
				display: "none"
			});
			mostrarNumeroPedido();
			location.reload();
		});
		$(".bt").on("click", function() {
			$("#pagamentoFactura .auto").css({
				display: "block"
			});
		});
		$(".fecharSistema").on("click", function() {
			fecharSistema();
			location.reload();
		});
		$("#list").on("click", function() {
			var novoElemento = $("<div class='tituloProd'><span>IMG</span><span>STOCK</span><span>PRECO</span><span>NOME</span><span>COD</span></div>");
			$(".produto").removeClass().addClass("lista").prepend(novoElemento);
		});
		$("#grid").on("click", function() {
			$(".tituloProd").css({
				display: "none"
			});
			$(".lista").removeClass().addClass("produto");
		});
	});
