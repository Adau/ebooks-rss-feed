<?php

header("Content-Type: application/rss+xml; charset=UTF-8");

$url = 'https://1001ebooks.com';
$xml = simplexml_load_file($url . '/feed/', 'SimpleXMLElement', LIBXML_NOCDATA);

$transliterator = Transliterator::createFromRules(
    "[^[:L:][:N:].]+ > '-';"    // Remplacer tout ce qui n'est pas une lettre, un chiffre ou un point par un tiret
);

foreach ($xml->channel->item as $item) {
    $picture = sprintf(
        '%s/wp-content/uploads/%s/%s/%s.jpg',
        $url,
        date('Y', strtotime($item->pubDate)),
        date('m', strtotime($item->pubDate)),
        trim($transliterator->transliterate($item->title), '-')
    );

    $item->description = '<p><img src="' . $picture . '" alt="couverture"></p>';
    $item->description .= $item->children('content', true);

    // Supprimer la fin du contenu
    $item->description = trim(
        preg_replace('/<p.*Auteur(.*?(\n))+.*?/', '', $item->description)
    );
}

echo $xml->asXML();
