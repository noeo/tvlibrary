<?php

function loadf($functionname)
{
    $returnstr = "";
    
    $tmp = explode('.', $functionname);
    
    if (! empty($tmp[0]))
        $addondir = $tmp[0];
    else
        $addondir = '-1';
    
    if (! empty($tmp[1]))
        $addonfunct = $tmp[1];
    else
        $addonfunct = '-1';
    
    if (is_callable($functionname)) {
        
        $init_yes = 1;
    } else 
        if (file_exists(FUNCTPATH . "$addondir/$addonfunct.php")) {
            require_once FUNCTPATH . "$addondir/$addonfunct.php";
            
            $functionname = $addonfunct;
            
            $init_yes = 1;
        } else 
            if (@file_exists(FUNCTPATH . "$functionname.php")) {
                include_once FUNCTPATH . "$functionname.php";
                
                $init_yes = 1;
            }
    
    if (! empty($init_yes)) {
        
        $numargs = func_num_args();
        $arg_list = func_get_args();
        $tmp_array = array();
        if ($numargs > 1) {
            
            for ($i = 1; $i < $numargs; $i ++) {
                
                $tmp_array[] = '$arg_list[' . $i . ']';
            }
        }
        
        if (is_callable($addondir . $addonfunct)) {
            if (count($tmp_array))
                eval('$returnstr= call_user_func("' . $addondir . $addonfunct . '",' . implode(",", $tmp_array) . ');');
            else
                $returnstr = call_user_func($addondir . $addonfunct);
        } elseif (is_callable("public" . $functionname)) {
            if (count($tmp_array))
                eval('$returnstr= call_user_func("' . "public" . $functionname . '",' . implode(",", $tmp_array) . ');');
            else
                $returnstr = call_user_func("public" . $functionname);
        } elseif (is_callable($functionname)) {
            if (count($tmp_array))
                eval('$returnstr= call_user_func("' . $functionname . '",' . implode(",", $tmp_array) . ');');
            else
                $returnstr = call_user_func($functionname);
        } else
            alert("Fatal Error: Function $functionname not found.");
    } else
        alert("Fatal Error: Function-File $functionname not found.");
    
    return $returnstr;
}
?>