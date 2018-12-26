<?php
	if (!defined('ABSOLUTE__PATH__'))
	{
	die ("<meta http-equiv=refresh content='0; url=http://".$HTTP_HOST."/?err=404'>");
	}

function filesize_get($filesize)
{
   // Если размер переданного в функцию файла больше 1кб
   if($filesize > 1024)
   {
       $filesize = ($filesize/1024);
       // если размер файла больше одного килобайта
       // пересчитываем в мегабайтах
       if($filesize > 1024)
       {
            $filesize = ($filesize/1024);
           // если размер файла больше одного мегабайта
           // пересчитываем в гигабайтах
           if($filesize > 1024)
           {
               $filesize = ($filesize/1024);
               $filesize = round($filesize, 1);
               return $filesize." ГБ";

           }
           else
           {
               $filesize = round($filesize, 1);
               return $filesize." MБ";
           }

       }
       else
       {
           $filesize = round($filesize, 1);
           return $filesize." Кб";
       }

   }
   else
   {
       $filesize = round($filesize, 1);
       return $filesize." байт";
   }

}	
//Кодирование строк с ключем
//base64_encode(strcode('String', 'key'))
//strcode(base64_decode(str_replace(' ','+',$String), 'key')
function strcode($str, $passw="")
{
   $salt = "Dn5*#2n!9j";
   $len = strlen($str);
   $gamma = '';
   $n = $len>100 ? 8 : 2;
   while( strlen($gamma)<$len )
   {
      $gamma .= substr(pack('H*', sha1($passw.$gamma.$salt)), 0, $n);
   }
   return $str^$gamma;
}



function GetNav($p, $num_pages){

	if (isset($_GET['page']) AND $_GET['page'] == 'tovar')
	{
	$num_elements = isset($_GET['num_elements'])?'&num_elements='.$_GET['num_elements']:'';
	$sort = isset($_GET['sort'])?'&sort='.$_GET['sort']:'';
	$sort_prod = isset($_GET['sort_prod'])?'&sort_prod='.$_GET['sort_prod']:'';
	$cat = isset($_GET['cat'])?'&cat='.$_GET['cat']:'';
	$q = isset($_GET['q'])?'&q='.$_GET['q']:'';
	$w = isset($_GET['w'])?'&w='.$_GET['w']:'';
	$length = isset($_GET['length'])?'&length='.$_GET['length']:'';
	$fulllink = '?page=tovar'.$num_elements.$sort.$sort_prod.$cat.$q.$w.$length;
	$and = '&p=';
	}
	elseif (isset($_GET['page']) AND $_GET['page'] == 'sklad')
	{
	$num_elements = isset($_GET['num_elements'])?'&num_elements='.$_GET['num_elements']:'';
	$sort = isset($_GET['sort'])?'&sort='.$_GET['sort']:'';
	$sort_prod = isset($_GET['sort_prod'])?'&sort_prod='.$_GET['sort_prod']:'';
	$cat = isset($_GET['cat'])?'&cat='.$_GET['cat']:'';
	$fulllink = '?page=sklad'.$num_elements.$sort.$sort_prod.$cat;
	$and = '&p=';
	}
	elseif (isset($_GET['page']) AND $_GET['page'] == 'postav')
	{
	$num_elements = isset($_GET['num_elements'])?'&num_elements='.$_GET['num_elements']:'';
	$fulllink = '?page=postav'.$num_elements;
	$and = '&p=';
	}
	elseif (isset($_GET['page']) AND $_GET['page'] == 'client')
	{
	$num_elements = isset($_GET['num_elements'])?'&num_elements='.$_GET['num_elements']:'';
	$fulllink = '?page=client'.$num_elements;
	$and = '&p=';
	}
	elseif (isset($_GET['page']) AND $_GET['page'] == 'zakaz')
	{
	$num_elements = isset($_GET['num_elements'])?'&num_elements='.$_GET['num_elements']:'';
	$fulllink = '?page=zakaz'.$num_elements;
	$and = '&p=';
	}
	elseif (isset($_GET['page']) AND $_GET['page'] == 'sklad_ready_product')
	{
	$num_elements = isset($_GET['num_elements'])?'&num_elements='.$_GET['num_elements']:'';
	$fulllink = '?page=sklad_ready_product'.$num_elements;
	$and = '&p=';
	}
	elseif (isset($_GET['page']) AND $_GET['page'] == 'zayavka')
	{
	$num_elements = isset($_GET['num_elements'])?'&num_elements='.$_GET['num_elements']:'';
	$sort = isset($_GET['sort'])?'&sort='.$_GET['sort']:'';
	$fulllink = '?page=zayavka'.$num_elements.$sort;
	$and = '&p=';
	}
	else
		{
		$fulllink = '';
		$and = '';
		}

//Проверяем нужна ли ссылка "На первую"
  if($p > 3){
    $first_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.'1"><span>1</span></a></div> ';   //или просто $first_page = '<a href="/index.php"><<</a>';
  }
  else{
    $first_page = '';
  }

//Проверяем нужна ли ссылка "На последнюю"
  if($p < ($num_pages - 2)){
    $last_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.$num_pages.'"> <span>'.$num_pages.'</span></a></div> ';
  }
  else{
    $last_page = '';
  }

//Проверяем нужна ли ссылка "На предыдущую"
  if($p > 1){
    $prev_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p - 1).'"> <span> < </span></a></div> ';
  }
  else{
    $prev_page = '';
  }

