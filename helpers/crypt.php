<?php
/*
 * crypt.php
 *
 * authors : Berfy
 *
 * Contains functions that crypts the password of a user such as generateSalt
 */


/**
 * Filters the given string
 * @param $name the string to filter
 * @param $requis precises if $name is required or not
 * @return the string if successfull, an exception otherwise
 */
function inputFilterString($name, $requis=true)
{
    $v = filter_input(INPUT_POST, $name, FILTER_SANITIZE_STRING);
    if ($requis && $v == null) {
        throw new Exception("argument $name est requis");
    }
    return $v;
}


/**
 * Generates a random string
 * @param $length the length of the random string (22 by default)
 * @return the random string
 */
function generateRandomString($length = 22)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


/**
 * Generates the salt
 * @return the salt
 */
function generateSalt()
{
    return '$2a$10$' . generateRandomString();
}
?>
