<?php

//Apply Menus
function dbt_menus(){

    add_menu_page("DB-Toolkit Extensions Manager", "DBT Extensions", 'activate_plugins', "dbte_manager", "dbte_Manager", dbtep.'images/cogs.png');
    $landing = add_submenu_page("dbte_manager", 'DB-Toolkit Extensions Manager', 'Manage', 'activate_plugins', "dbte_manager", 'dbte_Manager');
    //$landing = add_submenu_page("dbte_manager", 'DB-Toolkit Extensions Store', 'Store', 'activate_plugins', "News", 'dbte_Manager');

    //$adminPage = add_submenu_page("Database_Toolkit_Welcome", 'Manage Interfaces', 'Interfaces & Clusters', 'activate_plugins', "Database_Toolkit", 'dbtoolkit_admin');
    //$adminPage = add_submenu_page("Database_Toolkit_Welcome", 'Application Builder', 'App Builder', 'activate_plugins', "dbt_builder", 'dbtoolkit_admin');


    add_action('admin_print_styles-'.$landing, 'dt_styles');
    add_action('admin_print_scripts-'.$landing, 'dt_scripts');
    add_action('admin_print_scripts-'.$landing, 'dbte_scripts');

}

function dbte_scripts(){

    // Include DBT-JS
    wp_register_script('DBTe-jslib', dbtep.'libs/handlers.js', false, false, true);
    wp_enqueue_script('DBTe-jslib');

}

// headers Ajax
function dbte_headers(){
?>
<script type="text/javascript" >
    function ajaxCall() {
    <?php
    if(is_admin()) {
        ?>
            var vars = { action : 'dt_ajaxCall',func: ajaxCall.arguments[0]};
        <?php
    }
    ?>

            for(i=1;ajaxCall.arguments.length-1>i; i++) {
                vars['FARGS[' + i + ']'] = ajaxCall.arguments[i];
            }

            var callBack = ajaxCall.arguments[ajaxCall.arguments.length-1];
            jQuery.post(ajaxurl,vars, function(data){
                callBack(data);
            });
        }
</script>
    <?php
}


