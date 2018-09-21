<script type="text/javascript">

    var markers = <?= $markersJson ?>;
    var polylines = <?= $polylinesJson ?>;

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
        var layerUrl = '<?= $layer ?>';

        L.tileLayer(layerUrl, {}).addTo(map);
        L.control.scale({imperial: false}).addTo(map);
        L.control.mousePosition().addTo(map);

        var railwayIcon = L.icon.fontAwesome({
            iconClasses: 'fa fa-subway',
            markerColor: '#00a9ce',
            iconColor: '#FFF'
        });

        for (var marker of markers) {
            L.marker(
                    [marker.lat, marker.lon], {icon: railwayIcon}
            ).bindPopup('<h3>' + marker.options.title + '</h3>')
                    .addTo(map);
        }

        if (polylines.length > 0) {
            for (c = 0; c < polylines.length; c++) {
                var polyline = new L.Polyline(polylines[c].tupple, polylines[c].options);
                polyline.addTo(map);
            }
        }

    });

</script>

<div id="map" style="min-height:<?= $mapHeight; ?>px"></div>