<?php 
	require 'config.php';

	// get data obat
	function getData($sql){
		global $conn;
		$res = mysqli_query($conn, $sql);
		$rows = [];
		while ($row = mysqli_fetch_assoc($res)) {
			$rows[] = $row;
		}
		return $rows;
	}

	// insert data
	function insert($data) {
    global $conn;

    // Escape and sanitize input data
    $nama = htmlspecialchars($data['nama']);
    $harga = htmlspecialchars($data['harga']);
    $kategory = htmlspecialchars($data['kategory']);
    $stok = htmlspecialchars($data['stok']);
    $expired = htmlspecialchars($data['expired']);

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO barang (nama, harga, kategory, stok, expired) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters
    $stmt->bind_param("ssssss", $nama, $harga, $kategory, $stok, $expired);

    // Execute the statement
    if ($stmt->execute()) {
        $result = $stmt->affected_rows;
    } else {
        $result = 0; // Or handle the error as needed
        echo "Error executing query: " . htmlspecialchars($stmt->error);
    }

    // Close the statement
    $stmt->close();

    return $result;
}


	// delete data
	function delete($id){
		global $conn;
		mysqli_query($conn, "DELETE FROM obat WHERE id = $id");
		return mysqli_affected_rows($conn);
	}
  
	// update data
	function update($data){
    global $conn;
		$id = $data['id'];
		$Nama_Barang = htmlspecialchars($data['Nama_Barang']);
		$Jenis_Barang = htmlspecialchars($data['Jenis_Barang']);
		$Jumlah_Stock = htmlspecialchars($data['Jumlah_Stock']);
		$Tanggal_Kadaluarsa = htmlspecialchars($data['Tanggal_Kadaluarsa']);
    
		$sql = "UPDATE obat set
					Nama_Barang = '$Nama_Barang',
					Jenis_Barang = $Jenis_Barang,
					Jumlah_Stock = $Jumlah_Stock,
					Tanggal_Kadaluarsa = '$Tanggal_Kadaluarsa' WHERE id = $id
		";

		mysqli_query($conn, $sql);

		return mysqli_affected_rows($conn);
	}

	// search data
	function search($keyword){
		$sql = "SELECT * FROM obat WHERE
				Nama_Barang LIKE '%$keyword%' OR
				Jenis_Barang LIKE '%$keyword%' OR
				Jumlah_Stock LIKE '%$keyword%' OR
				Tanggal_Kadaluarsa LIKE '%$keyword%'";
		return getData($sql);
	}
  ?>