<?php

$modul="upload";
$area="no_customer";
$menu_item="upload";

require("../inc/req.php");
require_once("../inc/sqlInjection.php");

$pk=$_REQUEST['pk'];

validate("area","int");
validate("info","string");
validate("article_id","int");
validate("basearticle_id","int");
validate("pk","string");

if ($_VALID['article_id']) {
    $sql = "SELECT article_nr FROM basearticle WHERE article_id=".$_VALID['article_id'];
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_row($res);
    $articleNr = $row[0];
}

// if we have got an article_id, we are in the fancy box view for article uploads
$article_id = $_REQUEST['article_id'] ? $_VALID['article_id'] : 'NULL';
$basearticle_id = $_REQUEST['basearticle_id'] ? $_VALID['basearticle_id'] : 'NULL';

if($_FILES['file']) {

    $prefix= array();
            if ($pk) {
                $prefix[] = $pk;
            }
            if ($article_id != 'NULL') {
                $prefix[] = 'a'.$article_id;
            }
            if ($basearticle_id != 'NULL') {
                $prefix[] = 'b'.$basearticle_id;
            }
            $prefixS = implode('_', $prefix);

            $tempname = $_FILES['file']['tmp_name'];
            $name = $_FILES['file']['name'];
			$original_name = $_FILES['file']['name'];
            $path_parts = pathinfo($name);
            $name = $path_parts['filename'];

            $name = ($prefixS)? $prefixS."_".time()."_".$name : time()."_".$name;   

    //unset($_FILES);
    //unset($_POST);
    //Array leeren;
    if ($_FILES['file']['error']) {
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $err[] = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            break;
            case UPLOAD_ERR_FORM_SIZE:
                $err[] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            break;
            case UPLOAD_ERR_PARTIAL:
                $err[] = 'The uploaded file was only partially uploaded';
            break;
            case UPLOAD_ERR_NO_FILE:
                $err[] = 'No file was uploaded';
            break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $err[] = 'Missing a temporary folder';
            break;
            case UPLOAD_ERR_CANT_WRITE:
                $err[] = 'Failed to write file to disk';
            break;
            case UPLOAD_ERR_EXTENSION:
                $err[] = 'File upload stopped by extension';
            break;
            default:
                $err[] = 'Unknown upload error';
            break;
        }
    }

    if($name=="") {
        $err[] = "Please select a File. ";
    }

    //if($type=="text/php") {
    //    $err[] = "No PHP files.";
    //}

    if($size > "50000000") {
        $err[] = "File ist too big!<br>Maxfilesize 50 MB!";
    }

    if($_VALID['area']==0) {
        $err[] = "Please select an Area. ";
    }

    if(empty($err)) {             

        $sql ="INSERT INTO document (project_id, user_id, article_id, basearticle_id, area, active, info, dbinsert, dbupdate,original_name) VALUES (" . (($pk)? "'".$pk."'":"NULL") . " , ".$_SESSION['user_id'].", " . $article_id . ", " . $basearticle_id . ", ".$_VALID['area'].", 1, '".sql_injection(($_REQUEST['info']))."', '".$timenow."', '".$timenow."' ,'".$original_name."')";
        $result = mysqli_query($con, $sql) or die(mysqli_error());
        if($result) {   
            $insertedId = mysqli_insert_id($con);
            
            $name = $name.'_'.$insertedId;
            $name = fileName($name). (($path_parts['extension'])?'.'.$path_parts['extension']:'');
                        
            //urlencode($_FILES['file']['name']);
            $type = $_FILES['file']['type'];
            $size = $_FILES['file']['size'];
    
            if(move_uploaded_file($tempname,UPLOAD_ROOT."$name")){
                mysqli_query($con, "UPDATE document SET name='$name' WHERE document_id=$insertedId");
            } else {
                unset($fileName);
                echo '<b><span style="background-color:red">An error occured: the file was not uploaded.</span></b>';
            }
            //Mailversand

            switch($_VALID['area']) {
                case 1: $msg_area = "Printing Datas";
                    break;
                case 2: $msg_area = "Correspondence";
                    break;
                case 3: $msg_area = "Quality Control";
                    break;
                case 4: $msg_area = "Delivery";
                    break;
                case 5: $msg_area = "Specification";
                    break;
            }

            if($pk) {
                $membersql="SELECT u.firstname, u.lastname, u.email
											FROM project2user p2u
											INNER JOIN user u ON p2u.user_id = u.user_id
											WHERE p2u.project_id = '".$pk."'";


                $memberresult = mysqli_query($con, $membersql);
                $projectsql="SELECT project_name, project_nr
											FROM project where project_id = '".$pk."'";


                $projectresult = mysqli_query($con, $projectsql);
                $projectrow = mysqli_fetch_array($projectresult);
                while($row=mysqli_fetch_row($memberresult)) {
                    $msg= "Hello ".$row[0]." ".$row[1]."<br>, there is a new Upload for Project " . $projectrow['project_name'] . " Nr: ".$projectrow['project_nr']." on PSO \r\n\r\nUpload from ".$_SESSION['email'].": Area:".$msg_area.($_VALID['article_id']?"\r\nArticle: ".$articleNr:"").( $_VALID['info']?"\r\nInfo: " . $_VALID['info'] : ""). "<br><br> http://".$_ENV['SERVER_NAME']."/downloads.php?file=".$name." <br><br>Link: http://".$_ENV['SERVER_NAME']."/project_d.php?pk=".$pk;
                    $subject= "PU - new Upload on project ".$projectrow['project_name']."";
                    send_mail($msg, $subject, $row[2], $row[0]." ".$row[1], true,true);

                }
            }
            if($basearticle_id) {
                $membersql="SELECT u.firstname, u.lastname, u.email
											FROM basearticle2user b2u
											INNER JOIN user u ON b2u.user_id = u.user_id
											WHERE b2u.basearticle_id = '".$basearticle_id."'";
                $memberresult = mysqli_query($con, $membersql);
				
                $articlesql="SELECT shortname, article_nr
											FROM basearticle where basearticle_id = '".$basearticle_id."'";


                $articleresult = mysqli_query($con, $articlesql);
                $articlerow = mysqli_fetch_array($articleresult);
                while($row=mysqli_fetch_row($memberresult)) {
                    $msg= "Hello ".$row[0]." ".$row[1].", there is a new Upload for Article " . $articlerow['shortname'] . " Nr: ".$articlerow['article_nr']." on PSO \r\n\r\nUpload from ".$_SESSION['email'].": Area:".$msg_area.($_VALID['article_id']?"\r\nArticle: ".$articleNr:"").( $_VALID['info']?"\r\nInfo: " . $_VALID['info'] : ""). "\r\n http://".$_ENV['SERVER_NAME']."/downloads.php?file=".$name." \r\n\r\nLink: http://".$_ENV['SERVER_NAME']."/article_d.php?id=".$basearticle_id;
                    $subject= "AU - new Upload on article ".$articlerow['shortname']."";
                    send_mail($msg, $subject, $row[2], $row[0]." ".$row[1], true,true);
                }
            }

            $output="Upload >>$name<< Successfull!";
            $up_col="#55FF00";
        }else {
            $output="Error occurred. ";
            $up_col="#DD2200";
        }

    }else {
        foreach($err as $error)
        $output= "$error<br>";
        $up_col="#DD2200";
    }

}