function dbte_Manager(){
    
            if(!empty($_FILES['extensionUpload'])){
                WP_Filesystem();
                //vardump($_FILES['extensionUpload']);
                $tmpLocation = uniqid();
                if(unzip_file($_FILES['extensionUpload']['tmp_name'], dbte.'tmp/'.$tmpLocation)){
                    $d = dir(dbte.'tmp/'.$tmpLocation);
                    while($entry = $d->read()) {
                     if ($entry!= "." && $entry!= "..") {
                        $Folder = $entry;
                     }
                    }
                    $d->close();
                    if(file_exists(dbte.'tmp/'.$tmpLocation.'/'.$Folder.'/conf.php')){
                            if(!file_exists(dbte.'extensions/'.$Folder)){
                                mkdir(dbte.'extensions/'.$Folder);
                                if(copy_dir(dbte.'tmp/'.$tmpLocation.'/'.$Folder, dbte.'extensions/'.$Folder)){
                                    dbte_uninstall(dbte.'tmp/'.$tmpLocation);
                                }
                            }else{
                                dbte_uninstall(dbte.'extensions/'.$Folder);
                                if(copy_dir(dbte.'tmp/'.$tmpLocation.'/'.$Folder, dbte.'extensions/'.$Folder)){
                                    dbte_uninstall(dbte.'tmp/'.$tmpLocation);
                                }
                            }
                    }else{
                            $ErrorMSG = 'Not a Valid Extention Package.';
                            dbte_uninstall(dbte.'tmp/'.$tmpLocation);
                    }
                }
            }

        // open this directory
        $dirRef = dbte.'extensions';
        $myDirectory = opendir($dirRef);

        // get each entry
        while($entryName = readdir($myDirectory)) {
                if($entryName != '.' && $entryName != '..'){
                    if(file_exists($dirRef.'/'.$entryName.'/conf.php')){                        
                        include($dirRef.'/'.$entryName.'/conf.php');
                        
                        if(!empty($FieldTypeTitle)){
                            $FieldsArray[$entryName]['Folder'] = $entryName;
                            $FieldsArray[$entryName]['Name'] = $FieldTypeTitle;
                            $FieldsArray[$entryName]['Description'] = $FieldDescription;
                            $FieldsArray[$entryName]['Version'] = $FieldVersion;
                            $FieldsArray[$entryName]['Author'] = $FieldAuthor;
                            $FieldsArray[$entryName]['URL'] = $FieldURL;
                            foreach($FieldTypes as $FieldType){
                                $FieldsArray[$entryName]['Fields'][] = $FieldType['name'];
                            }
                            if(file_exists(DB_TOOLKIT.'/data_form/fieldtypes/'.$entryName)){
                                $FieldsArray[$entryName]['Status'] = 'Installed';
                            }else{
                                $FieldsArray[$entryName]['Status'] = 'Not Installed';
                            }
                            $FieldTypeTitle = false;
                        }else{                            
                            if($Type == 'form'){
                                $ProcessorsArray[$entryName]['Folder'] = $entryName;
                                $ProcessorsArray[$entryName]['Name'] = $Title;
                                $ProcessorsArray[$entryName]['Version'] = $Version;
                                $ProcessorsArray[$entryName]['Description'] = $Desc;
                                $ProcessorsArray[$entryName]['Author'] = $Author;
                                $ProcessorsArray[$entryName]['URL'] = $URL;
                                if(file_exists(DB_TOOLKIT.'/data_form/processors/'.$entryName)){
                                    $ProcessorsArray[$entryName]['Status'] = 'Installed';
                                }else{
                                    $ProcessorsArray[$entryName]['Status'] = 'Not Installed';
                                }
                            }            
                            if($Type == 'view'){
                                $ViewProcessorsArray[$entryName]['Folder'] = $entryName;
                                $ViewProcessorsArray[$entryName]['Name'] = $ViewTitle;
                                $ViewProcessorsArray[$entryName]['Version'] = $Version;
                                $ViewProcessorsArray[$entryName]['Description'] = $ViewDesc;
                                $ViewProcessorsArray[$entryName]['Author'] = $Author;
                                $ViewProcessorsArray[$entryName]['URL'] = $URL;
                                if(file_exists(DB_TOOLKIT.'/data_report/processors/'.$entryName)){
                                    $ViewProcessorsArray[$entryName]['Status'] = 'Installed';
                                }else{
                                    $ViewProcessorsArray[$entryName]['Status'] = 'Not Installed';
                                }
                            }
                        }
                    }
                }
        }
        
        // close directory
        closedir($myDirectory);

        //vardump($FieldsArray);
        //vardump($ProcessorsArray);
?>

<h2 id="appTitle">DB-Toolkit</h2>
<div id="dbt_container" class="wrap poststuff">

        <input type="hidden" name="Data[Content][_FormLayout]" cols="50" rows="10" id="_FormLayout" />
        <div id="header">
            <div class="title">
                <h2>Extensions Manager</h2>
            </div>
            <?php
            $icon = '<img src="'.WP_PLUGIN_URL.'/db-toolkit/images/defaultlogo.png" />';
            ?>
            <div class="logo"><?php echo $icon; ?></div>

            <div class="clear"></div>
        </div>
        <div class="save_bar_tools">

        <form name="importApplication" enctype="multipart/form-data" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	Upload Extension: <input type="file" name="extensionUpload" >
	<input type="submit" value="Upload" class="button">
        </form>

        </div>
        <div id="main">
            <div id="dbt-nav">

                <ul>
                    <li class="current">
                        <a href="#FieldTypes" title="FieldTypes">FieldTypes</a>
                    </li>
                    <li class="">
                        <a href="#FormProcessors" title="Form Processors">Form Processors</a>
                    </li>
                    <li class="">
                        <a href="#ViewProcessors" title="View Processors">View Processors</a>
                    </li>
                </ul>

            </div>
            <div id="content">
                <div id="FieldTypes" class="group" style="display: block;">
                    <h2>FieldTypes</h2>
                    <?php
                        ksort($FieldsArray);
                        foreach($FieldsArray as $folder=>$Field){
                            
                            echo '<div class="interfaceModule">';
                                
                                // Install Status
                                echo '<div class="interfaceDets" style="width: 130px !important;">';
                                    //echo '<div>'.$Field['Status'].'</div>';
                                    $Class = "button";
                                    if(file_exists(DB_TOOLKIT.'data_form/fieldtypes/'.$Field['Folder'])){
                                        $Class = "button-primary";
                                    }
                                    echo '<button id="button_'.$Field['Folder'].'" class="'.$Class.'" onclick="dbte_installFieldType(\''.$Field['Folder'].'\');">'.$Field['Status'].'</button>';
                                echo '</div>';

                                // Name
                                echo '<h2>'.$Field['Name'].'</h2>';
                                echo '<div class="interfaceDescription">'.$Field['Description'].'</div>';
                                if(!empty($Field['Author'])){
                                    echo '<div class="interfaceDetails">Author: <a href="'.$Field['URL'].'" target=_blank"><strong>'.$Field['Author'].'</strong></a> | Version: '.$Field['Version'].'</div>';
                                }
                            echo '</div>';

                        }
                    ?>
                </div>
                <div id="FormProcessors" class="group" style="display: none;">
                    <h2>Form Processors</h2>
                    <?php
                        ksort($ProcessorsArray);
                        foreach($ProcessorsArray as $folder=>$Field){
                           
                            echo '<div class="interfaceModule">';

                                // Install Status
                                echo '<div class="interfaceDets" style="width: 130px !important;">';
                                    //echo '<div>'.$Field['Status'].'</div>';
                                    $Class = "button";
                                    if(file_exists(DB_TOOLKIT.'data_form/processors/'.$Field['Folder'])){
                                        $Class = "button-primary";
                                    }
                                    echo '<button id="button_'.$Field['Folder'].'" class="'.$Class.'" onclick="dbte_installProcessor(\''.$Field['Folder'].'\');">'.$Field['Status'].'</button>';
                                echo '</div>';

                                // Name
                                echo '<h2>'.$Field['Name'].'</h2>';
                                echo '<div class="interfaceDescription">'.$Field['Description'].'</div>';
                                if(!empty($Field['Author'])){
                                    echo '<div class="interfaceDetails">Author: <a href="'.$Field['URL'].'" target=_blank"><strong>'.$Field['Author'].'</strong></a> | Version: '.$Field['Version'].'</div>';
                                }
                            echo '</div>';

                        }
                    ?>
                </div>
                <div id="ViewProcessors" class="group" style="display: none;">
                    <h2>View Processors</h2>
                    <?php
                        ksort($ViewProcessorsArray);
                        foreach($ViewProcessorsArray as $folder=>$Field){

                            echo '<div class="interfaceModule">';

                                // Install Status
                                echo '<div class="interfaceDets" style="width: 130px !important;">';
                                    //echo '<div>'.$Field['Status'].'</div>';
                                    $Class = "button";
                                    if(file_exists(DB_TOOLKIT.'data_report/processors/'.$Field['Folder'])){
                                        $Class = "button-primary";
                                    }
                                    echo '<button id="button_'.$Field['Folder'].'" class="'.$Class.'" onclick="dbte_installViewProcessor(\''.$Field['Folder'].'\');">'.$Field['Status'].'</button>';
                                echo '</div>';

                                // Name
                                echo '<h2>'.$Field['Name'].'</h2>';
                                echo '<div class="interfaceDescription">'.$Field['Description'].'</div>';
                                if(!empty($Field['Author'])){
                                    echo '<div class="interfaceDetails">Author: <a href="'.$Field['URL'].'" target=_blank"><strong>'.$Field['Author'].'</strong></a> | Version: '.$Field['Version'].'</div>';
                                }
                            echo '</div>';

                        }
                    ?>
                </div>
            </div>
            <div class="clear"></div>

        </div>
        <div class="save_bar_top">

                <span class="submit-footer-reset">
                </span>
        </div>

    <div style="clear:both;"></div>
</div>











<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#dbt-nav li a').click(function(){
            jQuery('#dbt-nav li').removeClass('current');
            jQuery('.group').hide();
            jQuery(''+jQuery(this).attr('href')+'').show();
            jQuery(this).parent().addClass('current');
            //alert(jQuery(this).attr('href'));
            return false;
        });

        jQuery('#dbt_container .help').click(function(){
            jQuery(''+jQuery(this).attr('href')+'').toggle();
            return false;
        })
    });
