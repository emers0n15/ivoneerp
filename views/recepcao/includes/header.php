
			<div class="header-left">
				<a href="dashboard.php" class="logo" title="Voltar ao Início">
					<img src="../../img/Logo - iVoneERP.jpg" width="140" alt="iVone ERP">
				</a>
			</div>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                <li class="nav-item">
                    <span class="nav-link user-name-simple">
                        <i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?>
                    </span>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="dashboard.php"><i class="fa fa-home"></i> Início</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="../../"><i class="fa fa-sign-out"></i> Sair</a>
                </div>
            </div>