//Проверяем нужна ли ссылка "На следущую"
  if($p < $num_pages){
    $next_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p + 1).'"> <span>Следующая &rarr; </span></a></div> ';
  }
  else{
    $next_page = '';
  }

//Формируем по 2 страницы до и после текущей (при наличии таковых, конечно):
  if($p - 2 > 0){
    $prev_2_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p - 2).'"><span>'.($p - 2).'</span></a></div> ';
  }
  else{
    $prev_2_page = '';
  }
  if($p - 1 > 0){
    $prev_1_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p - 1).'"><span>'.($p - 1).'</span></a></div> ';
  }
  else{
    $prev_1_page = '';
  }
  if($p + 2 <= $num_pages){
    $next_2_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p + 2).'"><span>'.($p + 2).'</span></a></div> ';
  }
  else{
    $next_2_page = '';
  }
  if($p + 1 <= $num_pages){
    $next_1_page = ' <div class="btn-group"><a class="btn btn-default" href="'.$fulllink.$and.($p + 1).'"><span>'.($p + 1).'</span></a></div> ';
  }
  else{
    $next_1_page = '';
  }
  
  $nav = "<div class='btn-toolbar' role='toolbar'><div class='btn-group'><a class='btn btn-default' href='#'>&uarr;</a></div>".$first_page.$prev_2_page.$prev_1_page."<div class='btn-group btn btn-default active'><span>".$p."</span></div>".$next_1_page.$next_2_page.$next_page."</div>";

  return $nav;
}

function showDate($date) // $date --> время в формате Unix time
{
	$date = ($date > time()) ? time() : $date;
    $stf = 0;
    $cur_time = time();
    $diff = $cur_time - $date;

    $seconds = array('секунда', 'секунды', 'секунд');
    $minutes = array('минута', 'минуты', 'минут');
    $hours = array('час', 'часа', 'часов');
    $days = array('день', 'дня', 'дней');
    $weeks = array('неделя', 'недели', 'недель');
    $months = array('месяц', 'месяца', 'месяцев');
    $years = array('год', 'года', 'лет');
    $decades = array('десятилетие', 'десятилетия', 'десятилетий');

    $phrase = array($seconds, $minutes, $hours, $days, $weeks, $months, $years, $decades);
    $length = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

    for ($i = sizeof($length) - 1; ($i >= 0) && (($no = $diff / $length[$i]) <= 1); $i--) ;
    if ($i < 0) $i = 0;
    $_time = $cur_time - ($diff % $length[$i]);
    $no = floor($no);
    $value = sprintf("%d %s ", $no, getPhrase($no, $phrase[$i]));

    if (($stf == 1) && ($i >= 1) && (($cur_time - $_time) > 0)) $value .= time_ago($_time);

    return $value . ' назад';
}
function getPhrase($number, $titles)
{
    $cases = array (2, 0, 1, 1, 1, 2);
    return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
}

