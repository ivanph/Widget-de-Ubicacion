<?php
/*
Plugin Name: Ubicacion
Description: Un widget para indicar la ubicación de la dependencia.
Version: 0.1
Author: Ivan Pintado

*/




class Ubicacion extends WP_Widget {
	
	

	/**
	 * Constructor del widget, indica el classname, la descripción
	 * y carga los estilos y scripts 
	 */
	function Ubicacion() {
		
		$widget_opts = array(
			'classname' => 'ubicacion-widget',
			'description' => 'Un widget para indicar la ubicación de la dependencia..',
		);
		$this->WP_Widget('ubicacion-widget', 'Widget de Ubicación', $widget_opts);
		
		if(is_admin()) {
		
			$admin_style_url = WP_PLUGIN_URL . '/ubicacion/css/ubicacion.css';
			$admin_style_file = WP_PLUGIN_DIR . '/ubicacion/css/ubicacion.css';
			if(file_exists($admin_style_file)) {
				wp_register_style('ubicacion-styles', $admin_style_url);
				wp_enqueue_style('ubicacion-styles');
			}
			
			$admin_script_url = WP_PLUGIN_URL . '/ubicacion/javascript/ubicacion.js';
			$admin_script_file = WP_PLUGIN_DIR . '/ubicacion/javascript/ubicacion.js';
			if(file_exists($admin_script_file)) {
				wp_register_script('ubicacion-script', $admin_script_url);
				wp_enqueue_script('ubicacion-script');
			}
			
		} else {
				wp_enqueue_script('jquery');
				$widget_style_file = WP_PLUGIN_DIR . '/ubicacion/css/ubicacion-display-widget.css';
				$widget_style_url = WP_PLUGIN_URL . '/ubicacion/css/ubicacion-display-widget.css';
				$mapa_style_file = WP_PLUGIN_DIR . '/ubicacion/css/ubicacion-display-mapa.css';
				$mapa_style_url = WP_PLUGIN_URL . '/ubicacion/css/ubicacion-display-mapa.css';
				if(file_exists($widget_style_file)) {
					wp_register_style('widget-ubicacion-display-styles', $widget_style_url);
					wp_enqueue_style('widget-ubicacion-display-styles');
				if(file_exists($mapa_style_file)){
						wp_register_style('mapa-ubicacion-display-style',$mapa_style_url);
						wp_enqueue_style('mapa-ubicacion-display-style');
						
						
				    
				}
				
				
			}
		
		}
		
	} // end constructor
	
	/**
	 * Despliega el widget en la página.
	 * * 
	 *
	 * @args			Un array de elementos
	 * @instance
	 */
	function widget($args, $instance) {
		//Verificamos lo que el usuario eligió para indicar la ubicación(dirección o coordenadas)
     
	$lat = get_option('latitud');
	$lng = get_option('longitud');
	$ruta = "http://maps.googleapis.com/maps/api/staticmap?center=".$lat.",".$lng."&zoom=16&size=250x250&maptype=roadmap&markers=color:green%7C".$lat.",".$lng."&sensor=false";

    ?>
    
    <!--
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type='text/javascript'>
	
	//Función para crear el mapa
		
		function makeMap() {
			//var latlng = new google.maps.LatLng(19.541384,-96.927052)
			var latlng = new google.maps.LatLng(<?php echo $lat;?>,<?php echo $long;?>)
		
			var myOptions = {
				zoom: 17,
				center: latlng,
				mapTypeControl: true,
				mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
				navigationControl: true,
				navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById('map_widget'), myOptions);
				
			var marker = new google.maps.Marker({
				position: latlng,
				map: map,
				title: ''
			});
			
			
		}
		window.onload = makeMap;
	
	
	
	</script>
	-->
	<h3 class="widget-title">Ubicación</h3>
	<img id='map_widget' src="<?php echo $ruta;?>"></img>
	<a href="como-llegar">Como Llegar</a>
	<?php
	
		
	} // end widget
	
