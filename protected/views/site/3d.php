<!--<input type="file" value="internal file" id="fileselect" onchange="load_local_file(this.files[0]);">
<iframe id="vs_iframe" src="http://www.viewstl.com/?embeded" style="border:0;margin:0;width:100%;height:100%;"></iframe>
<script>
    document.getElementById('vs_iframe').contentWindow.postMessage({msg_type:'get_info'}, '*');


function load_local_file(f)
{
        document.getElementById('vs_iframe').contentWindow.postMessage({msg_type:'load', file:f}, '*');
}
</script>-->


<!DOCTYPE html>

<script src="<?php echo Assets::themeUrl("vendor/3dviewer/js/three.min.js"); ?>"></script>
<script src="<?php echo Assets::themeUrl("vendor/3dviewer/js/webgl_detector.js"); ?>"></script>
<script src="<?php echo Assets::themeUrl("vendor/3dviewer/js/OrbitControls.js"); ?>"></script>
<script src="<?php echo Assets::themeUrl("vendor/3dviewer/js/parser.js"); ?>"></script>
<script src="<?php echo Assets::themeUrl("vendor/3dviewer/js/read_internal.js"); ?>"></script>




<html lang="en">
    <head>
        <title>Free online STL viewer</title>
        <meta charset="utf-8">
        <meta name="description" content="View 3D STL files directly in your browser - no software installation is required">
        <style type="text/css">
            html {margin: 0;}
            body {margin: 0;}

            .cinfo
            {
                width:250px;
                background:#DDDDDD;
                border: 1px solid;
                border-radius: 25px;
                padding:2px 10px 2px 10px;
                margin-bottom:20px;
            }

            .cinfo table {width:100%;}
            .cinfo tr {text-align:left;vertical-align:top;}

            #statswrap
            {
                display:inline-block;
                vertical-align:top;
                -webkit-transition: width 1.5s;
                transition: width 1.5s;
                overflow:hidden;
            }

            .cbar
            {
                width:20px;
                height:20px;
                display:inline-block;
                cursor:pointer;
                border:2px solid transparent;
            }

            .cact {margin-bottom:5px;}
            .cact span {border-bottom: 1px dotted #3434AB;color:#3434AB;cursor:pointer;}

            .vslink
            {
                position:absolute;
                bottom:0px;right:1px;
                font:10px Arial;color:#555555;
                text-decoration:none;
            }
        </style>

        <script>
//            (function(i, s, o, g, r, a, m) {
//                i['GoogleAnalyticsObject'] = r;
//                i[r] = i[r] || function() {
//                    (i[r].q = i[r].q || []).push(arguments)
//                }, i[r].l = 1 * new Date();
//                a = s.createElement(o),
//                        m = s.getElementsByTagName(o)[0];
//                a.async = 1;
//                a.src = g;
//                m.parentNode.insertBefore(a, m)
//            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
//
//            ga('create', 'UA-54015102-1', 'auto');
//            ga('send', 'pageview');
        </script>

        <style>
            header
            {
                text-align:center;
                border: 1px solid #909090;
                border-radius: 12px;
                padding:5px;
                margin:5px;
                background:#FFFFFF;
            }

            footer
            {
                float:right;
                font:14px Arial;color:#1010FF;
                margin:30px;
            }

            footer a {text-decoration:none;font:14px Arial;color:#1010FF;}
            footer a:hover {text-decoration:none;font:14px Arial;color:#1010FF;}

            h2 {margin-bottom:5px;margin-top:0px;}

            h4 {margin-left:20px;margin-bottom:1px;margin-top:2px;color:#202050;}
            h5 {margin-left:20px;margin-bottom:2px;margin-top:0px;font-weight: normal;}

            .stitle
            {
                font:20px Arial;color:#2020DD;margin:10px 30px 10px 30px;
            }
        </style>			

<!--        <script src="three.min.js"></script>
        <script src="webgl_detector.js"></script>
        <script src="OrbitControls.js"></script>
        <script src="parser.js"></script>
        <script src="general/read_internal.js"></script>
        <script src="general/read_external.js"></script>-->

        <script> var need_browser = false;</script>

        <!--[if lt IE 10 ]>
                        <script>
                                //window.location.href="/replace_browser.html";
                                need_browser=true;
                        </script>
        <![endif]-->

        <script>
            if (!(typeof FileReader !== "undefined"))
            {
                //window.location.href="/replace_browser.html";
                need_browser = true;
            }
        </script>

        <script>
            var is_ie = !!window.MSStream;
            var waiting = false;
            var mesh = null;
            var material = new THREE.MeshLambertMaterial({color: 0x909090, overdraw: 1, wireframe: false, shading: THREE.FlatShading, vertexColors: THREE.FaceColors});
            
//            var loader = new THREE.ImageLoader();
//
//// load a image resource
//loader.load(
//	// resource URL
//	'/apps/decorate3d/public/themes/frontend/assets/images/logo.png',
//	// Function when resource is loaded
//	function ( image ) {
//		// do something with it
//
//		// like drawing a part of it on a canvas
//		var canvas = document.createElement( 'canvas' );
//		var context = canvas.getContext( '2d' );
//		context.drawImage( image, 100, 100 );
//	},
//	// Function called when download progresses
//	function ( xhr ) {
//		console.log( (xhr.loaded / xhr.total * 100) + '% loaded' );
//	},
//	// Function called when download errors
//	function ( xhr ) {
//		console.log( 'An error happened' );
//	}
//);
//            
            
            
            if (!is_ie)
                //double side not supported on IE
                material.side = THREE.DoubleSide;

            var cancel_download = false;
            var max_file_size = 40000000;
            var mesh_color = '#909090';

            var xsize = 0;
            var ysize = 0;
            var zsize = 0;
            var vol = 0;
            var model_filename = '';
            var view_units = 1; //mm
            var file_units = 1; //mm

            function $id(id)
            {
                return document.getElementById(id);
                console.log(document.getElementById(id));
            }

            function set_view_units(v)
            {
                view_units = v;
                $id("vunits").innerHTML = v == "1" ? "mm" : "in";
                setCookie("units", v == "1" ? "mm" : "in", 1000);
                recalc_units();
            }

            function set_file_units(v)
            {
                file_units = v;
                recalc_units();
            }

            function recalc_units()
            {
                if (file_units == view_units)
                    set_vol_and_size(vol, xsize, ysize, zsize);
                else if (file_units == 1)
                    //file in mm, view in inches
                    set_vol_and_size(vol / 16387.064, xsize / 25.4, ysize / 25.4, zsize / 25.4);
                else
                    //file in inches, view in mm
                    set_vol_and_size(vol * 16387.064, xsize * 25.4, ysize * 25.4, zsize * 25.4);
            }

            function do_resize()
            {
                var height = isNaN(window.innerHeight) ? window.clientHeight : window.innerHeight;
                var width = isNaN(window.innerWidth) ? window.clientWidth : window.innerWidth;
                height = Math.min(height, width - 150);
                $id('cjc').style.height = (height - 220) + 'px';
                $id('cjc').style.width = (height - 220) + 'px';
                $id('cjcwrap').style.height = (height - 220) + 'px';
                $id('cjcwrap').style.width = (height - 220) + 'px';
                $id('cjcdrag').style.top = ((height - 320) / 2) + 'px';
                $id('cjcdrag').style.left = ((height - 570) / 2) + 'px';
                $id('cjcpbar').style.top = ((height - 345) / 2) + 'px';
                $id('cjcpbar').style.left = ((height - 570) / 2) + 'px';
                $id('cjcproc').style.top = ((height - 345) / 2) + 'px';
                $id('cjcproc').style.left = ((height - 570) / 2) + 'px';

                var rsize_width = $id("cjcwrap").getBoundingClientRect().width;
                var rsize_height = $id("cjcwrap").getBoundingClientRect().height;
                renderer.setSize(rsize_width - 5, rsize_height - 5);
            }

            function handleDragOver(e)
            {
                if (waiting)
                    return;

                e.stopPropagation();
                e.preventDefault();
                e.dataTransfer.dropEffect = 'copy';
            }

            function handleFileDrop(e)
            {
                e.stopPropagation();
                e.preventDefault();

                if (waiting)
                    return;
//                console.log(e.dataTransfer.files);
                //first, check if its a file
                if (e.dataTransfer.files.length > 0)
                {
                    if (e.dataTransfer.files[0].size > max_file_size)
                    {
                        alert('File is too big - maximum allowed size is 35mb');
                        return false;
                    }

                    if (!supported_file_type(e.dataTransfer.files[0].name))
                    {
                        alert('File type is not supported');
                        return false;
                    }
                    read_file(e.dataTransfer.files[0]);
                }

                else if (typeof e.dataTransfer.getData("Text") === 'string')
                {
                    //check - maybe a url?
                    read_from_url(e.dataTransfer.getData("Text"));
                }
            }

            function supported_file_type(filename)
            {
                switch (filename.split('.').pop().toLowerCase())
                {
                    case 'stl':
                    case 'obj':
                    case 'vf':
                        return true;
                        break;

                    default:
                        return false;
                }

            }

            function switch_view(v)
            {
                $id('cjcdrag').style.display = ((v == 'drag') ? 'block' : 'none');
                $id('cjcpbar').style.display = ((v == 'pbar') ? 'block' : 'none');
                $id('cjcproc').style.display = ((v == 'proc') ? 'block' : 'none');
                $id('cjc').style.display = ((v == 'cjc') ? 'block' : 'none');
            }

            function after_error()
            {
                switch_view('drag');
                cancel_download = false;
                waiting = false;
                return false;
            }


            function after_file_load(filename, s)
            {
                var vf_data;

                try
                {
                    vf_data = parse_3d_file(filename, s);
                }
                catch (err)
                {
                    vf_data = "Error parsing the file";
                }

                if (typeof vf_data === 'string')
                {
                    alert(vf_data);
                    switch_view('drag');
                    waiting = false;
                    return;
                }

                if (mesh != null) {
                    scene.remove(mesh);
                    mesh = null
                }
                ;

                var geo = new THREE.Geometry;
                geo.vertices = vf_data.vertices;
                geo.faces = vf_data.faces;
                geo.computeBoundingBox();

                geo.computeCentroids();
                geo.computeFaceNormals();
                geo.computeVertexNormals();
                THREE.GeometryUtils.center(geo);
                mesh = new THREE.Mesh(geo, material);

                if (vf_data.colors)
                    set_color($id('white_color'), '#FFFFFF');

                update_mesh_color();

                scene.add(mesh);

                directionalLight.position.x = geo.boundingBox.min.y * 2;
                directionalLight.position.y = geo.boundingBox.min.y * 2;
                directionalLight.position.z = geo.boundingBox.max.z * 2;

                pointLight.position.x = (geo.boundingBox.min.y + geo.boundingBox.max.y) / 2;
                pointLight.position.y = (geo.boundingBox.min.y + geo.boundingBox.max.y) / 2;
                pointLight.position.z = geo.boundingBox.max.z * 2;

                camera.position.set(0, 0, Math.max(geo.boundingBox.max.x * 3, geo.boundingBox.max.y * 3, geo.boundingBox.max.z * 3));
                controls.reset();
                switch_view('cjc');

                xsize = geo.boundingBox.max.x - geo.boundingBox.min.x;
                ysize = geo.boundingBox.max.y - geo.boundingBox.min.y;
                zsize = geo.boundingBox.max.z - geo.boundingBox.min.z;
                vol = compute_vol(geo);
                model_filename = filename;


                guess_file_units(xsize, ysize, zsize);
                recalc_units();

                $id('ifilename').innerHTML = filename.substr(0, 20);
                $id('ifilename').style.visibility = 'visible';

                setTimeout(function() {
                    $id('statswrap').style.width = '280px';
                }, 500);


                //$id('cjcwrap').addEventListener('dragover', handleDragOver, false);
                //$id('cjcwrap').addEventListener('drop', handleFileDrop, false);				

                waiting = false;
            }

            function set_color_by_name(color)
            {
                switch (color.toLowerCase())
                {
                    case "black":
                        set_color($id('black_color'), '#000000');
                        break;
                    case "white":
                        set_color($id('white_color'), '#FFFFFF');
                        break;
                    case "blue":
                        set_color($id('blue_color'), '#0000FF');
                        break;
                    case "green":
                        set_color($id('green_color'), '#00FF00');
                        break;
                    case "red":
                        set_color($id('red_color'), '#FF0000');
                        break;
                    case "yellow":
                        set_color($id('yellow_color'), '#FFFF00');
                        break;
                    case "gray":
                        set_color($id('gray_color'), '#909090');
                        break;
                    case "azure":
                        set_color($id('azure_color'), '#00FFFF');
                        break;
                    case "pink":
                        set_color($id('pink_color'), '#FF00FF');
                        break;
                    case "purple":
                        set_color($id('purple_color'), '#703487');
                        break;
                    case "darkblue":
                        set_color($id('darkblue_color'), '#102075');
                        break;
                    case "brown":
                        set_color($id('brown_color'), '#654321');
                        break;
                    default:
                        //any valid color value goes
                        if (/^#[0-9A-F]{6}$/i.test(color))
                            set_color($id('white_color'), color);
                }
            }


            function set_color(o, o_color)
            {
                c = $id('cpal').getElementsByTagName("div");

                var i = c.length;
                while (i--)
                {
                    if (c[i] == o)
                        c[i].style.border = "2px solid #012101";
                    else
                        c[i].style.border = "2px solid transparent";
                }

                //mesh_color=o.style.background;
                mesh_color = o_color;
                update_mesh_color();
            }


            function update_mesh_color()
            {
                if (mesh == null)
                    return;
                mesh.material.color.set(parseInt(mesh_color.substr(1), 16));
            }

            function compute_vol(geo)
            {
                var x1, x2, x3, y1, y2, y3, z1, z2, z3, i;
                var len = geo.faces.length;
                var totalVolume = 0;

                for (i = 0; i < len; i++)
                {
                    x1 = geo.vertices[geo.faces[i].a].x;
                    y1 = geo.vertices[geo.faces[i].a].y;
                    z1 = geo.vertices[geo.faces[i].a].z;
                    x2 = geo.vertices[geo.faces[i].b].x;
                    y2 = geo.vertices[geo.faces[i].b].y;
                    z2 = geo.vertices[geo.faces[i].b].z;
                    x3 = geo.vertices[geo.faces[i].c].x;
                    y3 = geo.vertices[geo.faces[i].c].y;
                    z3 = geo.vertices[geo.faces[i].c].z;


                    totalVolume +=
                            (-x3 * y2 * z1 +
                                    x2 * y3 * z1 +
                                    x3 * y1 * z2 -
                                    x1 * y3 * z2 -
                                    x2 * y1 * z3 +
                                    x1 * y2 * z3) / 6;
                }

                return Math.abs(totalVolume);
            }

            function numberWithCommas(x)
            {
                var parts = x.toString().split(".");
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                return parts.join(".");
            }

            function set_shading(i)
            {
                if (i == 2)
                    material.wireframe = true;
                else
                {
                    material.wireframe = false;
                    material.shading = ((i == 1) ? THREE.SmoothShading : THREE.FlatShading);
                    if (mesh != null)
                        mesh.geometry.normalsNeedUpdate = true;
                }
            }

            function view_example(ename)
            {
                download_from_local('examples/' + ename, ename);
            }

            function reset()
            {
                if (waiting)
                    return;
                switch_view('drag');
                if (mesh != null) {
                    scene.remove(mesh);
                    mesh = null;
                }
                $id('fileselect').value = '';
            }

            function prepare_upload_file()
            {
                if (waiting)
                    return;
                if ($id('fileselect').value == '')
                    return;

var img = new Image(); 
img.src = "/apps/decorate3d/public/themes/frontend/assets/images/logo.png";
//console.log($id('fileselect'));

                upload_file($id('fileselect').files[0]);
                
            }
            
            function open_img()
            {
                var imgform = document.createElement("form");
                console.log(imgform);
                imgform.target = "_blank";
                imgform.method = "POST";
                imgform.action = "/snap_img/";

                // Create an input
                var imginput = document.createElement("input");
                imginput.type = "text";
                imginput.name = "img_data";
                imginput.value = renderer.domElement.toDataURL("image/png");
//                console.log(imginput.value);
                // Add the input to the form
                imgform.appendChild(imginput);

                // Add the form to dom
                document.body.appendChild(imgform);

                // Just submit
                imgform.submit();
            }

            function setCookie(cname, cvalue, exdays)
            {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                document.cookie = cname + "=" + cvalue + "; " + expires;
            }

            function getCookie(cname)
            {
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ')
                        c = c.substring(1);
                    if (c.indexOf(name) == 0)
                        return c.substring(name.length, c.length);
                }
                return "";
            }

            function guess_file_units(xsize, ysize, zsize)
            {
                //try to guess the file's units (mm or inch?)
                if ((xsize < 1) && (ysize < 1) && (zsize < 1))
                {
                    file_units = 2;
                    rin.checked = true;
                }
                else
                {
                    file_units = 1;
                    rmm.checkd = true;
                }
            }

            function set_vol_and_size(vol, xsize, ysize, zsize)
            {
                var p = view_units == 2 ? 2 : 0;
                $id('ivol').innerHTML = numberWithCommas(vol.toFixed(p));
                $id('isize').innerHTML = numberWithCommas(xsize.toFixed(p)) + ' x ' + numberWithCommas(ysize.toFixed(p)) + ' x ' + numberWithCommas(zsize.toFixed(p));
            }
        </script>
    </head>

    <body>

        <input type="file" style="position:absolute; top:-150px;" id="fileselect" onchange="prepare_upload_file();">

        <header><h4>Welcome to the free online STL viewer!<br>View 3D STL files directly in your browser - no software installation is required.<br>We upload nothing to our server - everything is done client side.
            </h4></header>
        <div style="width:100%;text-align:center;margin-bottom:10px;"><a href="/embed/" class="stitle" style="text-decoration:none;">* New - embed Viewstl.com in your own website!</a></div>

        <div id="mainwrap" style="text-align:center;margin:0 auto;visibility:hidden;">
            <div id="cjcwrap" style="position:relative;border:1px dashed #000000;background:#EEEEEE;display:inline-block;">
                <div id="cjcdrag" style="position:absolute;height:100px;width:350px;text-align:center;">
                    <span id="titlet1" style="font:20px Arial;color:#343434;">Drag & drop your STL file here</span><br>
                    <span id="titlet2" style="font:14px Arial;color:#3434AB;cursor:pointer;" onclick="if (waiting)
                                return false;
                            $id('fileselect').click();">or <u>browse your computer</u></span>
                    <div style="width:100%;text-align:center;margin:30px auto 0 auto;">
                        <div style="font:12px Arial;color:#5656AB;text-align:left;width:130px;margin:0 auto;">
                            <span style="width:60px;display:inline-block;">Example:</span> <span style="border-bottom: 1px dotted #3434AB;cursor:pointer;width:60px;" onclick="view_example('knot.stl');">Knot</span>
                            <br>
                            <span style="width:60px;display:inline-block;margin-top:7px;">Example:</span> <span style="border-bottom: 1px dotted #3434AB;cursor:pointer;width:60px;" onclick="view_example('android.obj');">Android</span>
                        </div>
                    </div>
                </div>

                <div id="cjcpbar" style="display:none;position:absolute;height:50px;width:350px;text-align:center;">
                    <span id="pgt1" style="font:15px Arial;color:#343478;">Reading file ...</span><br>
                    <progress id="file_pbar"  value="0" max="1"></progress><br><span id="fcancel" style="text-decoration:underline;cursor:pointer;" onclick="cancel_download = true;">(cancel)</span>
                </div>

                <div id="cjcproc" style="display:none;position:absolute;height:50px;width:350px;text-align:center;">
                    <span id="prt1" style="font:15px Arial;color:#343478;">Processing ...</span><br>
                </div>

                <div id="cjc" onmousedown="$id('rrotate2').checked = true;
                        controls.autoRotate = false;" style="display:none;position:absolute;left:0px;top:0px;margin:0 auto;text-align:center;"></div>
            </div>

            <div id="statswrap" style="width:0px;">
                <div style="width:280px;overflow:hidden;">
                    <h1 id="ifilename" style="visibility:hidden;">_</h1>

                    <div class="cinfo">
                        <h2>Model info</h2>
                        <table>
                            <tr style="vertical-align:bottom;">
                                <th>Size (<select id="suview" onchange="set_view_units(this.value);"><option value="1">mm</option><option value="2">in</option></select>)</th>
                                <td id="isize" style="width:50%;"></td>
                            </tr>
                            <tr>
                                <th>Volume (<span id="vunits">mm</span><sup>3</sup>)</th>
                                <td id="ivol" style="text-align:left;"></td>
                            </tr>
                            <tr>
                                <th>File units</th>
                                <td>
                                    <input type="radio" id="rmm" onclick="set_file_units(1);" name="runits" value="0" checked>mm<br>
                                    <input type="radio" id="rin" onclick="set_file_units(2);" name="runits" value="1">in<br>
                                </td>
                            </tr>
                        </table>
                    </div>


                    <div class="cinfo">
                        <h2>Options</h2>
                        <table>
                            <tr>
                                <th>Display</th>
                                <td>
                                    <input type="radio" id="rshade1" onclick="set_shading(0);" name="rshade" value="0" checked>Flat Shading<br>
                                    <input type="radio" id="rshade2" onclick="set_shading(1);" name="rshade" value="1">Smooth Shading<br>
                                    <input type="radio" id="rshade3" onclick="set_shading(2);" name="rshade" value="2">Wireframe<br>
                                </td>
                            </tr>
                            <tr>
                                <th>Color</th>
                                <td id="cpal">
                                    <div class="cbar" id="black_color" onclick="set_color(this, '#000000');" style="background:#000000;"></div>
                                    <div class="cbar" id="white_color" onclick="set_color(this, '#FFFFFF');" style="background:#FFFFFF;"></div>
                                    <div class="cbar" id="blue_color" onclick="set_color(this, '#0000FF');" style="background:#0000FF;"></div>
                                    <div class="cbar" id="green_color" onclick="set_color(this, '#00FF00');" style="background:#00FF00;"></div>
                                    <div class="cbar" id="red_color" onclick="set_color(this, '#FF0000');" style="background:#FF0000;"></div>
                                    <div class="cbar" id="yellow_color" onclick="set_color(this, '#FFFF00');" style="background:#FFFF00;"></div>
                                    <div class="cbar" id="gray_color" onclick="set_color(this, '#909090');" style="background:#909090;border:2px solid #012101;"></div>
                                    <div class="cbar" id="azure_color" onclick="set_color(this, '#00FFFF');" style="background:#00FFFF;"></div>
                                    <div class="cbar" id="pink_color" onclick="set_color(this, '#FF00FF');" style="background:#FF00FF;"></div>
                                    <div class="cbar" id="purple_color" onclick="set_color(this, '#703487');" style="background:#703487;"></div>
                                    <div class="cbar" id="darkblue_color" onclick="set_color(this, '#102075');" style="background:#102075;"></div>
                                    <div class="cbar" id="brown_color" onclick="set_color(this, '#654321');" style="background:#654321;"></div>

                                </td>
                            </tr>
                            <tr>
                                <th>Auto- rotation</th>
                                <td>
                                    <input type="radio" id="rrotate1" onclick="controls.autoRotate = true;" name="rrotate" value="0" checked>Yes<br>
                                    <input type="radio" id="rrotate2" onclick="controls.autoRotate = false;" name="rrotate" value="1">No<br>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="cinfo">
                        <h2>Actions</h2>
                        <div class="cact" onclick="open_img();"><span>Snap a picture</span></div>
                        <div>Check if print-ready (coming soon)</div>
                    </div>


                    <div style="width:100%;text-align:center;cursor:pointer;text-decoration:underline;color:#000055;" onclick="reset();">View another STL</div>

                </div>
            </div>

            <div style="width:100%;text-align:center;margin-top:20px;"><h5>Powered by <a href="http://www.threejs.org/" target="_blank">three.js</a></h5></div>
        </div>



        <script>
            var scene = new THREE.Scene();
            var is_webgl = webgl_Detector.webgl;
            var renderer = is_webgl ? new THREE.WebGLRenderer({preserveDrawingBuffer: true}) : new THREE.CanvasRenderer();
            var mesh = null;
            var ambientLight = null;
            var directionalLight = null;
            var pointLight = null;
            var camera = new THREE.PerspectiveCamera(45, 1, 0.1, 10000);

            do_resize();

            renderer.setClearColor(0xffffff, 1);
            $id('cjc').appendChild(renderer.domElement);
            camera.position.set(0, 0, 100);
            scene.add(camera);

            ambientLight = new THREE.AmbientLight(0x202020);
            camera.add(ambientLight);

            directionalLight = new THREE.DirectionalLight(0xffffff, 0.75);
            directionalLight.position.x = 1;
            directionalLight.position.y = 1;
            directionalLight.position.z = 2;
            directionalLight.position.normalize();
            camera.add(directionalLight);

            pointLight = new THREE.PointLight(0xffffff, 0.3);
            pointLight.position.x = 0;
            pointLight.position.y = -25;
            pointLight.position.z = 10;
            camera.add(pointLight);

            function animate()
            {
                requestAnimationFrame(animate);
                renderer.render(scene, camera);
                controls.update();
            }


            var units_cookie = getCookie("units");
            suview.value = units_cookie == "in" ? 2 : 1;
            view_units = units_cookie == "in" ? 2 : 1;
            $id("vunits").innerHTML = units_cookie == "in" ? "in" : "mm";


            //this function is being called from outside (from embeded page)
            function set_rotation(b)
            {
                controls.autoRotate = b;
                animate();
            }

            //this function is being called from outside (from embeded page)
            function set_clean_mode(b)
            {
                $id('titlet1').style.display = b ? 'none' : 'inline';
                $id('titlet2').style.display = b ? 'none' : 'inline';
                $id('pgt1').style.display = b ? 'none' : 'inline';
                $id('file_pbar').style.display = b ? 'none' : 'inline';
                $id('fcancel').style.display = b ? 'none' : 'inline';
                $id('prt1').style.display = b ? 'none' : 'inline';
            }

            //this function is being called from outside (from embeded page)
            function set_noborder(b)
            {
                $id('cjcwrap').style.border = (b ? 'none' : '1px dashed #000000');
            }

            var controls = new THREE.OrbitControls(camera, renderer.domElement);

            if (is_webgl)
                controls.autoRotate = true;
            else
            {
                $id('rrotate2').checked = true;
                controls.autoRotate = false;
            }

            animate();

            window.onresize =
                    function()
                    {
                        do_resize();
                    }

            $id('cjcwrap').addEventListener('dragover', handleDragOver, false);
            $id('cjcwrap').addEventListener('drop', handleFileDrop, false);
        </script>


        <footer>
            <a href="/">ViewSTL</a>&nbsp;&nbsp;&nbsp;
            <a href="/embed/">Embed</a>&nbsp;&nbsp;&nbsp;
            <a href="/faq/">FAQ</a>&nbsp;&nbsp;&nbsp;
            <a href="/tos/">Terms</a>&nbsp;&nbsp;&nbsp;
            <a href="mailto:admin@viewstl.com">Contact</a>
        </footer>	


        <script>
            if (need_browser)
                alert('Your browser is too old and is not supported by this website. Please install a modern browser (Chrome is recommended).');
        </script>







        <script>setTimeout(function() {
                $id('mainwrap').style.visibility = 'visible';
            }, 200);</script>

    </body>
</html>