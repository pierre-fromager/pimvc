<script type="text/javascript">

    $j(document).ready(function () {

    
            var northEast = L.latLng(49.17991, 1.69739);
            var southWest = L.latLng(48.5516, 3.01025);
            var bounds = L.latLngBounds(southWest, northEast);
            var mapOpts = {
                maxBounds: bounds,
                maxZoom: 18,
                minZoom: 9,
                fullscreenControl: {
                    pseudoFullscreen: false
                }
            };

        var map = L.map('map', mapOpts).setView(<?= $options->center(true); ?>,<?= $options->zoom; ?>);
        var basicLayerTileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        var wikiMediaLayerTileUrl = 'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png';
        var humanitarialTileUrl = 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
        var osmTileFrance = 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png';
        var osmTileForest = 'https://tile.thunderforest.com/landscape/{z}/{x}/{y}.png';
        var osmTileForestOutdoor = 'https://tile.thunderforest.com/outdoors/{z}/{x}/{y}.png';
        var osmTileTransportation = 'https://tile.thunderforest.com/transport/{z}/{x}/{y}.png';
        var osmTileBw = 'http://a.tile.stamen.com/toner/{z}/{x}/{y}.png';
                var osmTileOpenRailwayStd = 'https://{s}.tiles.openrailwaymap.org/standard/{z}/{x}/{y}.png';
        var osmTileOpenRailwaySpeed = 'https://{s}.tiles.openrailwaymap.org/maxspeed/{z}/{x}/{y}.png';
        var localLayer = '<?= $baseUrl ?>/metro/lignes/tiles/s/{s}/z/{z}/x/{x}/y/{y}';
        var mirrorOvhLayer ='http://osm.pier-infor.fr/{z}/{x}/{y}.png';

        L.tileLayer(localLayer, {}).addTo(map);
        L.control.scale({imperial: false}).addTo(map);
        L.control.mousePosition().addTo(map);

        var pointList = [];

        var railwayIcon = L.icon.fontAwesome({
            iconClasses: 'fa fa-subway',
            markerColor: '#00a9ce',
            iconColor: '#FFF'
        });

    <?php foreach ($markers as $marker) : ?>
                var baseIcon = L.Icon.extend({
                options: <?= \json_encode($marker->getOptions()->icon, JSON_PRETTY_PRINT) ?>
            });
            var markerIcon = new baseIcon();
            var popupContent = '<h3><?= addslashes($marker->getOptions()->title); ?></h3>';
            var markerLatLng = <?= $marker->getLatLng(true) ?>;
            pointList.push(markerLatLng);
            L.marker(markerLatLng, {icon: railwayIcon}).bindPopup(popupContent).addTo(map);
    <?php endforeach; ?>
        if (pointList.length > 1) {
            var firstpolyline = new L.Polyline(pointList, {
                color: '#00a9ce',
                weight: 3,
                opacity: 0.5,
                smoothFactor: 1
            });
            firstpolyline.addTo(map);
        }
    });

</script>

<div id="map" style="min-height:<?= $mapHeight; ?>px"></div>