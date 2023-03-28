function evidenziaLibro(e) {
    console.log("clicked");
    var idLibro = e.target.id;
    var posizione = idLibro.split("-");
    
    var xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (this.status == 200) {
            //document.getElementById("messaggio").innerHTML = this.response;
            console.log(this.response);
        }
    };

    var url = ""
    if (posizione[0] == "1") {
        url = new URL('http://scaffale1.local/libro');
    } else {
        url = new URL('http://scaffale2.local/libro');
    }

    url.searchParams.set('ripiano', posizione[1]);    // ripiano
    url.searchParams.set('pos', posizione[2]);    // posizione

    xhttp.open("GET", url, true);
    xhttp.send();
}

window.addEventListener('load', function () {
    var arrayRicerca = document.getElementsByClassName("btn");

    console.log(arrayRicerca);
    for (let i=0; i<arrayRicerca.length; i++) {
        arrayRicerca[i].addEventListener("click", evidenziaLibro);
    }
});

