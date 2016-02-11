<?php
session_start();
require 'autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

define('CONSUMER_KEY', ''); // add your app consumer key between single quotes
define('CONSUMER_SECRET', ''); // add your app consumer secret key between single quotes
define('OAUTH_CALLBACK', 'https://sohaibilyas.com/twapp/callback.php'); // your app callback URL

if (!isset($_SESSION['access_token'])) {
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
	$_SESSION['oauth_token'] = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
	echo $url;
} else {
	$access_token = $_SESSION['access_token'];
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	
	// getting basic user info
	$user = $connection->get("account/verify_credentials");
	
	// printing username on screen
	echo "Welcome " . $user->screen_name . '<br>';

	// getting recent tweeets by user 'snowden' on twitter
	$tweets = $connection->get('statuses/user_timeline', ['count' => 200, 'exclude_replies' => true, 'screen_name' => 'snowden', 'include_rts' => false]);
	$totalTweets[] = $tweets;
	$page = 0;

	for ($count = 200; $count < 500; $count += 200) { 
		$max = count($totalTweets[$page]) - 1;
		$tweets = $connection->get('statuses/user_timeline', ['count' => 200, 'exclude_replies' => true, 'max_id' => $totalTweets[$page][$max]->id_str, 'screen_name' => 'snowden', 'include_rts' => false]);
		$totalTweets[] = $tweets;
		$page += 1;
	}

	// printing recent tweets on screen
	$start = 1;
	foreach ($totalTweets as $page) {
		foreach ($page as $key) {
			echo $start . ':' . $key->text . '<br>';
			$start++;
		}
	}
}