// num2str(878867.15); // восемьсот семьдесят восемь тысяч восемьсот шестьдесят семь рублей 15 копеек
function num2str($num) {
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
        array('копейка' ,'копейки' ,'копеек',	 1),
        array('рубль'   ,'рубля'   ,'рублей'    ,0),
        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
        array('миллион' ,'миллиона','миллионов' ,0),
        array('миллиард','милиарда','миллиардов',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}

function curentDay ($mktime)
{
	$mounts = array ('01' => 'января','02' => 'февраля','03' => 'марта','04' => 'апреля','05' => 'мая','06' => 'июня','07' => 'июля','08' => 'августа','09' => 'сентября','10' => 'октября','11' => 'ноября','12' => 'декабря');

	if (!is_numeric($mktime))
	{
	$mktime = time();
	}

	if (date("Y-m-d",$mktime) == date("Y-m-d",time()))
	{
	$return = 'Сегодня';
	}
	elseif (date("Y-m-d",$mktime) == date("Y-m-d",(time()-86400)))
	{
	$return = 'Вчера';
	}
	else
		{
		$return = date("d",$mktime).' '.$mounts[date("m",$mktime)];
		}
	return $return;
}

function rus_date() {
    $translate = array(
    "am" => "дп",
    "pm" => "пп",
    "AM" => "ДП",
    "PM" => "ПП",
    "Monday" => "Понедельник",
    "Mon" => "Пн",
    "Tuesday" => "Вторник",
    "Tue" => "Вт",
    "Wednesday" => "Среда",
    "Wed" => "Ср",
    "Thursday" => "Четверг",
    "Thu" => "Чт",
    "Friday" => "Пятница",
    "Fri" => "Пт",
    "Saturday" => "Суббота",
    "Sat" => "Сб",
    "Sunday" => "Воскресенье",
    "Sun" => "Вс",
    "January" => "Января",
    "Jan" => "Янв",
    "February" => "Февраля",
    "Feb" => "Фев",
    "March" => "Марта",
    "Mar" => "Мар",
    "April" => "Апреля",
    "Apr" => "Апр",
    "May" => "Мая",
    "May" => "Мая",
    "June" => "Июня",
    "Jun" => "Июн",
    "July" => "Июля",
    "Jul" => "Июл",
    "August" => "Августа",
    "Aug" => "Авг",
    "September" => "Сентября",
    "Sep" => "Сен",
    "October" => "Октября",
    "Oct" => "Окт",
    "November" => "Ноября",
    "Nov" => "Ноя",
    "December" => "Декабря",
    "Dec" => "Дек",
    "st" => "ое",
    "nd" => "ое",
    "rd" => "е",
    "th" => "ое"
    );
    
    if (func_num_args() > 1) {
        $timestamp = func_get_arg(1);
        return strtr(date(func_get_arg(0), $timestamp), $translate);
    } else {
        return strtr(date(func_get_arg(0)), $translate);
    }
}

    function dateInterval ($start, $end = '')
    {
        $start = strtotime($start); 
        $end   = empty($end) ? time() : strtotime($end);
      
        for($d = $start; $d <= $end ; $d = strtotime('tomorrow', $d)) 
            $interval[date('j.n.Y', $d)] = date('j.n.Y', $d);
        
        return isset($interval)?$interval:false;
    }

function curl_exec_follow($ch, &$maxredirect = null) {

  // we emulate a browser here since some websites detect
  // us as a bot and don't let us do our job
  $user_agent = "Mozilla/".rand(5,6).".0 (Windows; U; Windows NT ".rand(5,6).".1; en-US; rv:".rand(1,3).".".rand(1,6).".".rand(1,8).")".
                " Gecko/".rand(2004,2017)."".rand(10,12)."".rand(10,30)." Firefox/1.0";
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent );

  $mr = $maxredirect === null ? 5 : intval($maxredirect);

  if (filter_var(ini_get('open_basedir'), FILTER_VALIDATE_BOOLEAN) === false 
      && filter_var(ini_get('safe_mode'), FILTER_VALIDATE_BOOLEAN) === false
  ) {

    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
    curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  } else {

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    if ($mr > 0)
    {
      $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
      $newurl = $original_url;

      $rch = curl_copy_handle($ch);

      curl_setopt($rch, CURLOPT_HEADER, true);
      curl_setopt($rch, CURLOPT_NOBODY, true);
      curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
      do
      {
        curl_setopt($rch, CURLOPT_URL, $newurl);
        $header = curl_exec($rch);
        if (curl_errno($rch)) {
          $code = 0;
        } else {
          $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
          if ($code == 301 || $code == 302) {
            preg_match('/Location:(.*?)\n/i', $header, $matches);
            $newurl = trim(array_pop($matches));

            // if no scheme is present then the new url is a
            // relative path and thus needs some extra care
            if(!preg_match("/^https?:/i", $newurl)){
              $newurl = $original_url . $newurl;
            }   
          } else {
            $code = 0;
          }
        }
      } while ($code && --$mr);

      curl_close($rch);

      if (!$mr)
      {
        if ($maxredirect === null)
        trigger_error('Too many redirects.', E_USER_WARNING);
        else
        $maxredirect = 0;

        return false;
      }
      curl_setopt($ch, CURLOPT_URL, $newurl);
    }
  }
  return curl_exec($ch);
}

