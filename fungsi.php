<?php 
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "reverse-geocoding-leaflet-draw-native");

function query($query) {
	global $conn;
	$result = mysqli_query($conn, $query);
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

function tambah($data) {
	global $conn;
	// ambil data dari tiap elemen
	$deskripsi = htmlspecialchars($data["deskripsi"]);
	$geojson = $data["geojson"];
	$alamat = $data["alamat"];
	$kode_pos = $data["kode_pos"];

	// query insert data
	$query = "INSERT INTO maps 
				VALUES
				('', '$deskripsi', '$geojson', '$alamat', '$kode_pos')
			";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}


function hapus($id) {
	global $conn;
	mysqli_query($conn, "DELETE FROM maps WHERE id = $id");

	return mysqli_affected_rows($conn);
}


function ubah($data) {
	global $conn;

	// ambil data dari tiap elemen
	$id = $data["id"];
	$deskripsi = htmlspecialchars($data["deskripsi"]);
	$geojson = $data["geojson"];
	$alamat = $data["alamat"];
	$kode_pos = $data["kode_pos"];

	// query insert data
	$query = "UPDATE maps SET
				deskripsi = '$deskripsi',
				geojson = '$geojson',
				alamat = '$alamat',
				kode_pos = '$kode_pos'
				WHERE id = $id
			";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

?>