<?php

function recursive_array_search($needle, $haystack)
{
    foreach ($haystack as $key => $value) {
        $current_key = $key;
        if ($needle === $value or (is_array($value) && recursive_array_search($needle, $value) !== false)) {
            return $current_key;
        }
    }
    return false;
}

function getdb()
{
    $return = array();
    $ini = dirname(__FILE__) . "/../conf/config.ini";
    $parse = parse_ini_file($ini, true);
    $driver = $parse["db_driver"];
    $return["user"] = $parse["db_user"];
    $return["password"] = $parse["db_password"];
    $return["options"] = $parse["db_options"];
    $return["attributes"] = $parse["db_attributes"];
    $return["dsn"] = "${driver}:";
    
    foreach ($parse["dsn"] as $k => $v) {
        $return["dsn"] .= "${k}=${v};";
    }
    
    $db = new PDO($return["dsn"], $return["user"], $return["password"], $return["options"]);
    
    foreach ($return["attributes"] as $k => $v) {
        $db->setAttribute(constant("PDO::{$k}"), constant("PDO::{$v}"));
    }
    
    return $db;
}

function alert($msg = "", $type = "danger")
{
    ob_start();
    require (dirname(__FILE__) . "/_header.php");
    
    echo '<div id="wrapper"><div id="page-wrapper"><div class="alert alert-' . $type . '" role="alert">' . $msg . '</div></div></div>';
    
    require (dirname(__FILE__) . "/_header.php");
    
    die(ob_get_clean());
}

function getparams($var = "")
{
    if (! file_exists(dirname(__FILE__) . "/../conf/questionnaire.php")) {
        
        alert("Fehler: Die Datei 'conf/questionnaire.php' wurde nicht gefunden.");
    }
    require (dirname(__FILE__) . "/../conf/questionnaire.php");
    
    $return = get_defined_vars();
    
    if (! empty($var)) {
        if (isset($return[$var])) {
            return $return[$var];
        }
    }
    
    return $return;
}

function query($query = "")
{
    if ($query) {
        
       
        
        global $db;
        
        if (empty($db)) {
            $db = getdb();
        }
        
        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            if (stristr($query, 'SELECT')) {
                $dump = $stmt->fetchAll();
            }
            $stmt->closeCursor();
        } catch (PDOException $e) {
            
            die("<h1>Datenbankfehler</h1>" . $e->getMessage() . " <br/><br/><b>SQL-Afrage:</b> $query <br>Hinweis: Die SQL-Abfrage wird nur Administratoren im Debug-Modus angezeigt oder wenn debug=1 gesetzt ist.");
        } catch (Exception $e) {
            die("<h1>Datenbankfehler</h1>" . $e->getMessage() . " <br/><br/><b>SQL-Afrage:</b> $query <br>Hinweis: Die SQL-Abfrage wird nur Administratoren im Debug-Modus angezeigt oder wenn debug=1 gesetzt ist.");
        }
        if (stristr($query, 'SELECT')) {
            return $dump;
        }
    }
}

function search_in_array($needle, $haystack)
{
    $return = array();
    if (is_array($haystack)) {

        foreach ($haystack as $key => $var) {



            if (stristr($key, $needle)) {

                if (is_array($var)) {
                    $tmp = search_in_array($needle, $var);

                    if ($tmp) {
                        $return = $return+$tmp;
                    }
                } else {
                    if ($var) {
                        $return[] = $var;
                    }
                }
            } else {

                if (is_array($var)) {
                    $tmp = search_in_array($needle, $var);

                    if ($tmp) {
                        $return = $return+$tmp;
                    }
                }
            }
        }
    }
    return $return;
}

function escapesql($string = "", $int = 0)
{
    global $db;
    
    if (empty($db)) {
        $db = getdb();
    }
    
    if ($string) {
        
        $string = $db->quote($string);
    }
    
    if (! strlen($string) && $int) {
        $string = 'NULL';
    } else 
        if (! strlen($string)) {
            $string = '0';
        }
    
    return $string;
}

function addpost($exept = "")
{
    $return = "";
    if (! is_array($exept)) {
        $exept = explode(",", $exept);
    }
    
    foreach ($_GET as $key => $var) {
        
        if (! in_array($key, $exept)) {
            
            if (is_array($var)) {
                
                foreach ($var as $part) {
                    $return .= '<input type="hidden" name="' . $key . '[]" value="' . $part . '">';
                }
            } else {
                
                $return .= '<input type="hidden" name="' . $key . '" value="' . $var . '">';
            }
        }
    }
    
    return $return;
}

function calltext($varname = "")
{
    if (isset($GLOBALS[$varname])) {
        return $GLOBALS[$varname];
    }
    
    if (defined($varname)) {
        return constant($varname);
    }
}

function qaccess($question_id)
{
    if (empty($_SESSION['views'])) {
        return false;
    }
    
    if (in_array($question_id, $GLOBALS["demoquestions"]) || in_array($_SESSION['views'], array(
        'god',
        'user'
    ))) {
        return true;
    }
    
    return false;
}

function getcountry($output = 'id')
{
    $country = array();
    
    global $country_access, $countryname;
    
    if (! empty($country_access[$_SESSION['multi']])) {
        
        $country = $country_access[$_SESSION['multi']];
        
        if (! empty($_REQUEST['c'])) {
            $selected_country = $_REQUEST['c'];
        } else {
            $selected_country = $_SESSION['country'];
        }
        
        if (! empty($selected_country)) {
            
            $country_new = array();
            
            if (! is_array($selected_country)) {
                $country_list = explode(",", $selected_country);
            } else {
                $country_list = $selected_country;
            }
            
            foreach ($country_list as $onec) {
                if (in_array($onec, $country_access[$_SESSION['multi']])) {
                    $country_new[] = $onec;
                }
            }
            
            if (! empty($country_new)) {
                $country = $country_new;
            }
        }
    }
    
    if (! is_array($country)) {
        $country = explode(",", $country);
    }
    
    if ($output == 'names') {
        
        $tmp = array();
        foreach ($country as $id) {
            $tmp[] = $countryname[$id];
        }
        
        return $tmp;
    }
    
    return $country;
}

function addurl($exept = "", $url = "")
{
    if (! is_array($exept)) {
        $exept = explode(",", $exept);
    }
    $addurl_array = array();
    
    foreach ($_REQUEST as $k => $v) {
        
        if (! in_array($k, $exept)) {
            $addurl_array[$k] = $v;
        }
    }
    
    $addurl = http_build_query($addurl_array);
    
    if ($url) {
        
        if (strstr($url, '?') && $addurl) {
            $con = '&';
        } else 
            if ($addurl) {
                $con = '?';
            } else {
                $con = '';
            }
        
        return $url . $con . $addurl;
    } else {
        return $addurl;
    }
}

function getdomain($domain = "", $part = "host")
{
    if (empty($domain)) {
        $domain = $_SERVER['HTTP_HOST'];
    }
    
    $domain = trim($domain);
    
    $tmp = parse_url($domain);
    
    if (! empty($tmp[$part])) {
        return $tmp[$part];
    }
}

