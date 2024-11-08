<?php 
    session_start();
    if(!isset($_SESSION['level'])){
        header("Location: login.php");
        exit;
    }
    require 'functions/func_obat.php';
    $id = $_GET['id'];
    $obat = getData("SELECT * FROM obat WHERE id = $id")[0];
    $checkked = explode(', ', $obat['khasiat']);
    if (isset($_POST['update'])){
        if (update($_POST) > 0) {
            header("Location:obat.php");
        }else {
            echo "<script>alert('Ada yang salah!!!');</script>";
        }
    }
 ?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="assets/img/favicon.ico">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>Apotek Pratama Admin</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />


    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>


    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="assets/css/demo.css" rel="stylesheet" />


    <!--     Fonts and icons     -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
</head>
<body>

<div class="wrapper">
    <div class="sidebar" data-color="blue" data-image="assets/img/sidebar-5.jpg">

    <!--   you can change the color of the sidebar using: data-color="blue | azure | green | orange | red | purple" -->


            <div class="sidebar-wrapper">
                <div class="logo">
                    <a href="#" class="simple-text">
                        <?php
                        if ($_SESSION['level'] == 'admin') {
                            echo "admin inventory puskesmas";
                        } else if ($_SESSION['level'] == 'apoteker') {
                            echo "apoteker puskesmas";
                        } else if ($_SESSION['level'] == 'pegawai') {
                            echo "pegawai puskesmas";
                        }
                        ?>
                    </a>
                </div>

            <ul class="nav">
            <?php if($_SESSION['level'] == 'apoteker' || $_SESSION['level'] == 'admin' || $_SESSION['level'] == 'pegawai') { ?>
                <li>
                    <a href="dashboard.php">
                        <i class="pe-7s-graph"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($_SESSION['level'] == 'admin') { ?>
                <li>
                    <a href="users.php">
                        <i class="pe-7s-user"></i>
                        <p>Users</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($_SESSION['level'] == 'apoteker' || $_SESSION['level'] == 'admin' || $_SESSION['level'] == 'pegawai') { ?>
                <li class="active">
                    <a href="obat.php">
                        <i class="pe-7s-note2"></i>
                        <p>Data Obat</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($_SESSION['level'] == 'pegawai' || $_SESSION['level'] == 'admin') { ?>
                <li>
                    <a href="penjualan.php">
                        <i class="pe-7s-note2"></i>
                        <p>Data Penjualan</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($_SESSION['level'] == 'pegawai') { ?>
                <li>
                    <a href="laporan.php">
                        <i class="pe-7s-news-paper"></i>
                        <p>Laporan</p>
                    </a>
                </li>
            <?php } ?>
            <?php if($_SESSION['level'] == 'apoteker' || $_SESSION['level'] == 'admin') { ?>
                <li>
                    <a href="request_obat.php">
                        <i class="pe-7s-note2"></i>
                        <p>Request Obat</p> <?php /// buat button print ?>
                    </a>
                </li>
            <?php } ?>
            <?php //if($_SESSION['level'] == 'apoteker') { ?>
                <!-- <li>
                    <a href="penjualan_obat_narkotika.php">
                        <i class="pe-7s-note2"></i>
                        <p>Penjualan Obat Narkotika</p>
                    </a>
                </li> -->
            <?php //} ?>
            </ul>
    	</div>
    </div>

    <div class="main-panel">
		<nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Update Data Obat</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-left">   
                        <li>
                           <a href="">
                                <i class="fa fa-search"></i>
								<p class="hidden-lg hidden-md">Search</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">              
                        <li>
                            <a href="logout.php">
                                <p>Log out</p>
                            </a>
                        </li>
						<li class="separator hidden-lg hidden-md"></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="content">
                <div class="row">
                    <div class="col-md-4">
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?= $obat['id'] ?>">
                    <div class="form-group">
                        <label>Nama</label>
                        <input value="<?= $obat['nama'] ?>" name="nama" type="text" class="form-control" placeholder="Nama Obat .." autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input value="<?= $obat['harga'] ?>" name="harga" type="text" class="form-control" placeholder="Harga Obat .." autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input value="<?= $obat['stok'] ?>" name="stok" type="text" class="form-control" placeholder="Stok Obat .." autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Kategory</label>
                        <select name="kategory" class="form-control">
                            <option disabled>-- Pilih Kategory --</option>
                            <option>Obat Bebas</option>
                            <option>Obat Bebas Terbatas</option>
                            <option>Obat Keras</option>
                            <option>Jamu</option>
                            <option>Obat Herbal Terstandar</option>
                            <option>Fitofarmaka</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="khasiat[]" value="batuk" <?php in_array('batuk', $checkked) ? print "checked" : ""; ?> > Batuk <br>
                        <input type="checkbox" name="khasiat[]" value="flu" <?php in_array('flu', $checkked) ? print "checked" : ""; ?>> Flu <br>
                        <input type="checkbox" name="khasiat[]" value="pusing" <?php in_array('pusing', $checkked) ? print "checked" : ""; ?>> Pusing <br>
                        <input type="checkbox" name="khasiat[]" value="sakit kepala" <?php in_array('sakit kepala', $checkked) ? print "checked" : ""; ?>> Sakit Kepala <br>
                        <input type="checkbox" name="khasiat[]" value="mual" <?php in_array('mual', $checkked) ? print "checked" : ""; ?>> Mual <br>
                        <input type="checkbox" name="khasiat[]" value="diare" <?php in_array('diare', $checkked) ? print "checked" : ""; ?>> Diare <br>
                        <input type="checkbox" name="khasiat[]" value="masuk angin" <?php in_array('masuk angin', $checkked) ? print "checked" : ""; ?>> Masuk Angin <br>
                        <input type="checkbox" name="khasiat[]" value="maag" <?php in_array('maag', $checkked) ? print "checked" : ""; ?>> Maag <br>
                        <input type="checkbox" name="khasiat[]" value="sakit perut" <?php in_array('sakit perut', $checkked) ? print "checked" : ""; ?>> Sakit Perut <br>
                        <input type="checkbox" name="khasiat[]" value="sakit badan" <?php in_array('sakit badan', $checkked) ? print "checked" : ""; ?>> Sakit Badan <br>
                        <input type="checkbox" name="khasiat[]" value="penenang" <?php in_array('penenang', $checkked) ? print "checked" : ""; ?>> Penenang <br>
                        <input type="checkbox" name="khasiat[]" value="panas" <?php in_array('panas', $checkked) ? print "checked" : ""; ?>> Panas <br>
                        <input type="checkbox" name="khasiat[]" value="darah tinggi" <?php in_array('darah tinggi', $checkked) ? print "checked" : ""; ?>> Darah Tinggi <br>
                        <input type="checkbox" name="khasiat[]" value="kolesterol" <?php in_array('kolesterol', $checkked) ? print "checked" : ""; ?>> Kolesterol <br>
                        <input type="checkbox" name="khasiat[]" value="diabetes" <?php in_array('diabetes', $checkked) ? print "checked" : ""; ?>> Diabetes <br>
                        <input type="checkbox" name="khasiat[]" value="malaria" <?php in_array('malaria', $checkked) ? print "checked" : ""; ?>> Malaria
                    </div>
                    <div class="form-group">
                        <label>Expired</label>
                        <input value="<?= $obat['expired'] ?>" name="expired" type="date" class="form-control" placeholder="Tanggal Expired ..">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                    </div>                                                                   
                </div>
            </form>
                </div>
            </div>

        <footer class="footer">
            <div class="container-fluid">
                <nav class="pull-left">
                    <ul>
                        <li>
                            <a href="#">
                                Home
                            </a>
                        </li>
                    </ul>
                </nav>
                <p class="copyright pull-right">
                    &copy; <script>document.write(new Date().getFullYear())</script> <a href="#">Apotek Pratama</a>, Jl. Saptamarga, Kec. Botupingge, Desa Panggulo Barat.
                </p>
            </div>
        </footer>


    </div>
</div>

</body>

    <!--   Core JS Files   -->
    <script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

	<!--  Charts Plugin -->
	<script src="assets/js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="assets/js/bootstrap-notify.js"></script>

    <!--  Google Maps Plugin    -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>

    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

	<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
	<script src="assets/js/demo.js"></script>


</html>
