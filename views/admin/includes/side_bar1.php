
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li class="active">
                            <a href="index.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                        </li>
                        <?php 
                            if ($_SESSION['categoriaUsuario'] == "recepcao") {
                        ?>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-coffee"></i> <span> Artigos </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="produtos.php">Artigos</a></li>
                                    <li><a href="grupo_artigos.php">Grupo de Artigos</a></li>
                                    <li><a href="familia_artigos.php">Família de Artigos</a></li>
                                    <!-- <li><a href="balanco.php">Balanço do Stock</a></li>
                                    <li><a href="balanco_vendas.php">Balanço de Vendas</a></li> -->
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-user"></i> <span> Entidades </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="clientes.php">Clientes</a></li>
                                    <li><a href="fornecedores.php">Fornecedores</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-file"></i> <span> Documentos </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="../vd.php" target="_blank">VD - Venda a Dinheiro</a></li>
                                    <li><a href="../facturas.php" target="_blank">FA - Factura</a></li>
                                    <li><a href="../cotacoes.php" target="_blank">CT - Cotação</a></li>
                                    <li><a href="../entrada_stocks.php" target="_blank">ES - Entrada de Stock</a></li>
                                    <li><a href="../saida_stocks.php" target="_blank">SS - Saída de Stock</a></li>
                                    
                                    <li><a href="../requisicao_internas.php" target="_blank">RI - Requisição Interna</a></li>
                                    <li><a href="../requisicao_externas.php" target="_blank">RE - Requisição Externa</a></li>
                                    <!-- <li><a href="../nota_entrega.php" target="_blank">NE - Nota de Entrega</a></li> -->
                                    
                                </ul>
                            </li>
                            
                            <li class="submenu">
                                <a href="#"><i class="fa fa-money"></i> <span> Fluxo de Caixa </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="rel_caixa.php">Caixa</a></li>
                                    
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-file"></i> <span> Explorador de Docs </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="rel_pedidos.php">Vendas a Dinheiro</a></li>
                                    <li><a href="factura.php">Facturas</a></li>
                                    <li><a href="cotacoes.php">Cotações</a></li>
                                    <li><a href="requisicao_interna.php">Requisição Interna</a></li>
                                    <li><a href="requisicao_externa.php">Requisição Externa</a></li>
                                </ul>
                            </li>
                        <?php
                            }else if ($_SESSION['categoriaUsuario'] == "armazem") {
                        ?>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-coffee"></i> <span> Artigos </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="produtos.php">Artigos</a></li>
                                    <li><a href="grupo_artigos.php">Grupo de Artigos</a></li>
                                    <li><a href="familia_artigos.php">Família de Artigos</a></li>
                                    <!-- <li><a href="balanco.php">Balanço do Stock</a></li>
                                    <li><a href="balanco_vendas.php">Balanço de Vendas</a></li> -->
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-user"></i> <span> Entidades </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="fornecedores.php">Fornecedores</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-file"></i> <span> Documentos </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="../requisicao_internas.php" target="_blank">RI - Requisição Interna</a></li>
                                    <li><a href="../requisicao_externas.php" target="_blank">RE - Requisição Externa</a></li>
                                    <li><a href="../entrada_stocks.php" target="_blank">ES - Entrada de Stock</a></li>
                                    <li><a href="../saida_stocks.php" target="_blank">SS - Saída de Stock</a></li>
                                    <!-- <li><a href="../nota_entrega.php" target="_blank">NE - Nota de Entrega</a></li> -->
                                    
                                </ul>
                            </li>
                            
                            <li class="submenu">
                                <a href="#"><i class="fa fa-database"></i> <span> Gestão de Stock </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">

                                        <li><a href="entrada_stock.php">Entrada de Stock</a></li>
                                        <li><a href="saida_stock.php">Saída de Stock</a></li>

                                    <li><a href="inventario.php">Inventário</a></li>
                                    <li><a href="periodicidade.php">Periodicidade</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-file"></i> <span> Explorador de Docs </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="requisicao_interna.php">Requisição Interna</a></li>
                                    <li><a href="requisicao_externa.php">Requisição Externa</a></li>
                                </ul>
                            </li>
                        <?php
                            }else if ($_SESSION['categoriaUsuario'] == "contabilidade") {
                        ?>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-coffee"></i> <span> Artigos </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="produtos.php">Artigos</a></li>
                                    <li><a href="grupo_artigos.php">Grupo de Artigos</a></li>
                                    <li><a href="familia_artigos.php">Família de Artigos</a></li>
                                    <!-- <li><a href="balanco.php">Balanço do Stock</a></li>
                                    <li><a href="balanco_vendas.php">Balanço de Vendas</a></li> -->
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-user"></i> <span> Entidades </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="clientes.php">Clientes</a></li>
                                    <li><a href="fornecedores.php">Fornecedores</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-file"></i> <span> Documentos </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="../vd.php" target="_blank">VD - Venda a Dinheiro</a></li>
                                    <li><a href="../devolucoes.php" target="_blank">DV - Devolução</a></li>
                                    <li><a href="../facturas.php" target="_blank">FA - Factura</a></li>
                                    <li><a href="../nota_creditos.php" target="_blank">NC - Nota de Crédito</a></li>
                                    <li><a href="../nota_debitos.php" target="_blank">ND - Nota de Débito</a></li>
                                    <li><a href="../recibos.php" target="_blank">RC - Recibo</a></li>
                                    <li><a href="../cotacoes.php" target="_blank">CT - Cotação</a></li>
                                    <li><a href="../ordem_compras.php" target="_blank">OC - Ordem de Compra</a></li>
                                    <li><a href="../requisicao_internas.php" target="_blank">RI - Requisição Interna</a></li>
                                    <li><a href="../requisicao_externas.php" target="_blank">RE - Requisição Externa</a></li>
                                    <!-- <li><a href="../nota_entrega.php" target="_blank">NE - Nota de Entrega</a></li> -->
                                    
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-money"></i> <span> Fluxo de Caixa </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="rel_caixa.php">Caixa</a></li>
                                    
                                    <li><a href="balanco_diario.php">Balanço Diário</a></li>
                                    <li><a href="entrada_caixa.php">Entrada de Caixa</a></li>
                                    <li><a href="saida_caixa.php">Retirada de Caixa</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-file"></i> <span> Explorador de Docs </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="rel_pedidos.php">Vendas a Dinheiro</a></li>
                                    <li><a href="factura.php">Facturas</a></li>
                                    <li><a href="nota_de_credito.php">Notas de Crédito</a></li>
                                    <li><a href="nota_de_debito.php">Notas de Débito</a></li>
                                    <li><a href="recibos.php">Recibos</a></li>
                                    <li><a href="cotacoes.php">Cotações</a></li>
                                    <li><a href="devolucao.php">Devoluções</a></li>
                                    <li><a href="ordens_compra.php">Ordem de Compras</a></li>
                                    <li><a href="requisicao_interna.php">Requisição Interna</a></li>
                                    <li><a href="requisicao_externa.php">Requisição Externa</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-newspaper-o"></i> <span> Contas Correntes </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="contas_pagar.php">Contas a Pagar</a></li>
                                    <li><a href="contas_receber.php">Contas a Receber</a></li>
                                    <li><a href="extrato_clientes.php">Extrato de Clientes</a></li>
                                </ul>
                            </li>
                        <?php
                            }else if ($_SESSION['categoriaUsuario'] == "admin") {
                        ?>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-coffee"></i> <span> Artigos </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="produtos.php">Artigos</a></li>
                                    <li><a href="grupo_artigos.php">Grupo de Artigos</a></li>
                                    <li><a href="familia_artigos.php">Família de Artigos</a></li>
                                    <!-- <li><a href="balanco.php">Balanço do Stock</a></li>
                                    <li><a href="balanco_vendas.php">Balanço de Vendas</a></li> -->
                                </ul>
                            </li>
                             <li class="submenu">
                                <a href="#"><i class="fa fa-user"></i> <span> Entidades </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="clientes.php">Clientes</a></li>
                                    <li><a href="fornecedores.php">Fornecedores</a></li>
                                </ul>
                            </li>
                             <li class="submenu">
                                <a href="#"><i class="fa fa-file"></i> <span> Documentos </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="../vd.php" target="_blank">VD - Venda a Dinheiro</a></li>
                                    <li><a href="../devolucoes.php" target="_blank">DV - Devolução</a></li>
                                    <li><a href="../facturas.php" target="_blank">FA - Factura</a></li>
                                    <li><a href="../nota_creditos.php" target="_blank">NC - Nota de Crédito</a></li>
                                    <li><a href="../nota_debitos.php" target="_blank">ND - Nota de Débito</a></li>
                                    <li><a href="../recibos.php" target="_blank">RC - Recibo</a></li>
                                    <li><a href="../cotacoes.php" target="_blank">CT - Cotação</a></li>
                                    <li><a href="../ordem_compras.php" target="_blank">OC - Ordem de Compra</a></li>
                                    <li><a href="../requisicao_internas.php" target="_blank">RI - Requisição Interna</a></li>
                                    <li><a href="../requisicao_externas.php" target="_blank">RE - Requisição Externa</a></li>
                                    <li><a href="../entrada_stocks.php" target="_blank">ES - Entrada de Stock</a></li>
                                    <li><a href="../saida_stocks.php" target="_blank">SS - Saída de Stock</a></li>
                                    <!-- <li><a href="../nota_entrega.php" target="_blank">NE - Nota de Entrega</a></li> -->
                                    
                                </ul>
                            </li>
                        
						    <li class="submenu">
    							<a href="#"><i class="fa fa-database"></i> <span> Gestão de Stock </span> <span class="menu-arrow"></span></a>
    							<ul style="display: none;">

                                        <li><a href="entrada_stock.php">Entrada de Stock</a></li>
                                        <li><a href="saida_stock.php">Saída de Stock</a></li>

    								<li><a href="inventario.php">Inventário</a></li>
    								<li><a href="periodicidade.php">Periodicidade</a></li>
    							</ul>
    						</li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-money"></i> <span> Fluxo de Caixa </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="rel_caixa.php">Caixa</a></li>
                                    
                                    <li><a href="balanco_diario.php">Balanço Diário</a></li>
                                    <li><a href="entrada_caixa.php">Entrada de Caixa</a></li>
                                    <li><a href="saida_caixa.php">Retirada de Caixa</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-file"></i> <span> Explorador de Docs </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="rel_pedidos.php">Vendas a Dinheiro</a></li>
                                    <li><a href="factura.php">Facturas</a></li>
                                    <li><a href="nota_de_credito.php">Notas de Crédito</a></li>
                                    <li><a href="nota_de_debito.php">Notas de Débito</a></li>
                                    <li><a href="recibos.php">Recibos</a></li>
                                    <li><a href="cotacoes.php">Cotações</a></li>
                                    <li><a href="devolucao.php">Devoluções</a></li>
                                    <li><a href="ordens_compra.php">Ordem de Compras</a></li>
                                    <li><a href="requisicao_interna.php">Requisição Interna</a></li>
                                    <li><a href="requisicao_externa.php">Requisição Externa</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-newspaper-o"></i> <span> Contas Correntes </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <li><a href="contas_pagar.php">Contas a Pagar</a></li>
                                    <li><a href="contas_receber.php">Contas a Receber</a></li>
                                    <li><a href="extrato_clientes.php">Extrato de Clientes</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#"><i class="fa fa-key"></i> <span> Configurações </span> <span class="menu-arrow"></span></a>
                                <ul style="display: none;">
                                    <!--<li><a href="ano_fiscal.php">Ano Fiscal</a></li>-->
                                    <!--<li><a href="sectores.php">Sectores</a></li>-->
                                    <!--<li><a href="dados_empresa.php">Dados da Empresa</a></li>-->
                                    <li><a href="tipos_iva.php">IVA</a></li>
                                    <li><a href="metodo_pagamento.php">Método de Pagamento</a></li>
                                    <li><a href="condicoes_pagamento.php">Condições de Pagamento</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="utilizadores.php"><i class="fa fa-user"></i> <span>Utilizadores</span></a>
                            </li>
                            
                        <?php
                            }
                        ?>
                    </ul>
                </div>
            </div>