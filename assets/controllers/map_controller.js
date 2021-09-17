import { Controller } from 'stimulus';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet'
import 'leaflet-gpx'

const ACCESS_TOKEN = "pk.eyJ1IjoidHdvaHVuZHJlZGNvdWNoZXMiLCJhIjoiY2tzOWJwZWgyMDJ5czJubjN6d2xnZ3N1aiJ9.E5bSII_vs6-qRLdmfXYlVg"

const BASE_MAPS = {
    watercolor: 'http://c.tile.stamen.com/watercolor/{z}/{x}/{y}.jpg',
    stamenToner: 'http://a.tile.stamen.com/toner/{z}/{x}/{y}.png',
    hikeBike: 'https://tiles.wmflabs.org/hikebike/{z}/{x}/{y}.png',
    osmFrance: 'http://a.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png',
    standard: 'https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png',
}

const BASE_URL = '/';

const
    GPX_LINE_COLOR_DEFAULT = '#60374b',
    GPX_LINE_COLOR_HIGHLIGHT = '#ee526d';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="map" attribute will cause
 * this controller to be executed. The name "map" comes from the filename:
 * map_controller.js -> "map"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static targets = [
        "map",
        "progress",
        "progressBar",
        "error",
        "errorMessage"
    ]

    connect() {
        const self = this;
        const gpxTracks = JSON.parse(this.element.getAttribute('data-gpx-tracks'));

        const options = {
            center: [50.110924, 8.682127],
            zoom: 10,
            scrollWheelZoom: false
        };

        const map = L.map('map', options);

        new L.TileLayer(BASE_MAPS.standard, {
            attribution: 'Map data &copy; <a href="https://www.osm.org" target="_blank" rel="noreferrer nofollow">OpenStreetMap</a>',
        }).addTo(map);

        let filesLoaded = 0;

        let bounds;

        for (const [, gpxTrack] of Object.entries(gpxTracks)) {

            const g = new L.GPX(BASE_URL + gpxTrack.file, {
                async: true,
                marker_options: {
                    startIconUrl: null,
                    endIconUrl: null,
                    shadowUrl: null,
                },
                polyline_options: {
                    color: GPX_LINE_COLOR_DEFAULT,
                    opacity: 1,
                    weight: 3,
                    lineCap: 'round'
                }
            });

            g.on('loaded', function (e) {
                const gpx = e.target;
                
                filesLoaded++;

                if (bounds) {
                    if (gpx.getBounds()._northEast.lng > bounds._northEast.lng) {
                        bounds._northEast.lng = gpx.getBounds()._northEast.lng;
                    }
                    if (gpx.getBounds()._northEast.lat > bounds._northEast.lat) {
                        bounds._northEast.lat = gpx.getBounds()._northEast.lat;
                    }
                    if (gpx.getBounds()._southWest.lng < bounds._southWest.lng) {
                        bounds._southWest.lng = gpx.getBounds()._southWest.lng;
                    }
                    if (gpx.getBounds()._southWest.lat < bounds._southWest.lat) {
                        bounds._southWest.lat = gpx.getBounds()._southWest.lat;
                    }
                } else {
                    bounds = gpx.getBounds();
                }

                // map.fitBounds(bounds);

                const progressInPercentage = Math.round(filesLoaded / gpxTracks.length * 100);

                self.progressBarTarget.setAttribute('valuenow', progressInPercentage);
                self.progressBarTarget.style.width = progressInPercentage + '%';


                if (filesLoaded === gpxTracks.length) {
                    map.fitBounds(bounds);

                    setTimeout(() => {
                        self.progressTarget.style.visibility = 'hidden';
                        self.mapTarget.classList.remove('loading');
                    }, 1000);
                }
            }).on('error', function(e) {
                console.log('Error loading file: ' + e.err);

                self.errorTarget.show();
                self.errorMessageTarget.innerText = 'Fehler beim Laden des GPX Tracks.';

            });

            if (gpxTrack.title) {
                g.bindTooltip(gpxTrack.title);
            }

            if (gpxTrack && gpxTrack.href) {
                g.on('click', function(e) {
                    window.location.assign(gpxTrack.href);
                }).on('mouseover', function(e) {
                    e.target.setStyle({color: GPX_LINE_COLOR_HIGHLIGHT})
                }).on('mouseout', function(e) {
                    e.target.setStyle({color: GPX_LINE_COLOR_DEFAULT})
                });
            }

            g.addTo(map);
        }
    }
}
