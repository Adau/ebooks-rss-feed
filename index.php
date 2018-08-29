<?php

header("Content-Type: application/rss+xml; charset=UTF-8");

$url = 'https://1001ebooks.com';
$xml = simplexml_load_file($url . '/feed/', 'SimpleXMLElement', LIBXML_NOCDATA);

foreach ($xml->channel->item as $item) {
    $picture = sprintf(
        '%s/wp-content/uploads/%s/%s/%s.jpg',
        $url,
        date('Y', strtotime($item->pubDate)),
        date('m', strtotime($item->pubDate)),
        preg_replace(array('/(– |’)|[^A-zÀ-ÿ0-9 \.-]/', '/ /'), array('', '-'), $item->title)
    );

    $item->description = '<p><img src="' . $picture . '" alt="couverture"></p>';
    $item->description .= $item->children('content', true);

    // Supprimer la fin du contenu
    $item->description = trim(
        preg_replace('/<p.*Auteur(.*?(\n))+.*?/', '', $item->description)
    );
}

echo $xml->asXML();
