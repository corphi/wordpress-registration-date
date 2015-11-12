<?php

spl_autoload_register(
	/**
	 * Try to load a Corphi WordPress class.
	 * 
	 * @param string $name
	 * @return bool
	 */
	function ( $name ) {
		if ( substr( $name, 0, 17 ) !== 'Corphi\\WordPress\\' ) {
			return false;
		}

		$name = __DIR__ . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $name ) . '.php';

		return is_file( $name ) && include( $name );
	}
);
