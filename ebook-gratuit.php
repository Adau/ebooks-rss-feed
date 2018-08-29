<?php

header("Content-Type: application/rss+xml; charset=UTF-8");

$url = 'https://www.ebook-gratuit.co';
$xml = simplexml_load_file($url . '/feed/', 'SimpleXMLElement', LIBXML_NOCDATA);

$transliterator = Transliterator::createFromRules(
    // Supprimer les accents et remplace tout ce qui n'est pas une lettre, un chiffre ou un point par un tiret
    "::Latin-ASCII; [^[:L:][:N:].]+ > '-';"
);

foreach ($xml->channel->item as $item) {
    $picture = sprintf(
        '%s/wp-content/uploads/%s/%s/%s.jpg',
        $url,
        date('Y', strtotime($item->pubDate)),
        date('m', strtotime($item->pubDate)),
        trim(
            // Supprimer la date à la fin du titre
            preg_replace('/\d{4}-$/', '', $transliterator->transliterate($item->title)),
            '-'
        )
    );

    $item->description = '<p><img src="' . $picture . '" alt="couverture"></p>' . $item->description;

    // Supprimer la fin du contenu
    $item->description = trim(
        preg_replace('/<p>L’article(.*?(\n))+.*?/', '', $item->description)
    );
}

echo $xml->asXML();
