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
    $stok = htmlspecialchars($data['stok']);
    $kategory = htmlspecialchars($data['kategory']);
		$khasiat = isset($data['khasiat']) && is_array($data['khasiat']) ? implode(', ', $data['khasiat']) : '';

    $expired = htmlspecialchars($data['expired']);

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO obat (nama, harga, stok, kategory, khasiat, expired) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters
    $stmt->bind_param("ssssss", $nama, $harga, $stok, $kategory, $khasiat, $expired);

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
		$nama = htmlspecialchars($data['nama']);
		$harga = htmlspecialchars($data['harga']);
		$stok = htmlspecialchars($data['stok']);
		$kategory = htmlspecialchars($data['kategory']);
		$khasiat = implode(', ', $data['khasiat']);
		$expired = htmlspecialchars($data['expired']);

		$sql = "UPDATE obat set
					nama = '$nama',
					harga = $harga,
					stok = $stok,
					kategory = '$kategory',
					khasiat = '$khasiat',
					expired = '$expired' WHERE id = $id
		";

		mysqli_query($conn, $sql);

		return mysqli_affected_rows($conn);
	}

	// search data
	function search($keyword){
		$sql = "SELECT * FROM obat WHERE
				nama LIKE '%$keyword%' OR
				harga LIKE '%$keyword%' OR
				stok LIKE '%$keyword%' OR
				kategory LIKE '%$keyword%' OR
				khasiat LIKE '%$keyword%' OR
				expired LIKE '%$keyword%'";
		return getData($sql);
	}
 ?>