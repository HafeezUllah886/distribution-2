<!DOCTYPE html>
<html>
<head>
    <title>User Location Timeline</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1o3ScSN4Y5geOXlTk4nZB-tVMqtPJqX0"></script>
</head>
<body>
    <h2>User Location Timeline</h2>
    <form id="filter-form">
        <select id="user_id" name="user_id" required>
            <option value="">Select User</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        <input type="date" id="date" name="date" required>
        <button type="submit">Show</button>
    </form>

    <div id="map" style="width:100%; height:500px; margin-top:20px;"></div>

    <script>
        let map;
        let directionsService;
        let directionsRenderer;
    
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: {lat: 30.1798, lng: 66.9750}
            });
            directionsService = new google.maps.DirectionsService();
        }
    
        initMap();
    
        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault();
    
            let user_id = document.getElementById('user_id').value;
            let date = document.getElementById('date').value;
    
            fetch(`/get-user-locations?user_id=${user_id}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    alert("No data found!");
                    return;
                }
    
                // Clear previous map
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 14,
                    center: {lat: parseFloat(data[0].latitude), lng: parseFloat(data[0].longitude)}
                });
    
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
        });
    </script>
    
    
</body>
</html>
