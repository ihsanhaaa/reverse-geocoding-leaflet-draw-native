<?php

$conn = mysqli_connect("localhost", "root", "", "reverse-geocoding-leaflet-draw-native");



function tambah($data) {
	global $conn;
	// ambil data dari tiap elemen
	$deskripsi = htmlspecialchars($data["deskripsi"]);
	$geojson = htmlspecialchars($data["geojson"]);
	$alamat = htmlspecialchars($data["alamat"]);
	$kode_pos = htmlspecialchars($data["kode_pos"]);

	// query insert data
	$query = "INSERT INTO maps 
				VALUES
				('', '$deskripsi', '$geojson', '$alamat', '$kode_pos')
			";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

?>

