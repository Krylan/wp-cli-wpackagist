<?php
/**
 * Generates composer.json with list of active plugins and themes
 *
 * ## OPTIONS
 * --pluginsonly
 * : Check and list only active plugins.
 *
 * --themesonly
 * : Check and list only active themes.
 *
 * --overwrite
 * : Save composer.json file in root dir (it will overwrite file if exists)
 */
class Packager_Command {
	public function __construct(){
		WP_CLI::log( '📜 WPACKAGIST Composer Helper' );
	}

	private function plugin_exists($plugin_name){
		$args = array( 'slug' => $plugin_name );
		$request = array( 'action' => 'plugin_information', 'timeout' => 15, 'request' => $args );
		$query_string = http_build_query($request);
		$url = 'https://api.wordpress.org/plugins/info/1.2/?'.$query_string;
		$response = file_get_contents( $url );
		$plugin_info = json_decode( $response );
		if($plugin_info !== null){
			return true;
		}
		return false;
	}

	private function theme_exists($theme_name){
		$args = array( 'slug' => $theme_name );
		$request = array( 'action' => 'theme_information', 'timeout' => 15, 'request' => $args );
		$query_string = http_build_query($request);
		$url = 'https://api.wordpress.org/themes/info/1.1/?'.$query_string;
		$response = file_get_contents( $url );
		$theme_info = json_decode( $response );
		if($theme_info !== null){
			return true;
		}
		return false;
	}

	public function generate($command, $args) {
		
		$output = new stdClass();

		$options = array(
		  'return'     => true,   // Return 'STDOUT'; use 'all' for full object.
		  'parse'      => 'json', // Parse captured STDOUT to JSON array.
		  'launch'     => false,  // Reuse the current process.
		  'exit_error' => true,   // Halt script execution on error.
		);

		if(array_key_exists('themesonly', $args) && array_key_exists('pluginsonly', $args)){
			WP_CLI::error( "You used both --themesonly and --pluginsonly at the same time." );
			WP_CLI::log( "That doesn't make sense. Aborting." );
			return false;
		}

		if(!array_key_exists('themesonly', $args)){
			WP_CLI::log( "⌛ Checking for plugins..." );
			$plugins = WP_CLI::runcommand( 'plugin list --format=json --fields=name,version,status', $options );
			foreach($plugins as $index => $plugin){
				if($plugin['status'] != 'active'){
					WP_CLI::warning( "✖️ Plugin {$plugin['name']} is not active (is {$plugin['status']}). Omitted" );
					continue;
				}
				if($this->plugin_exists($plugin['name'])){
					$plugin_name = 'wpackagist-plugin/'.$plugin['name'];
					$plugin_ver = '>='.$plugin['version'];

					$output->$plugin_name = $plugin_ver;
					WP_CLI::success( "✔️ Plugin {$plugin['name']} ({$plugin['version']}) was added." );
				}else{
					WP_CLI::warning( "✖️ Plugin {$plugin['name']} was not found in WP repository. Omitted" );
				}
			}
		}

		if(!array_key_exists('pluginsonly', $args)){
			WP_CLI::log( "⌛ Checking for themes..." );
			$themes = WP_CLI::runcommand( 'theme list --format=json --fields=name,version,status', $options );
			foreach($themes as $index => $theme){
				if($theme['status'] != 'active'){
					WP_CLI::warning( "✖️ Theme {$theme['name']} is not active (is {$theme['status']}). Omitted" );
					continue;
				}
				if($this->theme_exists($theme['name'])){
					$theme_name = 'wpackagist-theme/'.$theme['name'];
					$theme_ver = '>='.$theme['version'];

					$output->$theme_name = $theme_ver;
					WP_CLI::success( "✔️ Theme {$theme['name']} ({$theme['version']}) was added." );
				}else{
					WP_CLI::warning( "✖️ Theme {$theme['name']} was not found in WP repository. Omitted" );
				}
			}
		}
		
		if(file_exists('composer.json')){
			$composer = file_get_contents('composer.json');
			$composer = json_decode($composer);
			foreach($composer->require as $key => $value){
				if(strpos($key, 'wpackagist-plugin/') === 0){
					unset($composer->require->$key);
				}
			}
		}else{
			WP_CLI::log( "❌ Composer.json file does not exist. Generating new one." );

			$composer = new stdClass();
			$composer->name = 'sample_name';
			$composer->description = '';
			$composer->{'minimum-stability'} = 'stable';
			$composer->repositories = array(
				(object) array(
					'type' => 'composer',
					'url' => 'https://wpackagist.org',
					'only' => array('wpackagist-plugin/*', 'wpackagist-theme/*')
				)
			);
			$composer->require = array();
		}

		$composer->require = (object) array_merge((array) $composer->require, (array) $output);
		$composer = json_encode($composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

		WP_CLI::log( '📜 Composer.json output:' );
		WP_CLI::log( $composer );

		if(array_key_exists('overwrite', $args)){
			file_put_contents('composer.json', $composer);

			WP_CLI::success( "✔️ Composer.json file successfully overwritten" );
		}else{
			WP_CLI::log( 'Use output above in your composer.json file' );
		}

		WP_CLI::success( "🆗 Job done!" );
	}
}

WP_CLI::add_command( 'wpackagist', 'Packager_Command' );