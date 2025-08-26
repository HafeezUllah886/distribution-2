@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Locations - {{$user->name}} ({{date('d M Y', strtotime($date))}})</h3>
                </div>
                <div class="card-body">
                        <input type="hidden" name="userID" value="{{$user->id}}">
                        <input type="hidden" name="date" value="{{$date}}">
                   <div class="row">
                    <div class="col-3">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="basic-addon1">Start Time</span>
                            </div>
                            <select name="start" id="start" class="form-control">
                                @foreach ($times as $time)
                                    <option value="{{$time}}">{{$time}}</option>
                                @endforeach
                            </select>
                          </div>
                    </div>
                    <div class="col-3">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="basic-addon1">End Time</span>
                            </div>
                            <select name="end" id="end" class="form-control">
                                @foreach ($times as $time)
                                    <option value="{{$time}}" @if($loop->last) selected @endif>{{$time}}</option>
                                @endforeach
                            </select>
                          </div>
                    </div>
                    <div class="col-3">
                        <button class="btn btn-primary" id="load_map">Show</button>
                    </div>
                   </div>
                   
                   <div class="row">
                    <div class="col-12">
                        <div id="map" style="width:100%; height:500px; margin-top:20px;"></div>

                    </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Default Modals -->

    
@endsection
@section('page-js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1o3ScSN4Y5geOXlTk4nZB-tVMqtPJqX0&callback=initMap" async defer></script>
<script>
    let map;
    let directionsService;
    let directionsRenderer;

    function initMap() {
        try {
            const mapElement = document.getElementById('map');
            if (!mapElement) {
                console.error('Map container not found');
                return;
            }
            
            map = new google.maps.Map(mapElement, {
                zoom: 14,
                center: {lat: 30.1798, lng: 66.9750}
            });
            directionsService = new google.maps.DirectionsService();
            
            // Initialize the map with default data
            loadMapData();
        } catch (error) {
            console.error('Error initializing map:', error);
        }
    }

    function loadMapData() {
        try {
            const userInput = document.querySelector('input[name="userID"]');
            const dateInput = document.querySelector('input[name="date"]');
            const startSelect = document.getElementById('start');
            const endSelect = document.getElementById('end');

            if (!userInput || !dateInput || !startSelect || !endSelect) {
                console.error('Required form elements not found');
                return;
            }

            const user_id = userInput.value;
            const date = dateInput.value;
            const start = startSelect.value;
            const end = endSelect.value;

            // Format the date for the API
            const formattedDate = new Date(date).toISOString().split('T')[0];
            
            // Make sure we're sending proper date parameters
            const params = new URLSearchParams({
                user_id: user_id,
                date: formattedDate,
                start: start,
                end: end
            });

            fetch(`/get-user-locations?${params}`)
                .then(response => {
                    // Check if the response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            console.error('Expected JSON, got:', text);
                            throw new Error('Invalid response format from server');
                        });
                    }
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data || data.length === 0) {
                        alert("No location data found for the selected time range.");
                        return;
                    }

                    // Clear existing markers and directions
                    if (directionsRenderer) {
                        directionsRenderer.setMap(null);
                    }
                    
                    // Reset map view
                    const firstLocation = { 
                        lat: parseFloat(data[0].latitude), 
                        lng: parseFloat(data[0].longitude) 
                    };
                    map.setCenter(firstLocation);
                    map.setZoom(14);

            // Add markers for each location
            let bounds = new google.maps.LatLngBounds();
            let waypoints = [];

            data.forEach((loc, index) => {
                let position = {lat: parseFloat(loc.latitude), lng: parseFloat(loc.longitude)};
                bounds.extend(position);

                new google.maps.Marker({
                    position: position,
                    map: map,
                    label: `${index + 1}`,
                    title: `Time: ${loc.time}`
                });

                if (index > 0 && index < data.length - 1) {
                    waypoints.push({
                        location: position,
                        stopover: true
                    });
                }
            });

            // Draw realistic route using Directions API
            let origin = {lat: parseFloat(data[0].latitude), lng: parseFloat(data[0].longitude)};
            let destination = {lat: parseFloat(data[data.length - 1].latitude), lng: parseFloat(data[data.length - 1].longitude)};

            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: true, // keep our custom markers
                polylineOptions: {
                    strokeColor: '#FF0000',
                    strokeWeight: 4
                }
            });

            directionsService.route({
                origin: origin,
                destination: destination,
                waypoints: waypoints,
                optimizeWaypoints: false,
                travelMode: google.maps.TravelMode.DRIVING
            }, function(response, status) {
                if (status === 'OK') {
                    directionsRenderer.setDirections(response);
                } else {
                    console.error('Directions request failed due to ' + status);
                }
            });

            map.fitBounds(bounds);
        });
    } catch (error) {
            console.error('Error loading map data:', error);
            alert('An error occurred while loading the map. Please check the console for details.');
        }
    }

    // Initialize the map when the page loads
    window.initMap = initMap;
    
    // Add event listener for the load button
    document.addEventListener('DOMContentLoaded', function() {
        const loadButton = document.getElementById('load_map');
        if (loadButton) {
            loadButton.addEventListener('click', loadMapData);
        }
    });
</script>
@endsection