function summa_replace ($s)
{
$s = str_replace(' ','',$s);;
$s = preg_replace ("/[^0-9.,-]/","",$s);
return str_replace(',','.',$s);
}
function summa_replace_plus ($s)
{
$s = preg_replace ("~[^0-9]~"," ",$s);
$s = str_replace(' ','.',$s);
return str_replace('..','.',$s);
}
function check_sklad ($mysql,$tovar_id)	
{
	$getq1 = mysqli_query($mysql,"SELECT * FROM CRM_sklad WHERE tovar_id='". intval($tovar_id) ."' LIMIT 1");
	if(!$getq1) die(trigger_error(mysqli_error($mysql)." in CRM_sklad"));
	$sc=mysqli_fetch_assoc($getq1);
	mysqli_free_result($getq1);
	
	return isset($sc['tovar_id'])?$sc:false;
}
function check_sklad_ready_product ($mysql,$recept_id)	
{
	$getq1 = mysqli_query($mysql,"SELECT * FROM CRM_ready_sklad WHERE recept_id='". intval($recept_id) ."' LIMIT 1");
	if(!$getq1) die(trigger_error(mysqli_error($mysql)." in CRM_sklad"));
	$sc=mysqli_fetch_assoc($getq1);
	mysqli_free_result($getq1);
	
	return isset($sc['recept_id'])?$sc:false;
}