// handle both cases at once: article and basearticle
if ($article_id && $article_id != 'NULL') {
    $somearticle_id = $article_id;
    $idname = 'article_id';
} else if ($basearticle_id && $basearticle_id != 'NULL') {
    $somearticle_id = $basearticle_id;
    $idname = 'basearticle_id';
}

if ($somearticle_id) {

// Dokument aktivieren/deaktivieren
    validate("document_id","int");
    validate("activate","int");
    validate("deactivate","int");
    $activ = $_VALID['activate']?1:($_VALID['deactivate']?0:NULL);

    if (isset($activ) && $_SESSION['mandator_id'] !== 100) {
        $sql = 'UPDATE document SET active=' . $activ . ' WHERE document_id=' . $_VALID['document_id'];
        mysqli_query($con, $sql);
    }

// Read Files
    $sql="SELECT document_id, u.email, u.firstname, u.lastname, name, area, d.dbinsert, d.dbupdate, d.active, d.info
			FROM document d
			INNER JOIN user u ON d.user_id = u.user_id
			WHERE " . (($pk)? "project_id ='".$pk."' AND ":"") . " $idname = ".$somearticle_id."
			ORDER BY ";
    $sql_long = $sql . " area, dbinsert desc"; // Wenn Uploads gross dann -> zuerst nach area sortieren
    $sql_short = $sql . " area, dbinsert desc ";// LIMIT 0,10";
    $result_short=mysqli_query($con, $sql_short);
    $result_long=mysqli_query($con, $sql_long);
    $doc_result_num = mysqli_num_rows($result_long);
    if ($doc_result_num > SHORTVIEW_NUM) $showPlusButton = true;
    $first1=false;
    $first2=false;
    $first3=false;
    $first4=false;
    $first5=false;

    if ($doc_result_num == 0) {
        //$doc_view .= "<br><span>No uploads</span>";
    } else {

        $doc_view .= '<ul id="uploadtree" class="filetree" style="clear:both">';
        while($row=mysqli_fetch_row($result_long)) {
            $active = ($row[8] == 1) ? 1 : 0;
            if($row[5]==1&&$first1==false) {
                $doc_view.='<li class="closed"><span class="folder">Priniting Datas</span><ul>';
                $areaTagOpen = 1;
                $first1=true;
            }
            if($row[5]==2&&$first2==false) {
                if ($areaTagOpen AND ($areaTagOpen < 2)) {
                    $doc_view.='</ul></li>';
                }
                $doc_view.='<li class="closed"><span class="folder">Correspondence</span><ul>';
                $areaTagOpen = 2;
                $first2=true;
            }
            if($row[5]==3&&$first3==false) {
                if ($areaTagOpen AND ($areaTagOpen < 3)) {
                    $doc_view.='</ul></li>';
                }
                $doc_view.='<li class="closed"><span class="folder">Quality Control</span><ul>';
                $areaTagOpen = 3;
                $first3=true;
            }
            if($row[5]==4&&$first4==false) {
                if ($areaTagOpen AND ($areaTagOpen < 4)) {
                    $doc_view.='</ul></li>';
                }
                $doc_view.='<li class="closed"><span class="folder">Delivery</span><ul>';
                $areaTagOpen = 4;
                $first4=true;
            }
            if($row[5]==5&&$first5==false) {
                if ($areaTagOpen AND ($areaTagOpen < 5)) {
                    $doc_view.='</ul></li>';
                }
                $doc_view.='<li class="closed"><span class="folder">Specification</span><ul>';
                $first5=true;
            }
            $doc_view.="<li" . (($active==0) ? " class='deactivated'":"") . "><span class='comment_author'>".htmlentities($row[1], ENT_QUOTES, 'UTF-8') ."<br />".htmlentities($row[6], ENT_QUOTES, 'UTF-8')."</span>&nbsp;<a target='_blank'" . (($active==0) ? " class='deactivated'":"") . " href='../upload/".$row[4]."'>".htmlentities($row[4], ENT_QUOTES, 'UTF-8')."</a>";
            if ($active && $_SESSION['mandator_id'] !== 100) {
                $doc_view.="&nbsp;<a href='" . $_SERVER['PHP_SELF'] . "?pk=" . $_VALID['pk'] . "&amp;xtend_view[uploads]=1&amp;document_id=" . $row[0] . "&amp;$idname=" . $somearticle_id ."&amp;deactivate=1'><i class='fa fa-unlock'></i></a>";
            } elseif ($_SESSION['mandator_id'] !== 100) {
                $doc_view.="&nbsp;(Out-dated)&nbsp;<a href='" . $_SERVER['PHP_SELF'] . "?pk=" . $_VALID['pk'] . "&amp;xtend_view[uploads]=1&amp;document_id=" . $row[0] . "&amp;$idname=" . $somearticle_id . "&amp;activate=1'><i class='fa fa-lock'></i></a>";
            }
            if ($row[9]) {
                $doc_view .= "&nbsp;<span class='comment_author'>(" . htmlentities($row[9], ENT_QUOTES, 'UTF-8') . ")</span>";
            }
            $doc_view .= "</li>";
        }
        $doc_view .= '</ul></li></ul>';
    }
}
?>

