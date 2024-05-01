<?php
require 'fungsi.php';

$id = $_GET["id"];

$map = query("SELECT * FROM maps WHERE id = $id")[0];
// var_dump($map);

// cek apakah tombol submit sudah ditekan atau belum
if (isset(($_POST["submit"]))) {

  // cek apakah data berhasil diubah
  if (ubah($_POST) > 0) {
    echo "
			<script>
				alert('Data berhasil diubah');
				document.location.href = 'create.php';
			</script>
		";
  } else {
    echo "
			<script>
				alert('Data gagal diubah');
				document.location.href = 'create.php';
			</script>
		";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    #geojson {
      width: 100%;
      box-sizing: border-box;
    }
  </style>

  <title>Edit Data</title>
</head>

<body>
  <div id="map" style="height: 500px;"></div>

  <div style="text-align: center; margin-top: 20px;">
    <a href="create.php">Kembali Ke Beranda</a>
  </div>

  <form id="dataForm" action="" method="post" enctype="multipart/form-data" style="margin-top: 25px;">
    <input type="hidden" name="id" value="<?php echo $map["id"]; ?>">
    <div>
      <label for="deskripsi">Deskripsi:</label><br>
      <textarea id="deskripsi" name="deskripsi" rows="4" cols="50"><?php echo $map["deskripsi"]; ?></textarea>
    </div>

    <div>
      <label for="geojson">Geojson:</label><br>
      <textarea id="geojson" name="geojson" rows="4" cols="50"><?php echo $map["geojson"]; ?></textarea>
    </div>

    <div>
      <button type="submit" name="submit" style="margin-top: 40px;">Edit Data</button>
    </div>
  </form>

  <script src="leaflet-draw/edit/EditToolbar.js"></script>
  <script src="leaflet-draw/edit/handler/EditToolbar.Edit.js"></script>
  <script src="leaflet-draw/edit/handler/EditToolbar.Delete.js"></script>

  <script src="leaflet-draw/edit/handler/Edit.Poly.js"></script>
  <script src="leaflet-draw/edit/handler/Edit.SimpleShape.js"></script>
  <script src="leaflet-draw/edit/handler/Edit.Circle.js"></script>
  <script src="leaflet-draw/edit/handler/Edit.Rectangle.js"></script>
  <script src="leaflet-draw/edit/handler/Edit.Marker.js"></script>

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

    var dataMap = <?php echo json_encode($map); ?>;

    const geojson = JSON.parse(dataMap.geojson);

    var drawnItems = L.geoJSON(geojson, {
      onEachFeature: function(feature, layer) {
        layer.addTo(map);
      }
    });

    map.addLayer(drawnItems);
    var drawControl = new L.Control.Draw({
      edit: {
        featureGroup: drawnItems,
      },
    });
    map.addControl(drawControl);

    // Event listener for when a new feature is created
    map.on('draw:created', function(event) {
      var layer = event.layer,
        feature = layer.feature = layer.feature || {};
      feature.type = feature.type || "Feature";
      var props = feature.properties = feature.properties || {};
      // drawnItems.addLayer(layer);

      // console.log(props);

      // var geojson = JSON.stringify(event.layer.toGeoJSON());
      // document.getElementById("geojson").value = geojson;
    });
  </script>
</body>

</html>