<?php

spl_autoload_register(
	/**
	 * Try to load a Corphi WordPress test class.
	 * 
	 * @param string $name
	 * @return bool
	 */
	function ( $name ) {
		if ( substr( $name, 0, 23 ) !== 'Corphi\\WordPress\\Tests\\' ) {
			return false;
		}

		$name = __DIR__ . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $name ) . '.php';

		return is_file( $name ) && include( $name );
	}
);
