<SCRIPT src="images/sozluk.js" type=text/javascript></SCRIPT>
<?
if (!$verified_user)
die;

if (!$okmsj) {
echo "Kurcuklama lan!";
exit;
}
else {
// degiskenleri ata
function pingGoogleSitemaps( $url_xml )
{
   $status = 0;
   $google = 'www.google.com';
   if( $fp=@fsockopen($google, 80) )
   {
      $req =  'GET /webmasters/sitemaps/ping?sitemap=' .
              urlencode( $url_xml ) . " HTTP/1.1\r\n" .
              "Host: $google\r\n" .
              "User-Agent: Mozilla/5.0 (compatible; " .
              PHP_OS . ") PHP/" . PHP_VERSION . "\r\n" .
              "Connection: Close\r\n\r\n";
      fwrite( $fp, $req );
      while( !feof($fp) )
      {
         if( @preg_match('~^HTTP/\d\.\d (\d+)~i', fgets($fp, 128), $m) )
         {
            $status = intval( $m[1] );
            break;
         }
      }
      fclose( $fp );
   }
   return( $status );
}
function getir($url){
	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 0);
		$xml = curl_exec($ch);
		curl_close($ch);
	}else{
		$xml = file_get_contents($url);
	}
	return $xml;
}
include "config.php";
$baslik= htmlspecialchars($_POST["baslik"]);
$mesaj = htmlspecialchars(strip_tags($_POST["mesaj"]));
if ($baslik == "" or $mesaj == "") {
if (!$okword) {
echo "Heryeri doldur can�m..";
exit;
}
else {
form($baslik);
exit;
}
}

$site = $_SERVER["HTTP_REFERER"];
$site = explode("/", $site);
$site = $site[2];

$tarih = date("YmdHi");
$gun = date("d");
$ay = date("m");
$yil = date("Y");
$saat = date("H:i");
$ip = getenv('REMOTE_ADDR');

$baslik = substr($baslik, 0, 80);

/*if (!ereg("^([A-Za-z0-9]|[[:space:]])+$",$baslik)) {
echo "<p class=div1>Basliklarda;<br>sadece ingilizce harfler,<br>bosluk {space},<br>ve rakamlar bulunabilir.<br>L�tfen bu kurallara uygun bir baslik yazin.</p>";
exit;
}*/

$yazar = $verified_user;
/*$baslik = ereg_replace("�","s",$baslik);
$baslik = ereg_replace("�","S",$baslik);
$baslik = ereg_replace("�","c",$baslik);
$baslik = ereg_replace("�","C",$baslik);
$baslik = ereg_replace("�","i",$baslik);
$baslik = ereg_replace("�","I",$baslik);
$baslik = ereg_replace("�","g",$baslik);
$baslik = ereg_replace("�","G",$baslik);
$baslik = ereg_replace("�","o",$baslik);
$baslik = ereg_replace("�","O",$baslik);
$baslik = ereg_replace("-","",$baslik);
$baslik = ereg_replace("?","",$baslik);
$baslik = ereg_replace("#","",$baslik);
$baslik = ereg_replace("�","u",$baslik);
$baslik = ereg_replace("�","U",$baslik);
$baslik = ereg_replace("�","O",$baslik);*/


$baslik = strtolower($baslik);
if (strstr($mesaj,"youtube.com/watch")) {
            $youtube='#((http)+(s)?:(//)|(www\.))((\w|\.|\-|_)+)(/)?(\S+)?#i';
            preg_match($youtube,$mesaj,$tube);
            $tube=$tube[0];
            $tube2=str_replace("watch?v=","v/",$tube);
        }  

$baslik = substr($baslik, 0, 80);

$mesaj = ereg_replace("&lt","(",$mesaj);
$mesaj = ereg_replace("&gt",")",$mesaj);
$mesaj = ereg_replace("<","(",$mesaj);
$mesaj = ereg_replace(">",")",$mesaj);
$mesaj = ereg_replace("\n","<br>",$mesaj);
if (strstr($mesaj,"youtube.com/watch")) {
            $youtube='#((http)+(s)?:(//)|(www\.))((\w|\.|\-|_)+)(/)?(\S+)?#i';
            preg_match($youtube,$mesaj,$tube);
            $tube=$tube[0];
            $tube2=str_replace("watch?v=","v/",$tube);
        }  

$sorgu = "SELECT id FROM konucuklar WHERE `baslik`='".mysql_real_escape_string($baslik)."'";
$sorgulama = mysql_query($sorgu);
if (mysql_num_rows($sorgulama)>0){
//kay�tlar� listele
while ($kayit=mysql_fetch_array($sorgulama)){
###################### var ##############################################
$id=$kayit["id"];
if ($id) {
echo "Bu ba�l�ktan zaten var.";
die;
}
}
}

// db ye yaz
$baslik = strtolower($baslik);
$sorgu = "INSERT INTO konucuklar ";
$sorgu .= "(baslik,ip,tarih,gun,ay,yil,saat)";
$sorgu .= " VALUES ";
$sorgu .= "('".mysql_real_escape_string($baslik)."','$ip','$tarih','$gun','$ay','$yil','$saat')";
mysql_query($sorgu);


//sitemap pingliyoruz.
include "config.php";
$sitemap = $sitemap;
if ($sorgu) { 
$pingle = pingGoogleSitemaps($sitemap); 
$rss = "http://pingomatic.com/ping/?title=Anka+Sozluk&blogurl=$site&rssurl=$sitemap&chk_weblogscom=on&chk_blogs=on&chk_technorati=on&chk_feedburner=on&chk_syndic8=on&chk_newsgator=on&chk_myyahoo=on&chk_pubsubcom=on&chk_blogdigger=on&chk_blogrolling=on&chk_blogstreet=on&chk_moreover=on&chk_weblogalot=on&chk_icerocket=on&chk_newsisfree=on&chk_topicexchange=on&chk_google=on&chk_tailrank=on&chk_bloglines=on&chk_postrank=on&chk_skygrid=on&chk_collecta=on&chk_audioweblogs=on&chk_rubhub=on&chk_geourl=on&chk_a2b=on&chk_blogshares=on";
$rss=getir($rss);
} else {}







