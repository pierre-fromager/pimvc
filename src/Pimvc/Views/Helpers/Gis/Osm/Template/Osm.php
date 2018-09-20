<script type="text/javascript">

    var markers = <?= $markersJson ?>;
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

        var localLayer = '<?= $layer ?>';

        L.tileLayer(localLayer, {}).addTo(map);
        L.control.scale({imperial: false}).addTo(map);
        L.control.mousePosition().addTo(map);

        var pointList = [];

        var railwayIcon = L.icon.fontAwesome({
            iconClasses: 'fa fa-subway',
            markerColor: '#00a9ce',
            iconColor: '#FFF'
        });


        for (var marker of markers) {
            console.log(marker);
            // console.log(railwayIcon.options.markerColor);
            var baseIcon = L.Icon.extend({options: marker.options});
            var markerIcon = new baseIcon();
            var popupContent = '<h3>' + marker.options.title + '</h3>';
                        var markerLatLng = [marker.lat, marker.lon];
                        pointList.push(markerLatLng);
                        L.marker(markerLatLng, {icon: railwayIcon}).bindPopup(popupContent).addTo(map);
        }


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