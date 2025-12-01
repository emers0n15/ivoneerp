$(function() {
	$("#btnDo").click(function() {
		ocultarMostrarMenu();
	});
	// $("ul li").bind('click', function() {
	// 	ocultarMostrarMenuLi();
	// });
	$(".cadEmpresa").bind('click', function() {
		$(".window").load('adminViews/cadastro/empresas.php')
	});
	$(".cadUtentes").bind('click', function() {
		$(".window").load('adminViews/cadastro/utentes.php')
	});
	$(".cadProdutos").bind('click', function() {
		$(".window").load('adminViews/cadastro/produtos.php')
	});
	$(".cadUsuarios").bind('click', function() {
		$(".window").load('adminViews/cadastro/usuarios.php')
	});
});