function page_count($mysql,$worker_id,$page)
{
	$time = time();
	$d1 = mktime (0, 0, 0, date('m',$time), date('d',$time), date('Y',$time));
	$d2 = mktime (0, 0, 0, date('m',$time), date('d',$time), date('Y',$time));
	$dates = " WHERE ('".($d1)."' <= CRM_page_count.time AND '".($d2+86399)."' >= CRM_page_count.time)";	
	
	$page = mysqli_real_escape_string($mysql,trim($page));
	$hash = md5(str_replace('/rupka.php','',$page));
	
	$getq = mysqli_query($mysql,"SELECT * FROM CRM_page_count ".$dates." AND worker_id='".$worker_id."' AND hash='".$hash."'  LIMIT 1");
	if(!$getq) die(trigger_error(mysqli_error($mysql)." in ".$SQL));
	$dt=mysqli_fetch_assoc($getq);
	mysqli_free_result($getq);
	
	if (isset($dt['worker_id']))
	{
	$marker = 'UPDATE ';
	$count = $dt['count']+1;
	$queryU = "UPDATE CRM_page_count SET time='".$time."', count='".$count."' ".$dates." AND worker_id='".$worker_id."' AND hash='".$hash."' LIMIT 1";
	mysqli_query($mysql,$queryU) or trigger_error(mysqli_error($mysql)." in ".$queryU);
	}
	else
		{
		$marker = 'INSERT ';
		$count = 1;
		$queryU = "INSERT INTO CRM_page_count (worker_id,hash,page,time,count) VALUES('".$worker_id."','".$hash."','".$page."','".$time."','".$count."')";
		mysqli_query($mysql,$queryU) or die(mysql_error($mysql));
		}
		
	$getq = mysqli_query($mysql,"SELECT * FROM CRM_online_user WHERE worker_id='".$worker_id."' LIMIT 1");
	if(!$getq) die(trigger_error(mysqli_error($mysql)." in ".$SQL));
	$ds=mysqli_fetch_assoc($getq);
	mysqli_free_result($getq);
	
	if (isset($ds['worker_id']))
	{
	$queryU = "UPDATE CRM_online_user SET page='".$page."', time='".$time."' WHERE worker_id='".$worker_id."' LIMIT 1";
	mysqli_query($mysql,$queryU) or trigger_error(mysqli_error($mysql)." in ".$queryU);
	}
	else
		{
		$queryU = "INSERT INTO CRM_online_user (worker_id,page,time) VALUES('".$worker_id."','".$page."','".$time."')";
		mysqli_query($mysql,$queryU) or die(mysql_error($mysql));
		}
		
return $count;
}

function str_num ($num)
{
	$num = is_numeric($num) ? $num : 0;
	if ($num > 50)
	{
	return 'очень много';
	}
	elseif ($num > 10)
	{
	return 'много';
	}
	elseif ($num > 5)
	{
	return 'мало';
	}
	elseif ($num > 0)
	{
	return 'очень мало';
	}
	elseif ($num == 0)
	{
	return 'нет';
	}
	elseif ($num < 0)
	{
	return 'в минусе';
	}
}
function getExtension1($filename) 
{
$e = explode(".", $filename);
return end($e);
}
function translit ($t)
{
	$t = trim($t);
	$t = strtolower($t);
	$t = str_replace('.','',$t);
	$t = str_replace(',','',$t);
	$t = str_replace('?','',$t);
	$t = str_replace(':','',$t);
	$t = str_replace('<','',$t);
	$t = str_replace('>','',$t);
	$t = str_replace('=','',$t);
	$t = str_replace('"','',$t);
	$t = str_replace('\'','',$t);
	$t = str_replace('/','',$t);
	$t = str_replace("\\",'',$t);
	$t = str_replace('  ','_',$t);
	$t = str_replace(' ','_',$t);
	$t = str_replace('-','_',$t);
	$t = str_replace('(','',$t);
	$t = str_replace(')','',$t);

	$t = str_replace('а','a',$t);
	$t = str_replace('б','b',$t);
	$t = str_replace('в','v',$t);
	$t = str_replace('г','g',$t);
	$t = str_replace('д','d',$t);
	$t = str_replace('е','e',$t);
	$t = str_replace('ё','e',$t);
	$t = str_replace('ж','j',$t);
	$t = str_replace('з','z',$t);
	$t = str_replace('и','i',$t);
	$t = str_replace('й','y',$t);
	$t = str_replace('к','k',$t);
	$t = str_replace('л','l',$t);
	$t = str_replace('м','m',$t);
	$t = str_replace('н','n',$t);
	$t = str_replace('о','o',$t);
	$t = str_replace('п','p',$t);
	$t = str_replace('р','r',$t);
	$t = str_replace('с','s',$t);
	$t = str_replace('т','t',$t);
	$t = str_replace('у','u',$t);
	$t = str_replace('ф','f',$t);
	$t = str_replace('х','h',$t);
	$t = str_replace('ц','c',$t);
	$t = str_replace('ч','ch',$t);
	$t = str_replace('ш','sh',$t);
	$t = str_replace('щ','shch',$t);
	$t = str_replace('ъ','',$t);
	$t = str_replace('ы','y',$t);
	$t = str_replace('ь','',$t);
	$t = str_replace('э','e',$t);
	$t = str_replace('ю','yu',$t);
	$t = str_replace('я','ya',$t);

	$t = str_replace('А','a',$t);
	$t = str_replace('Б','b',$t);
	$t = str_replace('В','v',$t);
	$t = str_replace('Г','g',$t);
	$t = str_replace('Д','d',$t);
	$t = str_replace('Е','e',$t);
	$t = str_replace('Ё','e',$t);
	$t = str_replace('Ж','j',$t);
	$t = str_replace('З','z',$t);
	$t = str_replace('И','i',$t);
	$t = str_replace('Й','y',$t);
	$t = str_replace('К','k',$t);
	$t = str_replace('Л','l',$t);
	$t = str_replace('М','m',$t);
	$t = str_replace('Н','n',$t);
	$t = str_replace('О','o',$t);
	$t = str_replace('П','p',$t);
	$t = str_replace('Р','r',$t);
	$t = str_replace('С','s',$t);
	$t = str_replace('Т','t',$t);
	$t = str_replace('У','u',$t);
	$t = str_replace('Ф','f',$t);
	$t = str_replace('Х','h',$t);
	$t = str_replace('Ц','c',$t);
	$t = str_replace('Ч','ch',$t);
	$t = str_replace('Ш','sh',$t);
	$t = str_replace('Щ','shch',$t);
	$t = str_replace('Ъ','',$t);
	$t = str_replace('Ы','y',$t);
	$t = str_replace('Ь','',$t);
	$t = str_replace('Э','e',$t);
	$t = str_replace('Ю','yu',$t);
	$t = str_replace('Я','ya',$t);

	$t = preg_replace('/[^0-9a-zA-Z_]/', '', $t);
	$t = trim($t);
	return $t;
}

