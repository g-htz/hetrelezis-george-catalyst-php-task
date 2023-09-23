<?php
    for($i = 1; $i <= 100; $i++) 
    {
        echo $i . "\n";
        if($i % 3 == 0 && $i % 5 == 0) echo "foobar" . "\n";
        else if($i % 3 == 0) echo "foo" . "\n";
        else if($i % 5 == 0) echo "bar" . "\n";
    }
?>