<html>
    <head>
        <title><?php echo TITLE." Upload"?></title>
        <link rel="stylesheet" type="text/css" href="../css/pm.css" />
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="../assets/vendor/jquery-treeview/jquery.treeview.css" />
    </head>
    <body>
        <form enctype="multipart/form-data" name="upload" action="upload.php" method="post">
            <div class="rma_box field_box">
                <fieldset style="height:180px">
                    <legend><i class="fa fa-upload"></i><span class="box_head">File Upload</span></legend>
                    <input type="hidden" name="pk" value="<?php echo $pk;?>">
                    <input type="hidden" name="article_id" value="<?php echo $_VALID['article_id'];?>">
                    <input type="hidden" name="basearticle_id" value="<?php echo $_VALID['basearticle_id'];?>">
                    <input type="file" name="file"><br />
                    <br>Info: <input type="text" size="20" name="info" value="<?php echo htmlentities($_VALID['info'], ENT_QUOTES, 'UTF-8');?>"><br>
                    <br><span>Area:</span> <select name="area">
                        <option></option>
                        <option value="1">Printing Data</option>
                        <option value="2">Correspondence</option>
                        <option value="3">Quality Control</option>
                        <option value="4">Delivery</option>
                        <option value="5">Specification</option>
                    </select>
                    <input type="submit" value="Upload">
<?php if($output) {
    echo "<p style='padding:2px;margin:3px;background-color:".$up_col."'>".$output."</p>";
                    }
                    if ($err) die();?>
                    <?php /*if($output&&empty($err)){echo "<script type='text/javascript'>window.top.location.reload();return false;</script>";}*/ ?>
                </fieldset>
            </div>
        </form>
<?php if ($somearticle_id) {
    echo $doc_view;?>

        <script src="../assets/vendor/jquery/jquery.min-v3.5.1.js"></script>
        <script src="../assets/vendor/jquery-treeview/jquery.treeview.js"></script>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                $("#uploadtree").treeview();

            });

        </script>
    <?php } if
($up_col=="#55FF00") {?>
        <script type="text/javascript">parent.window.location.reload();<?php if (!$err) echo 'parent.$.fancybox.close();'?></script>
    <?php } ?>
    </body>
</html>