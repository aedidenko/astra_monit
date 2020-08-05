<?php

header("Content-Type: video/x-ms-asf");
header("Content-disposition: attachment; filename=\"". $_GET[play] .".asx\"");
echo "<asx version=\"3\">\n";

?>
    <entry>
        <ref href="<?php echo $_GET[play]; ?>"/>
    </entry>
<?php
    echo "</asx>";
?>
