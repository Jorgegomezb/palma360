<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>GDIE Parte 1</title>
    <meta name="description" content="Hello, WebVR! • A-Frame">
    <script src="https://cdn.dashjs.org/latest/dash.all.min.js"></script>
    <script src="https://aframe.io/releases/0.9.0/aframe.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


</head>
<?php
    
    $video = $_GET["video"];
   
    if(empty($video)){
        $video = "p1";
    }
    if($video[0] == 'p'){
        $loop = "true";
    }else{
        $loop = "false";
    }
?>

<body>
    <a-scene id="escena" cursor="rayOrigin: mouse">
        <a-assets>
            <video id="palma" controls data-dashjs-player autoplay crossorigin muted loop=<?php echo $loop?>
                <source id="source_video" src=<?php
                    $path = "https://ltim.uib.es/files/palma360/".$video."/dash.mpd";
                    echo $path;
                ?> ></source>
            </video>
        </a-assets>
        <a-entity id="camera" camera look-controls="reverseMouseDrag: true">
        <!--<a-cursor id="cursor" geometry='primitive: ring', material='color: #FFC0CB;\shader: flat'>
        </a-cursor>-->
        <!--Esto no se mueve, rectangulo y marca de agua del HUD-->
            <a-plane id="hud"  color="#CCC" position="0 -0.5 -0.8" height="0.5" width="1" material="opacity: 0.2" geometry>
                <a-image id="fondo_hud" src="img/info.png" position="0 -0.5 -0.8" material="opacity: 0.2"></a-image>
                <!--Texto e imagen del HUD-->
                <!--<a-image src="img/info.png" position="-0.5 -0.5 -0.8" width="1" height="1" ></a-image>-->
                 <!--<a-text id="textPrimitive" position="0 -0.5 -0.8"  width="1" hidden></a-text>-->
            </a-plane>
         </a-entity>       
        <a-videosphere src="#palma"></a-videosphere>
        
        <!--<a-image id="siguiente"  src="img/siguiente.png" height="0.07" width="0.07" position="-0.4  -0.01 -0.90"></a-image>-->
    </a-scene>
