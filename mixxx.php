<html>
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet">
</head>

<body>

<?php
//$sqlitedb='C:/Users/lemmh/AppData/Local/Mixxx/mixxxdb.sqlite';
$sqlitedb='C:/MixxxAppData/mixxxdb.sqlite';
$basedir='C:/inetpub/wwwroot/';
$title='Legendary LAN Radio';
$listen='http://radio.cryptichaven.org:8020/listen.pls';
$insert=3; //How to add to playlist, 1 = start, 2 = random, 3 = end

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<style type="text/css">
.dir {
padding-left:20px;
background:#c9c9c9;
text-size:2em;
font-weight:bold;
border-bottom:solid 1px #000;
}
.songs {
    display:block;
}
.typec {
background:#595959;
}
.file { 
background:#898989;
padding-left:20px;
border-bottom:#000 solid 1px;
}
.header {
    font-family: 'Roboto Condensed', 'Roboto', Arial;
    text-transform: uppercase;
    color: #7f7;
    font-size:5em;
    text-align:center;
    background:#696969;
}
a:link,a:visited {
color:#000;
text-decoration:none;
}
.background {
background:#696969;
padding:15px;
border:";
}
body {
font-family: Roboto, 'Open Sans', Arial;

background:#234;
padding:15px;
}
.links {
text-align:center;
}
.current {
color:#fff;
background:#898989;
}
.headbar {
background:#797979;
padding-left:20px;
border-bottom:#000 solid 1px;
}
</style>

