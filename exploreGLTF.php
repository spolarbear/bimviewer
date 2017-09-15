<!DOCTYPE html>
<html lang="en">
	<head>
		<title>three.js webgl - gltf exporter</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>
			body {
				font-family: Monospace;
				background-color: #000;
				margin: 0px;
				overflow: hidden;
			}
			#info {
				color: #ccc;
				text-align: center;
				position: absolute;
				top: 0px; width: 100%;
				padding: 5px;
			}
		</style>
	</head>
	<body>
		<div id="info">
			GLTF Exporter<br/>
			<button id="export_scene">Export Scene1</button>
			<button id="export_scenes">Export Scene1 and Scene 2</button>
			<button id="export_object">Export Sphere</button>
			<button id="export_objects">Export Sphere and Grid</button>
			<button id="export_scene_object">Export Scene1 and Sphere</button>
		</div>

		<script src="../build/three.js"></script>

		<script src="js/Detector.js"></script>
		<script src="js/exporters/GLTFExporter.js"></script>

		<script>

			function exportGLTF( input ) {

				var gltfExporter = new THREE.GLTFExporter();

				gltfExporter.parse( input, function( result ) {

					var output = JSON.stringify( result, null, 2 );
					console.log( output );
					saveString( output, 'scene.gltf' );

				} );

			}

			document.getElementById( 'export_scene' ).addEventListener( 'click', function () {

				exportGLTF( scene1 );

			} );

			document.getElementById( 'export_scenes' ).addEventListener( 'click', function () {

				exportGLTF( [ scene1, scene2 ] );

			} );

			document.getElementById( 'export_object' ).addEventListener( 'click', function () {

				exportGLTF( sphere );

			} );

			document.getElementById( 'export_objects' ).addEventListener( 'click', function () {

				exportGLTF( [ sphere, gridHelper ] );

			} );

			document.getElementById( 'export_scene_object' ).addEventListener( 'click', function () {

				exportGLTF( [ scene1, gridHelper ] );

			} );


			var link = document.createElement( 'a' );
			link.style.display = 'none';
			document.body.appendChild( link ); // Firefox workaround, see #6594

			function save( blob, filename ) {

				link.href = URL.createObjectURL( blob );
				link.download = filename || 'data.json';
				link.click();

				// URL.revokeObjectURL( url ); breaks Firefox...

			}

			function saveString( text, filename ) {

				save( new Blob( [ text ], { type: 'text/plain' } ), filename );

			}

			if ( ! Detector.webgl ) Detector.addGetWebGLMessage();

			var container;

			var camera, scene1, scene2, renderer;
			var gridHelper, sphere;

			init();
			animate();

			function init() {

				container = document.createElement( 'div' );
				document.body.appendChild( container );

				scene1 = new THREE.Scene();
				scene1.name = 'Scene1';

				// ---------------------------------------------------------------------
				// Perspective Camera
				// ---------------------------------------------------------------------
				camera = new THREE.PerspectiveCamera( 45, window.innerWidth / window.innerHeight, 1, 2000 );
				camera.position.set(600, 400, 0);

				camera.name = "PerspectiveCamera";
				scene1.add( camera );

				// ---------------------------------------------------------------------
				// Ambient light
				// ---------------------------------------------------------------------
				scene1.add( new THREE.AmbientLight( 0xffffff, 0.2 ) );

				// ---------------------------------------------------------------------
				// DirectLight
				// ---------------------------------------------------------------------
				var light = new THREE.DirectionalLight( 0xffffff, 1 );
				light.position.set( 1, 1, 0 );
				scene1.add( light );

				// ---------------------------------------------------------------------
				// Grid
				// ---------------------------------------------------------------------
				gridHelper = new THREE.GridHelper( 2000, 20 );
				gridHelper.position.y = -50;
				gridHelper.name = "Grid";
				scene1.add( gridHelper );

				// ---------------------------------------------------------------------
				// Axis
				// ---------------------------------------------------------------------
				var axis = new THREE.AxisHelper(500);
				axis.name = "AxisHelper";
				scene1.add( axis );

				// ---------------------------------------------------------------------
				// Simple geometry with basic material
				// ---------------------------------------------------------------------
				// Icosahedron
				var mapGrid = new THREE.TextureLoader().load( 'textures/UV_Grid_Sm.jpg' );
				mapGrid.wrapS = mapGrid.wrapT = THREE.RepeatWrapping;
				var material = new THREE.MeshBasicMaterial( {
					color: 0xffffff,
					map: mapGrid
				} );

				object = new THREE.Mesh( new THREE.IcosahedronGeometry( 75, 0 ), material );
				object.position.set( -200, 0, 200 );
				object.name = 'Icosahedron';
				scene1.add( object );

				// Octahedron
				material = new THREE.MeshBasicMaterial( {
					color: 0x0000ff,
					wireframe: true
				} );
				object = new THREE.Mesh( new THREE.OctahedronGeometry( 75, 1 ), material );
				object.position.set( 0, 0, 200 );
				object.name = 'Octahedron';
				scene1.add( object );

				// Tetrahedron
				material = new THREE.MeshBasicMaterial( {
					color: 0xff0000,
					transparent: true,
					opacity: 0.5
				} );

				object = new THREE.Mesh( new THREE.TetrahedronGeometry( 75, 0 ), material );
				object.position.set( 200, 0, 200 );
				object.name = 'Tetrahedron';
				scene1.add( object );

				// ---------------------------------------------------------------------
				// Buffered geometry primitives
				// ---------------------------------------------------------------------
				// Sphere
				material = new THREE.MeshStandardMaterial( {
					color: 0xffff00,
					metalness: 0.5,
					roughness: 1.0,
					flatShading: true
				} );
				sphere = new THREE.Mesh( new THREE.SphereBufferGeometry( 70, 10, 10 ), material );
				sphere.position.set( 0, 0, 0 );
				sphere.name = "Sphere";
				scene1.add( sphere );

				// Cylinder
				material = new THREE.MeshStandardMaterial( {
					color: 0xff00ff,
					flatShading: true
				} );
				object = new THREE.Mesh( new THREE.CylinderBufferGeometry( 10, 80, 100 ), material );
				object.position.set( 200, 0, 0 );
				object.name = "Cylinder";
				scene1.add( object );

				// TorusKnot
				material = new THREE.MeshStandardMaterial( {
					color: 0xff0000,
					roughness: 1
				} );
				object = new THREE.Mesh( new THREE.TorusKnotGeometry( 50, 15, 40, 10 ), material );
				object.position.set( -200, 0, 0 );
				object.name = "Cylinder";
				scene1.add( object );


				// ---------------------------------------------------------------------
				// Hierarchy
				// ---------------------------------------------------------------------
				var mapWood = new THREE.TextureLoader().load( 'textures/hardwood2_diffuse.jpg' );
				material = new THREE.MeshStandardMaterial( { map: mapWood, side: THREE.DoubleSide } );

				object = new THREE.Mesh( new THREE.BoxBufferGeometry( 40, 100, 100 ), material );
				object.position.set( -200, 0, 400 );
				object.name = "Cube";
				scene1.add( object );

				object2 = new THREE.Mesh( new THREE.BoxBufferGeometry( 40, 40, 40, 2, 2, 2 ), material );
				object2.position.set( 0, 0, 50 );
				object2.rotation.set( 0, 45, 0 );
				object2.name = "SubCube";
				object.add( object2 );


				// ---------------------------------------------------------------------
				// Groups
				// ---------------------------------------------------------------------
				group1 = new THREE.Group();
				group1.name = "Group";
				scene1.add( group1 );

				group2 = new THREE.Group();
				group2.name = "subGroup";
				group2.position.set( 0, 50, 0);
				group1.add( group2 );

				object2 = new THREE.Mesh( new THREE.BoxBufferGeometry( 30, 30, 30 ), material );
				object2.name = "Cube in group";
				object2.position.set( 0, 0, 400 );
				group2.add( object2 );

				// ---------------------------------------------------------------------
				// Triangle Strip
				// ---------------------------------------------------------------------
				var geometry = new THREE.BufferGeometry();
				var positions = new Float32Array([
					0, 0, 0,
					0, 80, 0,
					80, 0, 0,
					80, 80, 0,
					80, 0, 80,
					80, 80, 80,
				]);

				var colors = new Float32Array([
					1, 0, 0,
					1, 0, 0,
					1, 1, 0,
					1, 1, 0,
					0, 0, 1,
					0, 0, 1,
				]);

				geometry.addAttribute( 'position', new THREE.BufferAttribute( positions, 3 ) );
				geometry.addAttribute( 'color', new THREE.BufferAttribute( colors, 3 ) );
				object = new THREE.Mesh( geometry, new THREE.MeshBasicMaterial( { side: THREE.DoubleSide, vertexColors: THREE.VertexColors } ) );
				object.position.set( 140, -40, -250);
				object.setDrawMode( THREE.TriangleStripDrawMode );
				object.name = 'Custom buffered';
				object.userData = { data: 'customdata', list: [ 1,2,3,4 ] };

				scene1.add( object );


				// ---------------------------------------------------------------------
				// Line Strip
				// ---------------------------------------------------------------------
				var geometry = new THREE.BufferGeometry();
				var numPoints = 100;
				var positions = new Float32Array( numPoints * 3 );

				for (var i = 0; i < numPoints; i++ ) {
					positions[ i * 3 ] = i;
					positions[ i * 3 + 1 ] = Math.sin( i / 2 ) * 20;
					positions[ i * 3 + 2 ] = 0;
				}

				geometry.addAttribute( 'position', new THREE.BufferAttribute( positions, 3 ) );
				object = new THREE.Line( geometry, new THREE.LineBasicMaterial( { color: 0xffff00 } ) );
				object.position.set(-50, 0, -200);
				scene1.add( object );


				// ---------------------------------------------------------------------
				// Line Loop
				// ---------------------------------------------------------------------
				var geometry = new THREE.BufferGeometry();
				var numPoints = 5;
				var radius = 70;
				var positions = new Float32Array( numPoints * 3 );

				for (var i = 0; i < numPoints; i++ ) {
					var s = i * Math.PI * 2 / numPoints;
					positions[ i * 3 ] = radius * Math.sin ( s );
					positions[ i * 3 + 1 ] = radius * Math.cos ( s );
					positions[ i * 3 + 2 ] = 0;
				}

				geometry.addAttribute( 'position', new THREE.BufferAttribute( positions, 3 ) );
				object = new THREE.LineLoop( geometry, new THREE.LineBasicMaterial( { color: 0xffff00 } ) );
				object.position.set(0, 0, -200);

				scene1.add( object );

				// ---------------------------------------------------------------------
				// Points
				// ---------------------------------------------------------------------
				var numPoints = 100;
				var pointsArray = new Float32Array( numPoints * 3 );
				for ( var i = 0; i < numPoints; i++ ) {
					pointsArray[ 3 * i ] = -50 + Math.random() * 100;
					pointsArray[ 3 * i + 1 ] = Math.random() * 100;
					pointsArray[ 3 * i + 2 ] = -50 + Math.random() * 100;
				}

				pointsGeo = new THREE.BufferGeometry();
				pointsGeo.addAttribute( 'position', new THREE.BufferAttribute( pointsArray, 3 ) );

				var pointsMaterial = new THREE.PointsMaterial( { color: 0xffff00, size: 5 } );
				var points = new THREE.Points( pointsGeo, pointsMaterial );
				points.name = "Points";
				points.position.set( -200, 0, -200);
				scene1.add( points );

				// ---------------------------------------------------------------------
				// Ortho camera
				// ---------------------------------------------------------------------
				var cameraOrtho = new THREE.OrthographicCamera( window.innerWidth / - 2, window.innerWidth / 2, window.innerHeight / 2, window.innerHeight / - 2, - 10, 10 );
				scene1.add( cameraOrtho );
				cameraOrtho.name = 'OrthographicCamera';

				material = new THREE.MeshLambertMaterial( {
					color: 0xffff00,
					side: THREE.DoubleSide
				} );

				object = new THREE.Mesh( new THREE.CircleGeometry( 50, 20, 0, Math.PI * 2 ), material );
				object.position.set( 200, 0, -400 );
				scene1.add( object );

				object = new THREE.Mesh( new THREE.RingGeometry( 10, 50, 20, 5, 0, Math.PI * 2 ), material );
				object.position.set( 0, 0, -400 );
				scene1.add( object );

				object = new THREE.Mesh( new THREE.CylinderGeometry( 25, 75, 100, 40, 5 ), material );
				object.position.set( -200, 0, -400 );
				scene1.add( object );

				//
				var points = [];

				for ( var i = 0; i < 50; i ++ ) {

					points.push( new THREE.Vector2( Math.sin( i * 0.2 ) * Math.sin( i * 0.1 ) * 15 + 50, ( i - 5 ) * 2 ) );

				}

				object = new THREE.Mesh( new THREE.LatheGeometry( points, 20 ), material );
				object.position.set( 200, 0, 400 );
				scene1.add( object );


				// ---------------------------------------------------------------------
				// 2nd Scene
				// ---------------------------------------------------------------------
				scene2 = new THREE.Scene();
				object = new THREE.Mesh( new THREE.BoxBufferGeometry( 100, 100, 100 ), material );
				object.position.set( 0, 0, 0 );
				object.name = "Cube2ndScene";
				scene2.name = 'Scene2';
				scene2.add(object);

				//

				renderer = new THREE.WebGLRenderer( { antialias: true } );
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( window.innerWidth, window.innerHeight );

				container.appendChild( renderer.domElement );

				//

				window.addEventListener( 'resize', onWindowResize, false );

			}

			function onWindowResize() {

				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();

				renderer.setSize( window.innerWidth, window.innerHeight );

			}

			//

			function animate() {

				requestAnimationFrame( animate );

				render();

			}

			function render() {

				var timer = Date.now() * 0.0001;

				camera.position.x = Math.cos( timer ) * 800;
				camera.position.z = Math.sin( timer ) * 800;

				camera.lookAt( scene1.position );
				renderer.render( scene1, camera );

			}

		</script>

	</body>
</html>
