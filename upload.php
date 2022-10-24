<?php
session_start();
$message = 'BLANK'; 

if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload Song') {
    $message = 'Got to isset(POST)';

  if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
    $message = 'Got to isset(FILES)';

    // uploaded file details
    $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
    $fileName = $_FILES['uploadedFile']['name'];
    $fileSize = $_FILES['uploadedFile']['size'];
    $fileType = $_FILES['uploadedFile']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // removing extra spaces
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

    // file extensions allowed
    $allowedfileExtensions = array('mp3', 'm4a', 'aac', 'ogg', 'flac', 'wav', 'mpt', 'mod', 'it', 'umx', 'xm', 's3m');

    if (in_array($fileExtension, $allowedfileExtensions)) {
      // directory where file will be moved
      $uploadFileDir = "C:/Users/Public/Music/LANtrax/";
      //$uploadFileDir = "C:/MixxxAppData/php_upload_temp/test/";
      //$dest_path = $uploadFileDir . $newFileName;
      $dest_path = $uploadFileDir . $fileName;
      
      echo '<div>Temp:'.$fileTmpPath.'</div>';
      echo '<div>Destination:'.$dest_path.'</div>';

      if(move_uploaded_file($fileTmpPath, $dest_path)) {
        $message = 'File uploaded successfully.';
      } else {
        $message = 'An error occurred while uploading the file to the destination directory. Ensure that the web server has access to write in the path directory.';
      }
    } else {
      $message = 'Upload failed as the file type is not acceptable. The allowed file types are:' . implode(',', $allowedfileExtensions);
    }
  } else {
    $message = 'Error occurred while uploading the file.<br>';
    $message .= 'Error:' . $_FILES['uploadedFile']['error'];
  }
} else {
    $message = 'POST has not been set';
}

$_SESSION['message'] = $message;
echo '<div>Message: '.$message.'</div>';
header("Location: mixxx.php");

?>