</body>
</html>
<script>
    let escena = document.getElementById('escena');
    let video = document.getElementById('palma');
    let camera = document.getElementById('camera')
    var posicion_imagen_hud= '-0.5 -0.5 -0.8';
    var nombre_video ="<?php echo $video; ?>";
    cargaInfos(nombre_video);
    cargaSiguientes(nombre_video);

    function cargaInfos(ruta){
        $.getJSON("infos.json", function(data){       
            $.each( data, function( key, val ) {
                i=data;
                
                if(key == ruta){
                    $.each(val,function(clave,valor){
                        var position = ""+valor.x+" "+valor.y+" "+valor.z;
                        appendEsfera(valor.id,position,"info","yellow");
                        console.log(valor.url)
                        appendImagen("img_"+valor.id,valor.imagen,posicion_imagen_hud,0,valor.url);
                        appendText("txt_"+valor.id, valor.texto, valor.url);
                    });
                }                
            });
        });
    }
    function cargaSiguientes(ruta){
        $.getJSON("siguientes.json", function(data){       
            $.each( data, function( key, val ) {
                i=data;
                
                if(key == ruta){
                    $.each(val,function(clave,valor){
                        var position = ""+valor.x+" "+valor.y+" "+valor.z;
                        var rotation = valor.rotation;
                        if(valor.id == "siguiente_p3"){
                            appendEsfera(valor.id,position,"siguiente","red");
                        }else{
                            appendEsfera(valor.id,position,"siguiente","green");
                        }
                        
                    });
                }                
            });
        });
    }
    function appendEsfera(id, position,clase,color) {
        var sphereEl = document.createElement('a-sphere');
        sphereEl.setAttribute('id', id);
        sphereEl.setAttribute('color',color);
        sphereEl.setAttribute('radius', 0.02);
        sphereEl.setAttribute('position', position);
        sphereEl.setAttribute('class', clase);
        escena.appendChild(sphereEl);
        sphereEl.addEventListener('loaded', function () {
            console.log('sphere attached');
        });
    }

    function appendImagen(id,src, position,rotation ,url) {
        var imagenEl = document.createElement('a-image');
        imagenEl.setAttribute('id', id);
        imagenEl.setAttribute('src', src);
        imagenEl.setAttribute('position', position);
        console.log("imagen url:" +url);
        imagenEl.setAttribute('href', url);
        if(id.includes("siguiente")){
            imagenEl.setAttribute('class', "siguiente");
            imagenEl.setAttribute('width', 0.05);
            imagenEl.setAttribute('height', 0.05);
            if(nombre_video.includes("r")){
                imagenEl.setAttribute("rotation",rotation);
                escena.appendChild(imagenEl);
            }else{
                escena.appendChild(imagenEl);
            }
            
        }else{
            imagenEl.setAttribute('class', "imagen");
            imagenEl.setAttribute('visible', false);
            hud.appendChild(imagenEl);
            
        }
        
        imagenEl.addEventListener('loaded', function () {
            console.log('image attached');
        });
    }

    function appendText(id,texto,url) {
        var textEl = document.createElement('a-text');
        textEl.setAttribute('id', id);
        textEl.setAttribute('value', texto);
        textEl.setAttribute('href', url );
        textEl.setAttribute('position', '0 -0.5 -0.8');
        textEl.setAttribute('class', "texto");
        textEl.setAttribute('width', 1);
        textEl.setAttribute('anchor', "left");
        textEl.setAttribute('align', "left");
        textEl.setAttribute('visible', false);
        hud.appendChild(textEl);
        textEl.addEventListener('loaded', function () {
            console.log('text attached');
        });
    }


    window.onload = function() {
    
        let cursor = document.getElementById('cursor');
        

        var infos = document.querySelectorAll(".info");

        var imagenes = document.querySelectorAll(".imagen");        
        var siguientes = document.querySelectorAll(".siguiente");

        var link="";
        /**Click de infos */
        infos.forEach(element => {
            element.addEventListener('click',function(){
                var elemshud = hud.children;
                
                for (var i = 0; i < elemshud.length; i++) {
                    if(elemshud[i].id != "fondo_hud"){
                        elemshud[i].setAttribute("visible",false);
                        
                        //link = elemshud[i].getAttribute('href');
                    }
                }
                var imagen_enlazada = document.getElementById("img_"+element.id);
                imagen_enlazada.setAttribute('visible',true);
                var texto_enlazado = document.getElementById("txt_"+element.id);
                texto_enlazado.setAttribute('visible',true);
            });
        });

        /**Click para siguientes */
        siguientes.forEach(element => {
            if(nombre_video.includes('r')){
                 element.setAttribute("visible",false);
            }           
            element.addEventListener('click',function(){
                var nuevo_video = this.id.split("_");
                if(nuevo_video[1] == nombre_video){
                    video.currentTime = 20;
                    element.setAttribute("visible",false);
                    var esfera2 = document.getElementById("siguiente_p2");
                    esfera2.setAttribute('visible',false);
                    video.play();
                }else{
                    console.log(nuevo_video)
                    cambiarVideo(nuevo_video[1]);
                }
                
            });
        });


        hud.addEventListener('click',function(){
            var elems = hud.children;
            
            for(var i = 0; i<elems.length; i++){
                var visible = elems[i].getAttribute("visible");
                console.log(elems[i].id+"---");
                if(elems[i].id != "fondo_hud" && visible){
                    link= elems[i].getAttribute("href");

                }
            }
            if (link != ""){
                window.open(link, '_blank');
            }
            
            
        });

        video.onended= function(){
            mostrarEnlaces(true)
        };

        function mostrarEnlaces(final){
            if(final){
                siguientes.forEach(element => {
                    if(element.getAttribute("id")!= "siguiente_p2" && element.getAttribute("id")!= "continua_r1a"){
                        console.log(element.getAttribute("id"));
                        element.setAttribute("visible",true);
                    }
                });
            }else{
                siguientes.forEach(element => {
                    if(element.getAttribute("id") == "siguiente_p2" || element.getAttribute("id")== "continua_r1a"){
                        element.setAttribute("visible",true);
                    }
                });
            }
            
        }
    

        /**Carga de información del json */

    

        /*Script aux para sacar posiciones. BORRAR CUANDO JSONS CREADOS 
        cursor.addEventListener('click', function (evt) {
            console.log(evt.detail.intersection);
        });*/

        //Script para cambiar de ventana 
        function cambiarVideo(nuevo_video){
            if(nuevo_video =="p3"){
                window.location.href = "https://alumnes-ltim.uib.es/gdie1903/#p3#r1b";
            }else{
                var str = window.location;
                var query_string = str.search;

                var search_params = new URLSearchParams(query_string);

                search_params.set('video', nuevo_video);

                // change the search property of the main url
                str.search = search_params.toString();

                // the new url string
                var new_url = str.toString();
            }
            


        }


        video.addEventListener('timeupdate',function(){
            var tiempo_video = video.currentTime;
        /**Codigo para parar la ruta para ver P2 */
        if(nombre_video.includes("r1a")){
            if (video.currentTime >= 19 && video.currentTime < 20){
                video.pause();
                mostrarEnlaces(false);
            }
        }
        },false);
        
        

    }
        
</script>