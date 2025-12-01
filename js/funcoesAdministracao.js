/*variaveis globais*/



/*funcao para mostrar e ocultar a barra de menu - btnclose*/
function ocultarMostrarMenu() {
	$(".esquerda").toggle(800, function() {
		$(".direita").css({
			width: '100%'
		});
	});
}

/*funcao para mostrar e ocultar a barra de menu - lis*/
function ocultarMostrarMenuLi() {
	$(".esquerda").slideUp(800, function() {
		$(".direita").css({
			width: '100%'
		});
	});
}