// id yi almak icin dbye baglan
$sorgu = "SELECT id FROM konucuklar WHERE `baslik`='".mysql_real_escape_string($baslik)."'";
$sorgulama = mysql_query($sorgu);
if (mysql_num_rows($sorgulama)>0){
//kay�tlar� listele
while ($kayit=mysql_fetch_array($sorgulama)){
###################### var ##############################################
$id=$kayit["id"];
if (!$id)
echo "Hata var beah: patrona s�ylesene bi d�zeltsin :(";
}
}
// idyi aldik
// mesaj olarak yaziyoz


$sorgu = "INSERT INTO mesajciklar ";
$sorgu .= "(sira,mesaj,yazar,ip,tarih,gun,ay,yil,saat)";
$sorgu .= " VALUES ";
$sorgu .= "('$id','".mysql_real_escape_string($mesaj)."','$yazar','$ip','$tarih','$gun','$ay','$yil','$saat')";
mysql_query($sorgu);
// mesajida yazdik


//rss pingliyoruz.
include "config.php";
$sitemap = $siterss;
if ($sorgu) { 
$pingle = pingGoogleSitemaps($sitemap); 
$rss = "http://pingomatic.com/ping/?title=Anka+Sozluk&blogurl=$site&rssurl=$siterss&chk_weblogscom=on&chk_blogs=on&chk_technorati=on&chk_feedburner=on&chk_syndic8=on&chk_newsgator=on&chk_myyahoo=on&chk_pubsubcom=on&chk_blogdigger=on&chk_blogrolling=on&chk_blogstreet=on&chk_moreover=on&chk_weblogalot=on&chk_icerocket=on&chk_newsisfree=on&chk_topicexchange=on&chk_google=on&chk_tailrank=on&chk_bloglines=on&chk_postrank=on&chk_skygrid=on&chk_collecta=on&chk_audioweblogs=on&chk_rubhub=on&chk_geourl=on&chk_a2b=on&chk_blogshares=on";
$rss=getir($rss);
} else {}



// ekranada basiyoz
echo "
<script language=\"javascript\">goUrl('sozluk.php?process=today','left');</script>
<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0;URL=sozluk.php?process=word&q=$baslik\">";
} // bitirdik IF i

function form($baslik) {
?>








<font size="4"><a><? if ($baslik) { echo "$baslik"; }?></a></font>
</br>
</br>
</br>
<b>
<div style="margin:35px;margin-bottom:0px;">b�yle bir �ey (<? if ($baslik) { echo "$baslik"; }?>) yok. ama olabilir de.</b></div>
</br>
</br>
</br>




<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-9">
</head>



<form method="post" action="">
<form method=post action=>
  <table width="100%" align="left" class="dash">
<td style="visibility: hidden;">
  <INPUT class=inp maxLength=80 SIZE=60 name=baslik value="<? if ($baslik) { echo "$baslik\" readonly"; }?>">
</td>
    <tr>

      <td colspan="2">

        "<? if ($baslik) { echo "$baslik"; }?>" hakk�nda kafan�zda bir tan�m veya verebileceginiz bir �rnek varsa eklemekten �ekinmeyin:

<div style="text-align: right;">
<input class="but" type="button" name="bkz" value="(bkz: )" onclick="hen('aciklama','(bkz: ',')')" accesskey=x>
<input class="but" type="button" name="bkz" value="` `" onclick="hen('aciklama','(gbkz: ',')')" accesskey=c>
<input class="but" type="button" name="bkz" value="-s!-" onclick="hen('aciklama','--- (gbkz: spoiler) ---\n\n','\n\n--- (gbkz: spoiler) ---')" accesskey=s> 
<input class="but" type="button" name="bkz" value="*" onclick="hen('aciklama','(u: ',')')" accesskey=v>
 </div>

     



                  <textarea id="aciklama" name="mesaj" rows="8" style="width:100%;"></textarea>


    </tr>

</td>

<tr>
<td width="90" align="left" valign="top">
<input id="kaydet" class=but type="submit" name="kaydet" value="yolla panpa">
    <input type=hidden name=ok value=ok>
    <input type=hidden name=okmsj value=ok>
<input type="hidden" name="gonder" value="kaydet">
</tr>

    <tr>

      <td valign="top"  colspan="2"> 
        </td>
     </tr>
  </table>
</form>

</form>
<p class="yazi">&nbsp;</p>
</br>
</br>
<div style="text-align: center;"><font size="1">&nbsp; &copy; 2015 - <a href="http://ankasozluk.com/" target="_blank">anka s�zl�k</a> </font></br><font size="1" color="gray"> '<? print("$q");?>' yazarlara aittir,</br> e�er ki bu pi&ccedil;&ouml;zlere itiraz�n varsa,ya �imdi konu�,yada sonsuza &nbsp;kadar sus. </br>ayr�ca bu ortam ve yaz�lanlar hatta ve hatta g&ouml;rm&uuml;� oldu�un her�ey,yazarlara aittir.. </br>burada her�ey oldu�u gibidir,olmas� gerekti�i gibi de�il.. </br>-kutsal anka&ccedil;-</font></div>
<center><img src="http://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Circle-A_red.svg/2000px-Circle-A_red.svg.png" width="16" height="16"></center>
</body>
</html>
<? } ?>