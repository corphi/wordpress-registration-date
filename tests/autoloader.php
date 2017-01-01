<?php

spl_autoload_register(
	/**
	 * Try to load a Corphi WordPress test class.
	 * 
	 * @param string $name
	 *
	 * @return bool
	 */
	function ( $name ) {
		static $prefix = 'Corphi\\WordPress\\Tests\\';
		if ( 0 !== strpos( $name, $prefix ) ) {
			return false;
		}

		$name = substr( $name, strlen( $prefix ) - 1 );
		$name = __DIR__.str_replace( '\\', DIRECTORY_SEPARATOR, $name ).'.php';

		return is_file( $name ) && include( $name );
	}
);
