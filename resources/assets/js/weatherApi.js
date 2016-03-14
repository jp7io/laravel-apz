function loadWeather() {
    var url = 'http://api.openweathermap.org/data/2.5/weather?q=Sao_Paulo,br&units=metric&APPID=YOUR_APPID'
    $.getJSON(url, function(data) {
        $('#weather').html('Sao Paulo: ' + data.main.temp + ' Celsius');
    });
}
