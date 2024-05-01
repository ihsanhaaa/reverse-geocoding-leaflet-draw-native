<?php
require 'fungsi.php';

$dataMaps = query("SELECT * FROM maps");

// cek apakah tombol submit sudah ditekan atau belum
if (isset(($_POST["submit"]))) {

  // cek apakah data berhasil ditambahkan
  if (tambah($_POST) > 0) {
    echo "
			<script>
				alert('Data berhasil ditambahkan');
				document.location.href = 'create.php';
			</script>
		";
  } else {
    echo "
			<script>
				alert('Data gagal ditambahkan');
				document.location.href = 'create.php';
			</script>
		";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reverse Geocoding Leaflet Draw with PHP and Form</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>

  <style>
    #dataForm {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      max-width: 800px;
      margin: 0 auto;
    }

    #dataForm div {
      flex: 1;
      margin-right: 10px;
    }

    #deskripsi,
    #alamat,
    #kode_pos,
    #geojson {
      width: 100%;
      box-sizing: border-box;
    }
  </style>

</head>

<body>
  <div id="map" style="height: 550px;"></div>

  <form id="dataForm" action="" method="post" enctype="multipart/form-data" style="margin-top: 25px;">
    <div>
      <label for="deskripsi">Deskripsi:</label><br>
      <textarea id="deskripsi" name="deskripsi" rows="4" cols="50"></textarea>
    </div>

    <div>
      <label for="geojson">Geojson:</label><br>
      <textarea id="geojson" name="geojson" rows="4" cols="50"></textarea>
    </div>

    <div>
      <label for="alamat">Alamat: (otomatis)</label><br>
      <textarea id="alamat" name="alamat" rows="4" cols="50"></textarea>
    </div>

    <div>
      <label for="kode_pos">Kode Pos: (otomatis)</label><br>
      <textarea id="kode_pos" name="kode_pos" rows="4" cols="50"></textarea>
    </div>

    <div>
      <button type="submit" name="submit" style="margin-top: 40px;">Simpan Data</button>
    </div>
  </form>

  <!-- tambahkan cdn turf.js untuk dapat menjalankan algoritma perhitungan titik tengah poligon -->
  <script src="https://cdn.jsdelivr.net/npm/@turf/turf@latest"></script>

  <!-- jquery -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
    var map = L.map("map").setView([-0.05509435153361005, 109.34942867782628], 15);

    // Basemaps
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'OpenStreetMap'
    });
    var openTopoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
      attribution: 'OpenTopoMap'
    });

    var Esri_WorldImagery = L.tileLayer(
      'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
      });

    var googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
      maxZoom: 20,
      subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });
    var googleTraffic = L.tileLayer('https://{s}.google.com/vt/lyrs=m@221097413,traffic&x={x}&y={y}&z={z}', {
      maxZoom: 20,
      minZoom: 2,
      subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
    });

    // Default basemap
    osm.addTo(map);

    var baseMaps = {
      "OSM": osm,
      "OpenTopoMap": openTopoMap,
      "Esri": Esri_WorldImagery,
      "Google Streets": googleStreets,
      "Google Traffic": googleTraffic,
    };

    L.control.layers(baseMaps).addTo(map);

    var mapsData = <?php echo json_encode($dataMaps); ?>;

    mapsData.forEach(function(mapData) {
      const geojson = JSON.parse(mapData.geojson);

      // Membuat layer GeoJSON dan menambahkannya ke peta
      L.geoJSON(geojson, {
        onEachFeature: function(feature, layer) {
          const popupContent = '<b>Popup Content</b><br>' +
            'Deskripsi: ' + mapData.deskripsi +
            '<br>Alamat: ' + mapData.alamat +
            '<br>Kode Pos: ' + mapData.kode_pos +
            '<br><a href="edit.php?id=' + mapData.id + '">Edit</a>' +
            '<br><a href="hapus.php?id=' + mapData.id + '">Hapus</a>';
          layer.bindPopup(popupContent);
          layer.addTo(map);
        }
      });
    });

    var drawControl = new L.Control.Draw({
      edit: {
        featureGroup: new L.FeatureGroup(),
      },
    });
    map.addControl(drawControl);

    // Feature group to store drawn layers
    var drawnItems = new L.FeatureGroup().addTo(map);
    map.addLayer(drawnItems);

    // Event listener for when a new feature is created
    map.on("draw:created", function(event) {
      var layer = event.layer,
        feature = (layer.feature = layer.feature || {});
      feature.type = feature.type || "Feature";
      var props = (feature.properties = feature.properties || {});
      drawnItems.addLayer(layer);

      // Dapatkan koordinat centroid polygon
      var centroid = turf.centroid(layer.toGeoJSON());
      var latitude = centroid.geometry.coordinates[1];
      var longitude = centroid.geometry.coordinates[0];

      // Gunakan OpenCage Geocoding API untuk mendapatkan informasi lokasi
      // sebaiknya pada bagian apiKey di simpan dalam env supaya lebih aman
      var apiKey = 'c27f372189e942e0a16ae5dccb593257';
      var geocodingUrl = `https://api.opencagedata.com/geocode/v1/json?q=${latitude}+${longitude}&key=${apiKey}&language=id`;

      console.log(geocodingUrl);
      $.get(geocodingUrl, function(data) {
        var kode_pos = data.results[0].components.postcode || 'Tidak diketahui';
        var jalan = data.results[0].components.road || 'Tidak diketahui';

        document.getElementById("alamat").value = jalan;
        document.getElementById("kode_pos").value = kode_pos;
      });

      var geojson = JSON.stringify(event.layer.toGeoJSON());
      document.getElementById("geojson").value = geojson;
    });
  </script>
</body>

</html>