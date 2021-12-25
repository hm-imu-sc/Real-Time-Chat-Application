<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            function dir_depth_count($f_dir="global_assets") {
                $dir = __DIR__;
                $depth = "";
                $dir_parts = explode("\\", $dir);
                for ($i=sizeof($dir_parts) - 1; $i > 0; $i--) { 
                    $directory = "C:";
                    for ($j=1; $j <= $i; $j++) {$directory.="\\{$dir_parts[$j]}";}
                    if (is_dir($directory)) {
                        $folder = opendir($directory);
                        while (($subfolder = readdir($folder)) !== FALSE && $folder) {
                            if (is_dir($directory."\\".$subfolder) && $subfolder==$f_dir) {return $depth;}
                        }
                    }
                    $depth.="../";
                }
                return $depth;
            }
            $dir_depth = dir_depth_count();
            include_once "{$dir_depth}my_modules/system.php";
        ?>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?php echo $dir_depth ?>global_assets/fonts/font-awesome-pro-5/css/all.css">
        <link rel="stylesheet" href="<?php echo $dir_depth ?>global_assets/css/style.css">
        <title>index</title>
    </head>
    <body>
        <button id="hide_unhide_system">
            <i class="fad fa-chevron-circle-left"></i>
        </button>
        <br>
        
        <div class="system">
            <?php
                access_denied($_COOKIE, true);
                echo system_controls(file:__FILE__);
                echo file_folders(__DIR__);
            ?>
        </div>
        
        <script src="<?php echo $dir_depth ?>global_assets/js/jquery-3.6.0.min.js"></script>
        <script src="<?php echo $dir_depth ?>global_assets/js/script.js"></script>
    </body>
</html>