function sendrequest($url,$post = 0){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url ); // отправляем на
  curl_setopt($ch, CURLOPT_HEADER, 1); // пустые заголовки
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // следовать за редиректами
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEJAR, ABSOLUTE__PATH__.'/programm_files/tmp/cookie.txt'); // сохранять куки в файл
  curl_setopt($ch, CURLOPT_COOKIEFILE,  ABSOLUTE__PATH__.'/programm_files/tmp/cookie.txt');
  curl_setopt($ch, CURLOPT_POST, $post!==0 ); // использовать данные в post
  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
function search_text ($text,$search)	
{
	if (empty($search))
	{
	return $text;
	}
	$search = trim($search);
	$text = mb_strtolower($text,'UTF-8');
	$search = mb_strtolower($search,'UTF-8');
	$pattern = "/((?:^|>)[^<]*)(".$search.")/si";
	$replace = '$1<b style="color:#FF0000; background:#FFFF00;">$2</b>';
	return preg_replace($pattern, $replace, $text);
}

function get_database_tables($mysql)
{
	$ret = array();
	$r = mysqli_query($mysql,"SHOW TABLES");
	if (mysqli_num_rows($r)>0)
	{
		while($row = mysqli_fetch_array($r, MYSQLI_NUM))
		{
			$ret[] = $row[0];
		}
	}
	return $ret;
}
function format_size($size){
    
    $mod = 1024;
    $units = array('Б', 'КБ', 'МБ', 'ГБ', 'ТБ', 'ПБ');   
    for ($i = 0; $size > $mod; $i++)   
        $size /= $mod;

    return round($size, 2) . " " . $units[$i];
}
function restoreDB ($pathANDfile)
{
	global $config;
	$command = 'mysql -u' . $config['db_username'] . ' -p' . $config['db_password'] . ' -h' . $config['db_hostname'] . ' --default-character-set=utf8 --force ' . $config['db_name'] . ' < '.$pathANDfile;
	$return = shell_exec($command);
	return $return;
}
function backupDB($backup_folder, $backup_name, $array=false)
{
    global $config;
    $fullFileName = $backup_folder . '/' . $backup_name . '.sql.gz';
    $command = 'mysqldump -h' . $config['db_hostname'] . ' -u' . $config['db_username'] . ' -p' . $config['db_password'] . ' ' . $config['db_name'] . ' '.( $array!==false ? implode(' ',$array) : '' ).' | gzip -c > ' . $fullFileName;
    shell_exec($command);
    return $fullFileName;
}
function deletefiledayDB ($dir_backup,$delday)
{
	$scanned_directory = array_diff(scandir($dir_backup), array('..', '.', '.htaccess','point'));
	foreach ($scanned_directory as $file)
	{
		if (file_exists($dir_backup.'/'.$file) AND getExtension1($file) == 'gz' AND (time() - filemtime($dir_backup.'/'.$file) > (86400*$delday) ))
		{
		unlink($dir_backup.'/'.$file);
		}
	}
	return;
}
function deletefilecntDB ($dir_backup,$delcnt)
{
	$scanned_directory = array_diff(scandir($dir_backup), array('..', '.', '.htaccess','point'));
	$scanned_directory = array_reverse($scanned_directory);
	$h=0;
	foreach ($scanned_directory as $file)
	{
		if (file_exists($dir_backup.'/'.$file) AND getExtension1($file) == 'gz' AND ($h>=$delcnt))
		{
		unlink($dir_backup.'/'.$file);
		}
	$h++;
	}
	return;
}
function request($url)
{
 $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url );
  curl_setopt($ch, CURLOPT_HEADER, 0); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