<script type="text/javascript">
function showhide(id) {
    var sub = id.split('_');

    id=document.getElementById(id);
    //if (id.style.display == 'block') {
    if (id.style.display != 'none') {
        id.style.display = 'none';
        document.getElementById('sub_'+sub[1]).style.display = 'none';
    } else {
        id.style.display = 'block';
    }
}
function getXMLHttp() {
    var xmlHttp;
    try {
        xmlHttp = new XMLHttpRequest(); 
    } catch(e) { 
        try{
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(e) {
            try{
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch(e) {
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    };
    return xmlHttp;
};
function addsong(song){
    var xmlHttp = getXMLHttp();
    xmlHttp.onreadystatechange = function(){
        if(xmlHttp.readyState == 4){    HandleResponse(xmlHttp.responseText);   }
    };
    xmlHttp.open("GET", "mixxx.php?song="+escape(song), true);
    xmlHttp.send(null);
}
function HandleResponse(response){
    document.getElementById('ResponseDiv').innerHTML = response;
}
</script>
<div class="background">
<span style="float:left;position: absolute;" class="search">
<form name="search" method="get" action=""><input name="s" type="text"/><input type="submit" value="Search" /></form>
</span><span style="clear:left;"></span>
<?php
function secs2mins($a) {
    $h=bcmod((intval($a) / 60),60).':'.bcmod(intval($a),60).(strlen(bcmod(intval($a),60))==1?'0':'');
    return ($h=='0:00'?'---':$h);
}
echo '<span style="float:right;"><a href="/mixxx.php"><font color="black">Home</font></a></span>'; //*AMS*
echo '<span style="float:right;"><a href="'.$listen.'"><font color="black">Listen</font></a></span>';
echo '<div class="header">'.$title.'</div>';
//$me=end(explode('/',$_SERVER["SCRIPT_NAME"]));
$tmp = explode('/',$_SERVER["SCRIPT_NAME"]);
$me = end($tmp);
echo '<div class="links">BROWSE BY ARTIST: ';
echo '<a href="'.$me.'?p=09">0-9</a>';
for ($x=97;$x<=122;$x++) {
    echo ' <a href="/'.$me.'?p='.chr($x).'">'.strtoupper(chr($x)).'</a>';
}
echo '</div>';

/* AMS Adding new links to browse by crate */
echo '<div class="links">BROWSE BY CATEGORY: ';
echo ' <a href="/'.$me.'?p=crate8">Comedic/Memey</a>';
echo ' <a href="/'.$me.'?p=crate6">Soundtracks</a>';
echo ' <a href="/'.$me.'?p=crate9">Video Game OSV</a>';
echo ' <a href="/'.$me.'?p=crate5">Retirement Home</a>';
echo '</div>';

/* Mixxx Auto DJ queue - going to present this differently at some point */
echo '<div class="links"> <a href="/'.$me.'?p=Mixxx">Mixxx</a> </div><br /><br />'."\n";

//if($_GET['s']) {
$z = ""; //*AMS*
$y = ""; //*AMS*
$sep = ""; //*AMS*

/* Search using input field */
if(isset($_GET['s'])) {
    $db=new PDO('sqlite:'.$sqlitedb);
    echo '<div class="typec">Search Results -> Directorys</div><table>';
    $rows = $db->query("SELECT t.*,l.artist,l.title,l.album,l.duration FROM track_locations t JOIN library l ON t.id = l.id JOIN crate_tracks ct ON t.id = ct.track_id WHERE ct.crate_id IN (4, 5, 6, 7, 8, 9) and l.artist like '%".str_replace(' ','%',$_GET['s'])."%' or l.title like '%".str_replace(' ','%',$_GET['s'])."%' or l.album like '%".str_replace(' ','%',$_GET['s'])."%' ORDER BY l.artist, l.album ")->fetchAll();
    foreach($rows AS $k => $v) {
        if (substr($basedir,0,-1) != $v['location']) {
            $d=str_replace($basedir,'',dirname($v['location']).'/');
            $d=str_replace('.','',$d);
            if (strpos($d,'/')) {
                if ($d!=$z) {
                    echo '</table></div><div onmouseover="this.style.cursor = \'pointer\';" onclick="showhide(\''.urlencode($d).'\');" id="'.$v['id'].'" class="dir">'.str_replace('/','<b> -> </b>',$d).'</div><div id="'.urlencode($d).'" class="songs"><table width="100%"><tr class="headbar"><th>Artist</th><th>Title</th><th>Album</th></tr>';
                    $z=$d;
                }
                echo '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td>'.($v['artist']?:'---').'</td><td>'.($v['title']?:'---').'</td><td align="center">'.($v['album']?:'---').'</td></tr>';
            }  else {
                $sep.= '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td>'.($v['artist']?:'---').'</td><td>'.($v['title']?:'---').'</td><td align="center">'.($v['album']?:'---').'</td></tr>'."\n";
            }
        }
    }
    if ($sep) echo '</div><br /><br /><div style="text-align:center;" class="typec">Search Results - > Single files</div><table width="100%"><tr class="headbar"><th>Filename</th><th>Genre</th><th>bpm</th><th>Length</th></tr>'.$sep.'</table>';
}
if (isset($_GET['s'])) die();
if (isset($_GET['p']) and $_GET['p'] != "" and !isset($_GET['s'])) $p=strtolower($_GET['p']);
else $p='a';

/* Add to Auto DJ Queue - triggered by addsong() function */
if (isset($_GET['song']) and $_GET['song'] != "") {
    $db=new PDO('sqlite:'.$sqlitedb);
    $rows = $db->query("select id from playlisttracks where playlist_id = 1 and track_id = ".$_GET['song'])->fetchAll();
    if (isset($rows[0]["id"])) return;
    $rows = $db->query("SELECT id FROM track_locations WHERE id = ".$_GET['song'])->fetchAll();
    if ($rows[0]["id"]) $id = $_GET['song'];
    $rows = $db->query("SELECT position FROM playlisttracks WHERE playlist_id = 1 ORDER BY position DESC LIMIT 1")->fetchAll();
    if ($insert==3) $position=$rows[0]['position']+1;
    if ($insert==1) $position=1;
    if ($insert==2) $position=rand(1,$rows[0]['position']);
    $playlist=1;
    if (!isset($id)) return;
    if (!isset($position)) return;
    $db->query("INSERT INTO playlisttracks (playlist_id,track_id,position) VALUES(".$playlist.",".$id.",".$position.")");
}

/* Mixxx Auto DJ Queue link */
//if (strtolower($_GET['p'])=='mixxx') {
if (isset($_GET['p']) and (strtolower($_GET['p'])=='mixxx')) {
    $db=new PDO('sqlite:'.$sqlitedb);
    echo '<div class="typec">Mixxx AutoDJ Queue</div></div><table width="100%"><tr class="headbar"><th>Filename</th><th>Genre</th><th>bpm</th><th>Length</th></tr>';
    $rows = $db->query("SELECT t.filename,l.bpm,l.duration,l.genre from playlisttracks p JOIN library l ON l.id = t.id JOIN track_locations t ON p.track_id = t.id where p.playlist_id = 1 order by p.position")->fetchAll();
    $u = 0; //*AMS*
    foreach ($rows AS $k => $v) {
        $u++;
        echo '<tr class="'.($u=='1'?'current':'file').'"><td>'.$u.': '.$v['filename'].'</td><td>'.($v['genre']?:'---').'</td><td align="center">'.($v['bpm']==0?'---':$v['bpm']).'</td><td align="center">'.secs2mins($v['duration']).'</tr>';
    }
    echo '</table>';
}

/* Alphabetic links */
//if (strtolower($_GET['p'])!='mixxx' and !is_numeric($p)) {
if (isset($_GET['p']) and strtolower($_GET['p'])!='mixxx' and !is_numeric($p)) {
    $db=new PDO('sqlite:'.$sqlitedb);
    //echo '<div class="typec">Sorted files</div><table>';
    $rows = $db->query("SELECT t.*,l.artist,l.title,l.album,l.duration,l.album_artist FROM track_locations t JOIN library l ON t.id = l.id JOIN crate_tracks ct ON t.id = ct.track_id WHERE ct.crate_id IN (4, 5, 6, 7, 8, 9) and l.artist like '$p%' or l.artist like 'The $p%' or l.artist like 'A $p%' ORDER BY l.album_artist,l.artist,l.album ")->fetchAll();
    foreach ($rows AS $k => $v) {
        //$dir=str_replace($basedir,'',$v['directory']); //store directory of current file (omitting base dir), compare at end, either add to grouping or start new
        $art=$v['artist'];
        $aart=(is_null($v['album_artist'])?$art:$v['album_artist']);
        if ((strtoupper($art)!=$z) && (strtoupper($aart)!=$y)) {
            echo '</table></div><div onmouseover="this.style.cursor = \'pointer\';" onclick="showhide(\''.urlencode($art).'\');" id="'.$v['id'].'" class="dir">'.$art;
            //echo '</div><div id="'.urlencode($dir).'" class="songs"><table width="100%"><tr class="headbar"><th>Filename</th><th>Genre</th><th>bpm</th><th>Length</th></tr>'."\n"; 
            echo '</div><div id="'.urlencode($art).'" class="songs"><table width="100%"><tr class="headbar"><th>Song</th><th>Album</th><th>Length</th></tr>'."\n"; 
            $z=strtoupper($art);
            $y=strtoupper($aart);
        }
        if (trim($v['filename'])!="") echo '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td>'.($v['title']?:'---').'</td><td align="center">'.$v['album'].'</td><td align="center">'.secs2mins($v['duration']).'</td></tr>'."\n";
    }
    echo '</table></div><br /><br />';
    /*
    $db=new PDO('sqlite:'.$sqlitedb);
    echo '<div style="text-align:center;" class="typec">Single files</div><table border="0" width="100%"><tr class="headbar"><th>Filename</th><th>Genre</th><th>bpm</th><th>Length</th></tr>';
    $rows = $db->query("SELECT t.*,l.genre,l.duration,l.bpm from track_locations t JOIN library l ON t.id = l.id where t.directory = '".substr($basedir,0,-1)."' AND t.filename like '$p%'")->fetchAll();
    foreach ($rows AS $v) {
        if (trim($v['filename'])!="") echo '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td>'.$v['filename'].'</td><td>'.($v['genre']?:'---').'</td><td align="center">'.(round($v['bpm'],2)?:'---').'</td><td align="center">'.secs2mins($v['duration']).'</td></tr>'."\n";
    }
    echo '</table>';
    */
}

/* Numeric "0-9" link */
if (isset($_GET['p']) and is_numeric($_GET['p'])) {
    $db=new PDO('sqlite:'.$sqlitedb);
    //echo '<div class="typec">Sorted files</div>';
    for($i=0;$i<=9;$i++) {
        $rows = $db->query("SELECT t.*,l.artist,l.title,l.album,l.duration FROM track_locations t JOIN library l ON t.id = l.id JOIN crate_tracks ct ON t.id = ct.track_id WHERE ct.crate_id IN (4, 5, 6, 7, 8, 9) and l.artist like '$i%' ORDER BY l.artist, l.album ")->fetchAll();
        foreach ($rows AS $k => $v) {
            //$dir=str_replace($basedir,'',$v['directory']); //store directory of current file (omitting base dir), compare at end, either add to grouping or start new
            $dir=$v['artist'];
            if ($dir!=$z) {
                echo '</table></div><div onmouseover="this.style.cursor = \'pointer\';" onclick="showhide(\''.urlencode($dir).'\');" id="'.$v['id'].'" class="dir">'.str_replace('/','<b> - > </b>',$dir);
                echo '</div><div id="'.urlencode($dir).'" class="songs"><table width="100%"><tr class="headbar"><th>Song</th><th>Album</th><th>Length</th></tr>'."\n"; 
                $z=$dir;
            }
            if (trim($v['filename'])!="") echo '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td>'.($v['title']?:'---').'</td><td align="center">'.$v['album'].'</td><td align="center">'.secs2mins($v['duration']).'</td></tr>'."\n";
        }
    }
    /*
    $db=new PDO('sqlite:'.$sqlitedb);
    echo '</table></div><br /><br /><div style="text-align:center;" class="typec">Single files</div><table width="100%"><tr class="headbar"><th>Filename</th><th>Genre</th><th>bpm</th><th>Length</th></tr>';
    for($i=0;$i<=9;$i++) {
        $rows = $db->query("SELECT t.*,l.genre,l.duration,l.bpm from track_locations t JOIN library l ON t.id = l.id where t.directory = '".substr($basedir,0,-1)."' AND t.filename like '$i%'")->fetchAll();
        foreach ($rows AS $v) {
            if (trim($v['filename'])!="") echo '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td>'.$v['filename'].'</td><td>'.($v['genre']?:'---').'</td><td align="center">'.(round($v['bpm'],2)?:'---').'</td><td align="center">'.secs2mins($v['duration']).'</td></tr>'."\n";
        }
    }
    */
    echo '</table>';
}

/* Crate 6 - Soundtracks */
if (isset($_GET['p']) and (strtolower($_GET['p'])=='crate6')) {
    $db=new PDO('sqlite:'.$sqlitedb);
    echo '<div class="typec">Soundtracks<br>Movie/TV scores and CD-quality video game stuff, including remixes/covers.</div><table>';
    $rows = $db->query("SELECT t.*,l.artist,l.title,l.album,l.duration,l.album_artist FROM track_locations t JOIN library l ON t.id = l.id JOIN crate_tracks ct ON t.id = ct.track_id WHERE ct.crate_id=6 ORDER BY l.album ")->fetchAll();
    foreach ($rows AS $k => $v) {
        $alb=$v['album'];
        if (strtoupper($alb)!=$z) {
            echo '</table></div><div onmouseover="this.style.cursor = \'pointer\';" onclick="showhide(\''.urlencode($alb).'\');" id="'.$v['id'].'" class="dir">'.str_replace('/','<b> - > </b>',$alb);
            echo '</div><div id="'.urlencode($alb).'" class="songs"><table width="100%"><tr class="headbar"><th>Song</th><th>Artist</th><th>Length</th></tr>'."\n"; 
            $z=strtoupper($alb);
        }
        if (trim($v['filename'])!="") echo '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td>'.($v['title']?:'---').'</td><td>'.$v['artist'].'</td><td align="center">'.secs2mins($v['duration']).'</td></tr>'."\n";
    }
    echo '</table></div><br /><br />';
}

/* Crate 9 - Video Game OSV */
if (isset($_GET['p']) and (strtolower($_GET['p'])=='crate9')) {
    $db=new PDO('sqlite:'.$sqlitedb);
    echo '<div class="typec">Video Game OSV<br>Video game music, the way it used to sound. No remixes here (see "Soundtracks" for those). Nostalgia purists only.</div><table>';
    $rows = $db->query("SELECT t.*,l.artist,l.title,l.album,l.duration,l.album_artist FROM track_locations t JOIN library l ON t.id = l.id JOIN crate_tracks ct ON t.id = ct.track_id WHERE ct.crate_id=9 ORDER BY l.album ")->fetchAll();
    foreach ($rows AS $k => $v) {
        $alb=$v['album'];
        if (strtoupper($alb)!=$z) {
            echo '</table></div><div onmouseover="this.style.cursor = \'pointer\';" onclick="showhide(\''.urlencode($alb).'\');" id="'.$v['id'].'" class="dir">'.str_replace('/','<b> - > </b>',$alb);
            echo '</div><div id="'.urlencode($alb).'" class="songs"><table width="100%"><tr class="headbar"><th>Song</th><th>Artist</th><th>Length</th></tr>'."\n"; 
            $z=strtoupper($alb);
        }
        if (trim($v['filename'])!="") echo '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td>'.($v['title']?:'---').'</td><td>'.$v['artist'].'</td><td align="center">'.secs2mins($v['duration']).'</td></tr>'."\n";
    }
    echo '</table></div><br /><br />';
}

/* Crate 8 - Comedic-Memey */
if (isset($_GET['p']) and (strtolower($_GET['p'])=='crate8')) {
    $db=new PDO('sqlite:'.$sqlitedb);
    echo '<div class="typec">Comedic/Memey<br>Stuff that was funny the first year it was uploaded, or has otherwise achieved meme status. Request at your own risk.</div><table>';
    echo '<div class="songs"><tr class="headbar"><th>Artist</th><th>Song</th><th>Length</th></tr></div>'."\n"; 
    $rows = $db->query("SELECT t.*,l.artist,l.title,l.album,l.duration,l.album_artist FROM track_locations t JOIN library l ON t.id = l.id JOIN crate_tracks ct ON t.id = ct.track_id WHERE ct.crate_id=8 ORDER BY l.album_artist,l.artist,l.album ")->fetchAll();
    foreach ($rows AS $k => $v) {
        //$art=$v['artist'];
        //$aart=(is_null($v['album_artist'])?$art:$v['album_artist']);
        //if ((strtoupper($art)!=$z) && (strtoupper($aart)!=$y)) {
            //echo '</table></div><div onmouseover="this.style.cursor = \'pointer\';" onclick="showhide(\''.urlencode($art).'\');" id="'.$v['id'].'" class="dir">'.str_replace('/','<b> - > </b>',$art);
            echo '<div onmouseover="this.style.cursor = \'pointer\';" id="'.$v['id'].'" class="dir"></div>';
            //echo '</div><div class="songs"><table width="100%"><tr class="headbar"><th>Artist</th><th>Song</th><th>Length</th></tr>'."\n"; 
            //echo '</div><div class="songs"><tr class="headbar"><th>Artist</th><th>Song</th><th>Length</th></tr>'."\n"; 
            //$z=strtoupper($art);
            //$y=strtoupper($aart);
        //}
        if (trim($v['filename'])!="") echo '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td align="center">'.$v['artist'].'</td><td>'.($v['title']?:'---').'</td><td align="center">'.secs2mins($v['duration']).'</td></tr>'."\n";
    }
    echo '</table></div><br /><br />';
}

/* Crate 5 - Retirement Home */
if (isset($_GET['p']) and (strtolower($_GET['p'])=='crate5')) {
    $db=new PDO('sqlite:'.$sqlitedb);
    echo '<div class="typec">Retirement Home<br>Dad Rock and anything else that\'s taking a break from the main rotation.</div><table>';
    $rows = $db->query("SELECT t.*,l.artist,l.title,l.album,l.duration,l.album_artist FROM track_locations t JOIN library l ON t.id = l.id JOIN crate_tracks ct ON t.id = ct.track_id WHERE ct.crate_id=5 ORDER BY l.album_artist,l.artist,l.album ")->fetchAll();
    foreach ($rows AS $k => $v) {
        $art=$v['artist'];
        $aart=(is_null($v['album_artist'])?$art:$v['album_artist']);
        if ((strtoupper($art)!=$z) && (strtoupper($aart)!=$y)) {
            echo '</table></div><div onmouseover="this.style.cursor = \'pointer\';" onclick="showhide(\''.urlencode($art).'\');" id="'.$v['id'].'" class="dir">'.str_replace('/','<b> - > </b>',$art);
            echo '</div><div id="'.urlencode($art).'" class="songs"><table width="100%"><tr class="headbar"><th>Song</th><th>Album</th><th>Length</th></tr>'."\n"; 
            $z=strtoupper($art);
            $y=strtoupper($aart);
        }
        if (trim($v['filename'])!="") echo '<tr onmouseover="this.style.cursor = \'crosshair\';" onclick="addsong('.$v['id'].');  this.style.background = \'#595959\';this.style.disabled=\'true\'; " class="file"><td>'.($v['title']?:'---').'</td><td align="center">'.$v['album'].'</td><td align="center">'.secs2mins($v['duration']).'</td></tr>'."\n";
    }
    echo '</table></div><br /><br />';
}

?>

<form action="upload.php" method="post" enctype="multipart/form-data">
  Select file to upload:
  <input type="file" name="uploadedFile" id="uploadedFile">
  <input type="submit" value="Upload Song" name="uploadBtn">
</form>

</body>

</html>