</script>





<?php
}


function dbte_installFieldType($Folder){
    if(file_exists(DB_TOOLKIT.'data_form/fieldtypes/'.$Folder)){
        if(dbte_uninstall(DB_TOOLKIT.'data_form/fieldtypes/'.$Folder)){
            $out['button'] = 'Not Installed';
            $out['styleClass'] = 'button';
            return $out;
        }
        return false;
    }else{
        WP_Filesystem();
        if(!file_exists(DB_TOOLKIT.'data_form/fieldtypes/'.$Folder)){
            mkdir(DB_TOOLKIT.'data_form/fieldtypes/'.$Folder);
        }
        copy_dir(dbte.'extensions/'.$Folder, DB_TOOLKIT.'data_form/fieldtypes/'.$Folder);
        $out['button'] = 'Installed';
        $out['styleClass'] = 'button-primary';
        return $out;
    }
    return false;
}

function dbte_installProcessor($Folder){
    if(file_exists(DB_TOOLKIT.'data_form/processors/'.$Folder)){
        if(dbte_uninstall(DB_TOOLKIT.'data_form/processors/'.$Folder)){
            $out['button'] = 'Not Installed';
            $out['styleClass'] = 'button';
            return $out;
        }
        return false;
    }else{
        WP_Filesystem();
        if(!file_exists(DB_TOOLKIT.'data_form/processors/'.$Folder)){
            mkdir(DB_TOOLKIT.'data_form/processors/'.$Folder);
        }
        copy_dir(dbte.'extensions/'.$Folder, DB_TOOLKIT.'data_form/processors/'.$Folder);
        $out['button'] = 'Installed';
        $out['styleClass'] = 'button-primary';
        return $out;
    }
    return false;
}
function dbte_installViewProcessor($Folder){
    if(file_exists(DB_TOOLKIT.'data_report/processors/'.$Folder)){
        if(dbte_uninstall(DB_TOOLKIT.'data_report/processors/'.$Folder)){
            $out['button'] = 'Not Installed';
            $out['styleClass'] = 'button';
            return $out;
        }
        return false;
    }else{
        WP_Filesystem();
        if(!file_exists(DB_TOOLKIT.'data_report/processors/'.$Folder)){
            mkdir(DB_TOOLKIT.'data_report/processors/'.$Folder);
        }
        copy_dir(dbte.'extensions/'.$Folder, DB_TOOLKIT.'data_report/processors/'.$Folder);
        $out['button'] = 'Installed';
        $out['styleClass'] = 'button-primary';
        return $out;
    }
    return false;
}

function dbte_uninstall($Folder){
    
    $d = dir($Folder); 
    while($entry = $d->read()) { 
     if ($entry!= "." && $entry!= "..") {
         if(is_dir($Folder.'/'.$entry)){
            dbte_uninstall($Folder.'/'.$entry);
         }else{
            unlink($Folder.'/'.$entry);
         }
     } 
    } 
    $d->close();
    rmdir($Folder);
    if(file_exists($Folder)){
        return false;
    }
    return true;
}


?>