function tel_replace($tel)
{
	$tel = preg_replace('/[^0-9]/', '', $tel);
	$tel = mb_substr($tel,0,1) == '8' ? '7'.mb_substr($tel, 1) : (mb_substr($tel,0,1) == '9'?'7'.$tel:$tel);
	return $tel;
	
}

function resizer_image($file_input, $w_o, $h_o){
	list($w_i, $h_i, $type) = getimagesize($file_input);
	if (!$w_i || !$h_i)
	{
		echo 'Невозможно получить длину и ширину изображения '.$file_input.'<br>';
		return;
	}
	$types = array('','gif','jpeg','png');
	$ext = $types[$type];
	if ($ext)
	{
		shell_exec ('mogrify -resize '.$w_o.'x'.$h_o.' '.$file_input);
	}
}

function look($zn) 
{
	echo '<pre>';
		print_r($zn);
	echo '</pre>';
}

function qr_code ($link,$HTTP_HOST,$size=4)
{
    $PNG_TEMP_DIR = ABSOLUTE__PATH__.'/qrcode';
    include_once (ABSOLUTE__PATH__.'/programm_files/inc/phpqrcode-master/qrlib.php');    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
    {
    mkdir($PNG_TEMP_DIR);
    }
    
    $errorCorrectionLevel = 'L';
    $matrixPointSize = is_numeric($size)?$size:4;
    $filename = $PNG_TEMP_DIR.'/QRCODE_'.md5($link.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
    
	QRcode::png($link, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
	
	return 'http://'.$HTTP_HOST.'/qrcode/'.basename($filename); 
}

function vk_linko ($link)
{
    $e = explode('vk.com/',$link);
    
    return 'https://vk.com/'.(isset($e[1])?$e[1]:$e[0]);
}

function message_s ($h1,$message,$link_OK,$link_CANCEL,$color)
{
	?>
	<div class="clearfix mtop"></div>
	<div class="alert alert-<?php echo $color; ?> col-lg-6 col-lg-offset-3">
		<h3 class="text-center text-<?php echo $color; ?>"><?php echo $h1; ?></h3>
		<strong><?php echo $message; ?></strong><br /> 
		
		<p class="text-center">
			<?php echo $link_OK.$link_CANCEL; ?>
		</p>
	</div>
	
	<div class="clearfix mtop"></div>
	<?php
	return;
}
?>
