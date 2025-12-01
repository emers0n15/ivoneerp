<?php 
session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
}
error_reporting(E_ALL);
include '../../conexao/index.php';

$_SESSION['idUsuario'] = $_SESSION['idUsuario'];
$_SESSION['nomeUsuario'] = $_SESSION['nomeUsuario'];
$_SESSION['categoriaUsuario'] = $_SESSION['categoriaUsuario']; 
?>
<!DOCTYPE html>
<html lang="en">



<head>
    <?php include 'includes/head.php'; ?>
    <style>
        /* Ajusta a tabela para ocupar toda a largura disponível */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        table#example {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="header">
            <?php include 'includes/header.php' ?>
        </div>
        <div class="sidebar" id="sidebar">
            <?php include 'includes/side_bar.php'; ?>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="page-title">Periodicidade</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
						<div class="table-responsive">
                            <table id="example" class="display">
                                <thead>
                                    <tr>
                                        <th>Artigo</th>
                                        <th>1</th>
                                        <th>2</th>
                                        <th>3</th>
                                        <th>4</th>
                                        <th>5</th>
                                        <th>6</th>
                                        <th>7</th>
                                        <th>8</th>
                                        <th>9</th>
                                        <th>10</th>
                                        <th>11</th>
                                        <th>12</th>
                                        <th>13</th>
                                        <th>14</th>
                                        <th>15</th>
                                        <th>16</th>
                                        <th>17</th>
                                        <th>18</th>
                                        <th>19</th>
                                        <th>20</th>
                                        <th>21</th>
                                        <th>22</th>
                                        <th>23</th>
                                        <th>24</th>
                                        <th>25</th>
                                        <th>26</th>
                                        <th>27</th>
                                        <th>28</th>
                                        <th>29</th>
                                        <th>30</th>
                                        <th>31</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $sql = "SELECT *, (SELECT nomeproduto FROM produto as p WHERE p.idproduto = i.artigo) as artigos FROM inventario as i";
                                        $rs = mysqli_query($db, $sql) or die(mysqli_error($db));
                                        while ($dados = mysqli_fetch_array($rs)) {
                                    ?>
                                        <tr>
                                            <td><?php echo $dados['artigos']; ?></td>
                                            <td><?php echo $dados['01']; ?></td>
                                            <td><?php echo $dados['02']; ?></td>
                                            <td><?php echo $dados['03']; ?></td>
                                            <td><?php echo $dados['04']; ?></td>
                                            <td><?php echo $dados['05']; ?></td>
                                            <td><?php echo $dados['06']; ?></td>
                                            <td><?php echo $dados['07']; ?></td>
                                            <td><?php echo $dados['08']; ?></td>
                                            <td><?php echo $dados['09']; ?></td>
                                            <td><?php echo $dados['10']; ?></td>
                                            <td><?php echo $dados['11']; ?></td>
                                            <td><?php echo $dados['12']; ?></td>
                                            <td><?php echo $dados['13']; ?></td>
                                            <td><?php echo $dados['14']; ?></td>
                                            <td><?php echo $dados['15']; ?></td>
                                            <td><?php echo $dados['16']; ?></td>
                                            <td><?php echo $dados['17']; ?></td>
                                            <td><?php echo $dados['18']; ?></td>
                                            <td><?php echo $dados['19']; ?></td>
                                            <td><?php echo $dados['20']; ?></td>
                                            <td><?php echo $dados['21']; ?></td>
                                            <td><?php echo $dados['22']; ?></td>
                                            <td><?php echo $dados['23']; ?></td>
                                            <td><?php echo $dados['24']; ?></td>
                                            <td><?php echo $dados['25']; ?></td>
                                            <td><?php echo $dados['26']; ?></td>
                                            <td><?php echo $dados['27']; ?></td>
                                            <td><?php echo $dados['28']; ?></td>
                                            <td><?php echo $dados['29']; ?></td>
                                            <td><?php echo $dados['30']; ?></td>
                                            <td><?php echo $dados['31']; ?></td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <?php include 'includes/footer_plugins.php'; ?>
    <link href="../datatables.min.css" rel="stylesheet"/>
    <script src="../datatables.min.js"></script>
    <script type="text/javascript" src="../js/data-table-act.js"></script>
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            $('#example').DataTable();

        });
    </script>
</body>


<!-- attendance23:24-->
</html>