

class myMarker
{
  constructor(plz, ortName, lat, lng, anzahl)
  {
    this.plz = plz;
    this.ortName = ortName;
    this.lat = lat;
    this.lng = lng;
    this.anzahl = anzahl;
  }
  static getfromphp = function (obj){
    return new myMarker (obj.plz, obj.ortName, obj.lat, obj.lng, obj.anzahl);
  };
}
    
function initMap() {
  
  fetch('marker.php')
    .then(response => response.json())
    .then(data => fuckjs(data));

  var myLatLng = { lat: 47.1443, lng: 8.4954 };
  const map = new google.maps.Map(document.getElementById("map"), {
  zoom: 9,
  center: myLatLng
  });

  function fuckjs(objects)
  {
    objects.forEach(function(object)
    {
      var marker = myMarker.getfromphp(object);
      console.log(object)
      new google.maps.Marker({
        position: { lat: marker.lat, lng: marker.lng},
        map,
        title: (marker.ortName + ', ' + marker.plz + ': ' + marker.anzahl),
      });
      
    })
  }
}