	/**
	 * Procesa las opciones del widget para guardarlas en la BD.
	 *
	 * @new_instance	The previous instance of values before the update.
	 * @old_instance	The new instance of values to be generated via the update.
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['indicar_ubicacion'] = strip_tags(stripslashes($new_instance['indicar_ubicacion']));
		$instance['ubicacion_mensaje'] = strip_tags(stripslashes($new_instance['ubicacion_mensaje']));
		$instance['indicar_coordenadas'] = strip_tags(stripslashes($new_instance['indicar_coordenadas']));
		$instance['coord_latitude'] = strip_tags(stripslashes($new_instance['coord_latitude']));
		$instance['coord_longitude'] = strip_tags(stripslashes($new_instance['coord_longitude']));
		
		if($instance['indicar_ubicacion']){
	 	//Obtenemos las coordenas de la ubicación indicada
	 	$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.urlencode($instance['ubicacion_mensaje']).'&sensor=false');
		$output= json_decode($geocode);
		$lat = $output->results[0]->geometry->location->lat;
		$lng = $output->results[0]->geometry->location->lng;
	 	}else{
	 	//Si no verificamos que haya seleccionado indicar las coordenadas y tomamos esas.
	 	if($instance['indicar_coordenadas']){
	 		$lat = $instance['coord_latitude'];
			$lng = $instance['coord_longitude'];
	 	}
	 }
	 update_option('latitud',$lat);
	 update_option('longitud',$lng);
	
	 updatePageContent();
		
		return $instance;
		
	} // end widget
	
	/**
	 * Genera la parte administrativa del widget
	 *
	 * @instance	The array of keys and values for the widget.
	 */
	function form($instance) {
		
		$instance = wp_parse_args(
			(array)$instance,
			array(
				'indicar_ubicacion' => '',
				'ubicacion_mensaje' => '',
				'indicar_coordenadas'=>'',
				'coord_latitude'=>'',
				'coord_longitude'=>''
				
			)
		);
		
		
		$indicar_ubicacion = strip_tags(stripslashes($instance['indicar_ubicacion']));
		$ubicacion_mensaje = strip_tags(stripslashes($instance['ubicacion_mensaje']));
		$indicar_coordenadas = strip_tags(stripslashes($instance['indicar_coordenadas']));
		$coord_latitude = strip_tags(stripslashes($instance['coord_latitude']));
		$coord_longitude = strip_tags(stripslashes($instance['coord_longitude']));

		
?>
	<p class="info">
		Para indicar la ubicación, puede escribir el nombre de la dependecia (deberá estar registrada en Google Maps) o proporcionar las coordenadas.
	</p>
	<p class="info">
		
	</p>
	<div class="ubicacion-options">
		<input type="checkbox" 
			   name="<?php echo $this->get_field_name('indicar_ubicacion'); ?>" 
			   id="<?php echo $this->get_field_id('indicar_ubicacion'); ?>" 
			   <?php if($instance['indicar_ubicacion'] == 'on') { echo 'checked="checked"'; } ?> />
		<label for="<?php echo $this->get_field_id('indicar_ubicacion'); ?>">
			Indicar el nombre de la dependecia
		</label>
		<input type="text" name="<?php echo $this->get_field_name('ubicacion_mensaje'); ?>" id="<?php echo $this->get_field_id('ubicacion_mensaje'); ?>" value="<?php echo $instance['ubicacion_mensaje']; ?>" />
	</div>
	
	<div class="ubicacion-options">
		<input type="checkbox" 
			   name="<?php echo $this->get_field_name('indicar_coordenadas'); ?>" 
			   id="<?php echo $this->get_field_id('indicar_coordenadas'); ?>" 
			   <?php if($instance['indicar_coordenadas'] == 'on') { echo 'checked="checked"'; } ?> />
		<label for="<?php echo $this->get_field_id('indicar_coordenadas'); ?>">
			Indicar las coordenadas
		</label>
		<br/>
		<label class="coord_label" for="<?php echo $this->get_field_id('coord_latitude');?> "> Latitud </label>
		<input type="text" name="<?php echo $this->get_field_name('coord_latitude'); ?>" id="<?php echo $this->get_field_id('coord_latitude'); ?>" value="<?php echo $instance['coord_latitude']; ?>" />
		<label class="coord_label" for="<?php echo $this->get_field_id('coord_longitude');?> "> Longitud </label>
		<input type="text" name="<?php echo $this->get_field_name('coord_longitude'); ?>" id="<?php echo $this->get_field_id('coord_longitude'); ?>" value="<?php echo $instance['coord_longitude']; ?>" />
	</div>

<?php
	} // end form

	
} // end class


	
	function createPageContent()
	{
		
		$lat = get_option('latitud');
		$lng = get_option('longitud');
		
		$script = WP_PLUGIN_URL . '/ubicacion/javascript/funciones-ubicacion-widget.js';
		$contenido ='<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
					</script><script type="text/javascript" src="http://www.google.com/jsapi?">
					</script><script type="text/javascript" src="'.$script.'"></script>
					<script type="text/javascript">
						window.onload= function(){setCoordsDestination("'.$lat.'","'.$lng.'");}
					</script>
					<div id="mapa"></div>
					Tipo de ruta: 
					<select id="route_type">
					       <option value="driving">Manejando</option>
					       <option value="walking">Caminando</option>
					</select>
					Distancia aproximada: <span id="distance"></span>
					Tiempo aproximado: <span id="time"></span>';
					
		return $contenido;
	}



	function onActivado()
	{
	$titulo = 'Como Llegar';

	
	$pagina = get_page_by_title($titulo);
	$pagina_id = $pagina->ID;
	//$ubicacion = new Ubicacion();

	$page_content = createPageContent();
		// Create post object
  $my_post = array( 
  	 'comment_status' => 'closed',
     'post_title' => $titulo,
     'post_content' => $page_content,
     'post_status' => 'publish',
     'post_author' => 1,
     'post_type' => 'page'
  );
  if (!isset($pagina_id)){
  	wp_insert_post( $my_post );
  }
	
	}

//Al desactivar el plugin se elimina la página
	function onDesActivado(){
	
		$pagina = get_page_by_title('Como Llegar');
		$pagina_id = $pagina->ID;
		if (isset($pagina_id)){
			wp_delete_post($pagina_id,true);
		}
	}
	
function updatePageContent(){
	$pagina = get_page_by_title('Como Llegar');
	$updated_post = array();
	$updated_post['ID']= $pagina->ID;
	$updated_post['post_content']=createPageContent();
	wp_update_post($updated_post);
}

register_activation_hook( __FILE__,'onActivado');
register_deactivation_hook(__FILE__,'onDesActivado');
add_action('widgets_init', create_function('', 'register_widget("Ubicacion");'));
?>