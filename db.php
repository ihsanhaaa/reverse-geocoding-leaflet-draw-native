<?php

$conn = mysqli_connect("localhost", "root", "", "leaflet-draw-native");



function tambah($data) {
	global $conn;
	// ambil data dari tiap elemen
	$deskripsi = htmlspecialchars($data["deskripsi"]);
	$geojson = htmlspecialchars($data["geojson"]);

	// query insert data
	$query = "INSERT INTO maps 
				VALUES
				('', '$deskripsi', '$geojson')
			";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

?>

