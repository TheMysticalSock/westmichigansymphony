<?php

function wp_upload_dir( $time = null ) {
	$siteurl = get_option( 'siteurl' );
	//echo "help".$siteurl;
	$upload_path = trim( get_option( 'upload_path' ) );

	if ( empty( $upload_path ) || 'wp-content/uploads' == $upload_path ) {
		$dir = WP_CONTENT_DIR . '/uploads';
	} elseif ( 0 !== strpos( $upload_path, ABSPATH ) ) {
		// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
		$dir = path_join( ABSPATH, $upload_path );
	} else {
		$dir = $upload_path;
	}

	if ( !$url = get_option( 'upload_url_path' ) ) {
		if ( empty($upload_path) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) )
			$url = WP_CONTENT_URL . '/uploads';
		else
			$url = trailingslashit( $siteurl ) . $upload_path;
	}

	// Obey the value of UPLOADS. This happens as long as ms-files rewriting is disabled.
	// We also sometimes obey UPLOADS when rewriting is enabled -- see the next block.
	if ( defined( 'UPLOADS' ) && ! ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) ) {
		$dir = ABSPATH . UPLOADS;
		$url = trailingslashit( $siteurl ) . UPLOADS;
	}

	// If multisite (and if not the main site in a post-MU network)
	if ( is_multisite() && ! ( is_main_site() && defined( 'MULTISITE' ) ) ) {

		if ( ! get_site_option( 'ms_files_rewriting' ) ) {
			// If ms-files rewriting is disabled (networks created post-3.5), it is fairly straightforward:
			// Append sites/%d if we're not on the main site (for post-MU networks). (The extra directory
			// prevents a four-digit ID from conflicting with a year-based directory for the main site.
			// But if a MU-era network has disabled ms-files rewriting manually, they don't need the extra
			// directory, as they never had wp-content/uploads for the main site.)

			if ( defined( 'MULTISITE' ) )
				$ms_dir = '/sites/' . get_current_blog_id();
			else
				$ms_dir = '/' . get_current_blog_id();

			$dir .= $ms_dir;
			$url .= $ms_dir;

		} elseif ( defined( 'UPLOADS' ) && ! ms_is_switched() ) {
			// Handle the old-form ms-files.php rewriting if the network still has that enabled.
			// When ms-files rewriting is enabled, then we only listen to UPLOADS when:
			//   1) we are not on the main site in a post-MU network,
			//      as wp-content/uploads is used there, and
			//   2) we are not switched, as ms_upload_constants() hardcodes
			//      these constants to reflect the original blog ID.
			//
			// Rather than UPLOADS, we actually use BLOGUPLOADDIR if it is set, as it is absolute.
			// (And it will be set, see ms_upload_constants().) Otherwise, UPLOADS can be used, as
			// as it is relative to ABSPATH. For the final piece: when UPLOADS is used with ms-files
			// rewriting in multisite, the resulting URL is /files. (#WP22702 for background.)

			if ( defined( 'BLOGUPLOADDIR' ) )
				$dir = untrailingslashit( BLOGUPLOADDIR );
			else
				$dir = ABSPATH . UPLOADS;
			$url = trailingslashit( $siteurl ) . 'files';
		}
	}

	$basedir = $dir;
	$baseurl = $url;

	$subdir = '';
	if ( get_option( 'uploads_use_yearmonth_folders' ) ) {
		// Generate the yearly and monthly dirs
		if ( !$time )
			$time = current_time( 'mysql' );
		$y = substr( $time, 0, 4 );
		$m = substr( $time, 5, 2 );
		$subdir = "/$y/$m";
	}

	$dir .= $subdir;
	$url .= $subdir;

	$uploads = apply_filters( 'upload_dir',
		array(
			'path'    => $dir,
			'url'     => $url,
			'subdir'  => $subdir,
			'basedir' => $basedir,
			'baseurl' => $baseurl,
			'error'   => false,
		) );

	// Make sure we have an uploads dir
	if ( ! wp_mkdir_p( $uploads['path'] ) ) {
		if ( 0 === strpos( $uploads['basedir'], ABSPATH ) )
			$error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
		else
			$error_path = basename( $uploads['basedir'] ) . $uploads['subdir'];

		$message = sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $error_path );
		$uploads['error'] = $message;
	}

	return $uploads;
}

?>