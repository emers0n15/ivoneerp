
			<div class="header-left">
				<a href="index.php" class="logo" title="Voltar ao Início">
					<img src="../../img/Logo - iVoneERP.jpg" width="140" alt="iVone ERP">
				</a>
			</div>
			<a id="toggle_btn" href="index.php" title="Voltar ao Início"><i class="fa fa-home"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown" aria-expanded="false">
                        <span class="user-img">
							<span class="status online"></span>
						</span>
						<span><?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?></span>
                        <i class="fa fa-chevron-down"></i>
                    </a>
					<div class="dropdown-menu dropdown-menu-right">
						<div class="user-header">
							<div class="avatar avatar-sm">
								<span class="status online"></span>
							</div>
							<div class="user-text">
								<h6><?php echo htmlspecialchars($_SESSION['nomeUsuario']); ?></h6>
								<p class="text-muted mb-0"><?php echo ucfirst($_SESSION['categoriaUsuario'] ?? 'Usuário'); ?></p>
							</div>
						</div>
						<a class="dropdown-item" href="index.php"><i class="fa fa-home"></i> Início</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="../../"><i class="fa fa-sign-out"></i> Sair</a>
					</div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="index.php"><i class="fa fa-home"></i> Início</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="../../"><i class="fa fa-sign-out"></i> Sair</a>
                </div>
            </div>