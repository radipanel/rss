<?php
	// Set the content type to application/rss+xml
	header( 'Content-type: application/rss+xml' );

	// Include the required glob.php file
	require_once( "../_inc/glob.php" );
	
	// We also gather up the category from the URL
	$catid = $core->clean( $_GET['cat'] );
	
	// We'll also allow them to limit the number of items from a feed
	$limit = $core->clean( $_GET['limit'] );
	
	// Now for a little logic
	if ( !$catid ) {
	
		// They didn't specify a category to get, so we just fetch everything (up to the limit)
		if ( !$limit ) {
		
				// Didn't specify a limit, so we get everything
				$data = $db->query( "SELECT * FROM `news`" );
		}
		else {
		
				// Otherwise, we have to use a limit statement
				$data = $db->query( "SELECT * FROM `news` LIMIT {$limit}" );
		}
	}
	else {
		
		// They specified a category to get, so we just fetch everything (up to the limit)
		if ( !$limit ) {
		
				// Didn't specify a limit, so we get everything
				$data = $db->query( "SELECT * FROM `news` WHERE `category`={$catid}" );
		}
		else {
		
				// Otherwise, we have to use a limit statement
				$data = $db->query( "SELECT * FROM `news` WHERE `category`={$catid} LIMIT {$limit}" );
		}
		
	}

// Now we identify the location of the RSS feed file
$rssloc = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
// And we modify it to provide the base
$artloc = str_replace( "rss.php", "news", $rssloc);

echo "<?xml version=\"1.0\"?>";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title>radiPanel - News Articles</title>  
<description>The latest stories from our radiPanel installation!</description>
<link><?php echo $rssloc; ?></link>
<atom:link href="<?php echo $rssloc; ?>" rel="self" type="application/rss+xml" />
<ttl>15</ttl> 

<?php
while( $articles = $db->assoc( $data ) ) {

	// Now to return our articles
	echo "<item>\r\n";
	echo "<guid>" . $artloc . "/" . $articles['id'] . "</guid>\r\n";
	echo "<title>" . $articles['title'] . "</title>\r\n";
	echo "<link>" . $artloc . "/" . $articles['id'] . "</link>\r\n";
	echo "<description>" . $articles['desc'] . "</description>\r\n";
	echo "<pubDate>" . date(DATE_RSS, $articles['stamp']) . "</pubDate>\r\n";
	echo "</item>\r\n";

	// And increment 1
	$i++;
}  
?>
</channel>
</rss>