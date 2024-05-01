<?php 
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "leaflet-draw-native");

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

	// query insert data
	$query = "INSERT INTO maps 
				VALUES
				('', '$deskripsi', '$geojson')
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

	// query insert data
	$query = "UPDATE maps SET
				deskripsi = '$deskripsi',
				geojson = '$geojson'
				WHERE id = $id
			";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

?>