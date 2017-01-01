<?php

namespace Corphi\WordPress\Tests;

use Corphi\WordPress\RegistrationDatePlugin;



/**
 * @coversDefaultClass \Corphi\WordPress\RegistrationDatePlugin
 * 
 * @author Philipp Cordes <pc@irgendware.net>
 * @license GPL-2.0+
 */
class RegistrationDatePluginTest extends \WP_UnitTestCase
{
	/**
	 * @return RegistrationDatePlugin
	 */
	protected function getPluginMock()
	{
		return $this->getMockBuilder( RegistrationDatePlugin::class )
			->disableOriginalConstructor()
			->setMethods(null)
			->getMock()
		;
	}

	/**
	 * @covers ::__construct
	 */
	public function testConstructor()
	{
		$plugin = new RegistrationDatePlugin();

		$this->assertInstanceOf( RegistrationDatePlugin::class, $plugin );
		$this->assertNotFalse( has_filter( 'manage_users_columns',          [ $plugin, 'filterColumns' ] ) );
		$this->assertNotFalse( has_filter( 'manage_users_sortable_columns', [ $plugin, 'filterSortableColumns' ] ) );
		$this->assertNotFalse( has_filter( 'manage_users_custom_column',    [ $plugin, 'filterColumnOutput' ] ) );
	}

	/**
	 * @return array[][]
	 */
	public function columnData()
	{
		return [
			[ [] ],
			[ [ 'a' => 'a', 'b' => 'b' ] ],
		];
	}

	/**
	 * @covers ::filterColumns
	 * @dataProvider columnData
	 * @param string[] $columns
	 */
	public function testFilterColumns( array $columns )
	{
		$plugin = $this->getPluginMock();

		$countBefore = count( $columns );
		$filteredColumns = $plugin->filterColumns( $columns );

		$this->assertArrayHasKey( 'registration_date', $filteredColumns );
		$this->assertCount( $countBefore + 1, $filteredColumns );
	}

	/**
	 * @covers ::filterSortableColumns
	 * @dataProvider columnData
	 * @param string[] $columns
	 */
	public function testFilterSortableColumns( array $columns )
	{
		$plugin = $this->getPluginMock();

		$countBefore = count( $columns );
		$filteredColumns = $plugin->filterSortableColumns( $columns );

		$this->assertArrayHasKey( 'registration_date', $filteredColumns );
		$this->assertSame( 'registration_date', $filteredColumns['registration_date'] );
		$this->assertCount( $countBefore + 1, $filteredColumns );
	}

	/**
	 * @return array[]
	 */
	public function columnOutputData()
	{
		return [
			[ new \stdClass(), 'login' ],
			[ '', 'registration_date', '2015-06-17 15:59:51' ],
			[ '', 'registration_date', '2015-05-17 15:59:51' ],
			[ '', 'registration_date', '2014-06-17 15:59:51' ],
		];
	}

	/**
	 * @covers ::filterColumnOutput
	 * @dataProvider columnOutputData
	 * @param mixed  $output
	 * @param string $column
	 * @param string $time
	 */
	public function testFilterColumnOutput( $output, $column, $time = 'now' )
	{
		$plugin = $this->getPluginMock();

		$userId = new \stdClass();
		$params = [];

		/**
		 * @param \WP_User|false $user
		 * @param int            $id
		 * @return \WP_User
		 */
		$mockGetUserBy = function ( $user, $id ) use ( &$params, $time ) {
			$user = new \WP_User();
			$user->user_registered = $time;
			$params = func_get_args();

			return $user;
		};
		add_filter( 'mock_get_userdata', $mockGetUserBy, 10, 99 );

		$filteredOutput = $plugin->filterColumnOutput( $output, $column, $userId );

		if ( 'registration_date' === $column ) {
			$this->assertCount( 2, $params );
			$this->assertFalse( $params[0] );
			$this->assertSame( $userId, $params[1] );

			$timestamp = strtotime( $time );
			$format = get_option( 'date_format' );
			$this->assertContains( esc_attr( date_i18n( $format, $timestamp ) ), $filteredOutput );
			$this->assertContains( esc_html( human_time_diff( $timestamp ) ), $filteredOutput );
			$this->assertRegExp( '@^<abbr.*>.*</abbr>$@', $filteredOutput );
		} else {
			$this->assertEmpty( $params );

			$this->assertSame( $output, $filteredOutput );
		}
	}
}
