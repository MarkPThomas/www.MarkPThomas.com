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

