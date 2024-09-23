const wdk_map_draw = (wdk_map, options = null) => {
    var $ = jQuery,
        map = wdk_map,
        editableLayers, drawPluginOptions, drawControl, removeButtonControl,outGreyMask;

    const event_init = () => {
        editableLayers = new L.FeatureGroup();
        map.addLayer(editableLayers);

        drawPluginOptions = {
            position: 'topleft',
            draw: {
                drag: true,
                polyline: false,
                polygon: false,
                circle: false,
                rectangle: {
                    shapeOptions: {
                        clickable: false,
                        color: '#ff7800',
                        weight: 1,
                    },
                    showRadius: true,
                },
                circlemarker: false,
                marker: false,
                showArea: true,
                showLength: true
            }
        };

        drawPluginOptions = $.extend(drawPluginOptions, options);

        drawControl = new L.Control.Draw(drawPluginOptions);
        map.addControl(drawControl);

        // Create a custom button for removing all rectangles
        const RemoveRectanglesButton = L.Control.extend({
            options: {
                position: 'topright', /*drawPluginOptions.position*/
            },
            onAdd: function () {
                var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom leaflet-draw-toolbar');
                container.innerHTML = '<a class="leaflet-draw-draw-rectangle-remove" href="#"></a>';
                container.style.display = 'none';

                container.onclick = function () {
                    removeAllRectangles();
                };

                return container;
            }
        });

        // Add the custom button to the map
        removeButtonControl = new RemoveRectanglesButton();
        map.addControl(removeButtonControl);

        map.on(L.Draw.Event.CREATED, function (e) {
            var type = e.layerType,
                layer = e.layer;

            if (type === 'rectangle') {
                setRectangle(layer, false);

                // Get coordinates of the polygon
                var coordinates = layer.getLatLngs();
                if(coordinates) {
                    $('.wdk-search-form').find('input[name="rectangle_ne"]').val(coordinates[0][1].lat+','+coordinates[0][1].lng);
                    $('.wdk-search-form').find('input[name="rectangle_sw"]').val(coordinates[0][3].lat+','+coordinates[0][3].lng);
                    event_rectange_changed();
                }
            }

            editableLayers.addLayer(layer);
        });

        map.on('draw:drawstart', function (e) {
            editableLayers.clearLayers();
            removeOuther();
        });

        
        // Custom control for the "Search by Drawing" button
        var CustomControl = L.Control.extend({
            options: {
                position: 'topright' // Position of the control
            },

            onAdd: function (map) {
                var container = L.DomUtil.create('div', 'custom-draw-button');
                container.innerHTML = 'Search by Drawing';
                container.onclick = function () {
                    new L.Draw.Rectangle(map, drawControl.options.draw.rectangle).enable();
                };
                return container;
            }
        });

        // Add the custom control to the map
        map.addControl(new CustomControl());
    };

    const setRectangle = (layer, fitBounds = true) => {
        editableLayers.addLayer(layer);

        // Set the zoom level
        if(fitBounds) {
            let zoom_map = map.getZoom();
            map.fitBounds(layer.getBounds());
            map.setZoom(zoom_map);
        }

        removeButtonControl.getContainer().style.display = 'block';
    };

    const event_rectange_changed  = (coordinates = '') => {
        if(true) {
            $('.wdk-search-form .wdk-search-start[type="submit"]').trigger('click');
        }
    };

    const removeAllRectangles = () => {
        editableLayers.clearLayers();
        removeButtonControl.getContainer().style.display = 'none';
           
        // Clear the input fields
        $('.wdk-search-form').find('input[name="rectangle_ne"]').val('');
        $('.wdk-search-form').find('input[name="rectangle_sw"]').val('');

        removeOuther();
    };


    const drawOuther = (bounds, fitBounds = true) => {
        //editableLayers.addLayer(layer);

        // Create a rectangle with the defined bounds
        var rectangle = [
            [bounds[0][0], bounds[0][1]],
            [bounds[0][0], bounds[1][1]],
            [bounds[1][0], bounds[1][1]],
            [bounds[1][0], bounds[0][1]]
        ];

        // Create a polygon for the grey area covering the whole map
        var outerBounds = [
            [[-90, -180], [90, -180], [90, 180], [-90, 180]],
            rectangle
        ];

        // Add the grey overlay with a hole in the middle where the rectangle is
        outGreyMask = L.polygon(outerBounds, {
            color: "grey",
            weight: 1,
            fillColor: "grey",
            fillOpacity: 0.4,
            interactive: false
        }).addTo(wdk_map);

        // Set the zoom level
        if(fitBounds) {
            let zoom_map = map.getZoom();
            map.fitBounds(bounds);
            map.setZoom(zoom_map);
        }

        removeButtonControl.getContainer().style.display = 'block';
    };

    const removeOuther = () => {
        if(outGreyMask) {
            map.removeLayer(outGreyMask);
            outGreyMask = null;
        }
    };

    // FeatureGroup is to store editable layers
    event_init();

    return {
        setRectangle: setRectangle,
        removeOuther: removeOuther,
        drawOuther: drawOuther,
        removeAllRectangles: removeAllRectangles
    };
};
