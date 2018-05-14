<?php
// Security notes: https://stackoverflow.com/questions/110575/do-htmlspecialchars-and-mysql-real-escape-string-keep-my-php-code-safe-from-inje
// Do not need to use mysqli_escape_chars where using prepared statements

// Use htmlspecialchars to protect against XSS attacks when you insert the data into an HTML document.
// Databases aren't HTML documents.
// (You might later take the data out of the database to put it into an HTML document, that is the time to use htmlspecialchars).

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateUrl($website){
    if (!preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i',$website)) {
        $websiteErr = "Invalid URL.";
    }
}

function validateEmail($email){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format.";
    }
}

function validateName($name){
    if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
        $nameErr = "Only letters and white space allowed.";
    }
}

function validateDate($date){

}

function validateTime($date){

}

function lastItem(array &$array){
    return array_values(array_slice($array, -1))[0];
}

/**
 * Returns only the text for the tag elements (no attributes, tag names, etc.).
 * This includes returning child element text.
 * @param int $i Index for the starting character of the tag.
 * @param string $content Content to search.
 * @return string The text for the tag elements (no attributes, tag names, etc.).
 * This includes returning child element text.
 */
function getTagContentsOnly($i, $content){
    // Exit if the index is not at the start of a tag
    if ($content[$i] !== '<') return '';

    $contentLength = strlen($content);
    $tagBalance = 1;
    $isInTag = false;
    $stringToCheck = '/';
    $stringToCheckLength = strlen($stringToCheck);
    $headerContent = '';
    do {
        $currentMaxJ = $i + $stringToCheckLength;

        if ($content[$i] === '<' &&
            $currentMaxJ < $contentLength &&
            $content[$currentMaxJ] !== $stringToCheck){
            // Entering opening tag
            $tagBalance++;
            $isInTag = true;
        } elseif ($content[$i] === '<' &&
            $currentMaxJ < $contentLength &&
            $content[$currentMaxJ] === $stringToCheck){
            // Entering closing tag
            $tagBalance--;
            $isInTag = true;
        } elseif ($content[$i] === '>'){
            // Leaving tag
            $isInTag = false;
        }

        // Records all text that is not within a tag
        if (!$isInTag && $tagBalance === 1 && $content[$i] !== '>'){
            $headerContent .= $content[$i];
        }
        $i++;
    } while (!($tagBalance === 0 && !$isInTag) && $i < $contentLength);
    return $headerContent;
}

