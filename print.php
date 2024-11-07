<?php 
    session_start();
    // Cek apakah session level sudah ada
    if (!isset($_SESSION['level'])) {
        header("Location: login.php");
        exit;
    }

    require 'functions/config.php';
    require_once __DIR__ . '/vendor/autoload.php';
    require 'functions/func_penjualan.php';

    $mpdf = new \Mpdf\Mpdf();

    // Buat HTML Template untuk Laporan
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Laporan Penjualan</title>
        <style>
            body {
                font-family: arial;
            }
            tr:nth-child(even) {
                background-color: #ddd;
            }
        </style>
    </head>
    <body>
    <h2>Laporan Penjualan</h2>
        <table border="1" cellspacing="0" cellpadding="8">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Obat</th>
                <th>Jumlah</th>
            </tr>';

    $i = 1;

    // Gunakan Prepared Statements untuk keamanan
    $stmt = $conn->prepare("SELECT * FROM penjualan INNER JOIN obat ON penjualan.id_obat = obat.id ORDER BY penjualan.id_penjualan DESC");
    
    // Eksekusi query
    $stmt->execute();
    $res = $stmt->get_result();

    // Looping data dan masukkan ke HTML
    while ($data = $res->fetch_assoc()) {
        $html .= '<tr>
                <td>' . $i . '</td>
                <td>' . $data['tanggal'] . '</td>
                <td>' . $data['nama'] . '</td>
                <td>' . $data['jumlah_obat'] . '</td>
            </tr>';
        $i++;
    }

    $html .= '</table>
    </body>
    </html>';

    // Tulis HTML ke PDF
    $mpdf->WriteHTML($html);

    // Output file PDF langsung ke browser
    $mpdf->Output('laporan-penjualan.pdf', 'I');
?>
