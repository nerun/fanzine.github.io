<?php
function addTabsOutsidePre($body, $tabs) {
    // Temporarily remove content inside <pre> tags by replacing it with a placeholder
    $body = preg_replace_callback('/<pre(.*?)<\/pre>/s', function ($matches) {
        // Store the content of <pre> and replace it with a placeholder
        return "<pre" . base64_encode($matches[1]) . "</pre>";
    }, $body);

    // Add tabs to the content outside <pre> tags
    $body = preg_replace('/^/m', str_repeat("\t", $tabs), $body);

    // Restore content inside <pre> tags by decoding the base64 encoded content
    $body = preg_replace_callback('/<pre(.*?)<\/pre>/s', function ($matches) {
        // Decode the base64 encoded content and restore it inside <pre> tags
        return "<pre" . base64_decode($matches[1]) . "</pre>";
    }, $body);

    return $body;
}

$body = file_get_contents($page_file);

if ( !empty($body) ) {
    $parameters = _getParams($body, $page_file);
    $article = $parameters[0];
    $author  = $parameters[1];
    $columns = $parameters[2];
    $date    = $parameters[3];
    $email   = $parameters[4];
    $image   = $parameters[5];
    
    // If email is not missing, link it to the author
    if ( !empty($email) ){
        $author = '<a href="mailto:'.$email.'">'.$author.'</a>';
    }

    // If featured image is not missing and it is not set to 'none', insert it
    if ( !empty($image) && mb_strtolower($image) != "none" ){
        echo tab(3) . '<img src="/img/' . $image . '" width="640"' .
            ' height="360" style="margin: 1px auto 1px; display: block;"' .
            ' class="responsive-img">' . "\n";
        echo tab(3) . '<hr width="75%">' . "\n";
    }

    // Insert title, author and publication date in the page, using Unicode characters as icons.
    echo tab(3) . '<h1 style="margin-bottom:0; text-align:center;">' . $article . "</h1>\n";
    echo tab(3) . '<p style="margin-top:0; font-size:80%; text-align:center;">' . '&#128197; ' .
        $date . '&emsp;&#128100; ' . $author . "</p>\n";
    echo tab(3) . "<br />\n";
    echo tab(3) . '<div id="columns" class="columns" style="column-count:' . $columns . ';">' . "\n";
    
    if ( mb_strtolower(substr($page_file, -2)) == 'md' ) {
        $body = $Parsedown->text($body) . "\n";
    }
    
    $body = addTabsOutsidePre($body, 4);
    
    echo $body;
    echo tab(3) . "</div>\n";
} else {
    include('404.php');
}
?>
