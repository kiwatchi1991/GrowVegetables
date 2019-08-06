<?php 

ini_set('log_errors','on');//ログを取るか
ini_set('error_log','php.log');//ログの出力ファイルを指定
session_start();//セッションを使う

//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}


//インスタンス格納用
$vegetables = array();
$soils = array();
$weather = array();

//抽象クラス（生育要素クラス（野菜・土のもとになる））
abstract class GrowthElements{
  protected $name;

  public function setName($str){
    $this->name = $str;
  }
  public function getName(){
    return $this->name;
  }
 }
//=================================
//野菜クラス
//=================================
class Vegetable extends GrowthElements{
  
  public function __construct($name){
      $this->name = $name;
  }
}

//=================================
//土クラス
//=================================
class Soil extends GrowthElements{
  //プロパティ
  protected $water;
  protected $n;
  protected $p;
  protected $k;
  protected $Ca;
  protected $solar;

  public function __construct($name, $water, $n, $p, $k, $Ca, $solar){
    $this->name = $name;
    $this->water = $water;
    $this->n = $n;
    $this->p = $p;
    $this->k = $k;
    $this->Ca = $Ca;
    $this->solar = $solar;
  }
  public function setWater($num){
    $this->water = $num;
  }
  public function getWater(){
    return $this->water;
  }
  public function setN($num){
    $this->n = $num;
  }
  public function getN(){
    return $this->n;
  }
  public function setP($num){
    $this->p = $num;
  }
  public function getP(){
    return $this->p;
  }
  public function setK($num){
    $this->k = $num;
  }
  public function getK(){
    return $this->k;
  }
  public function setCa($num){
    $this->Ca = $num;
  }
  public function getCa(){
    return $this->Ca;
  }
  public function setSolar($num){
    $this->solar = $num;
  }
  public function getSolar(){
    return $this->solar;
  }

  public function doWater($targetObj){
    $actionPoint = 30;
    $targetObj->setWater($targetObj->getWater()+$actionPoint);
    History::set('①　'.$_SESSION['vegetable']->getName().'に水やりしました');
  }
  

  
  public function fertilizeAll($targetObj){
    $actionPoint = 10;
    $targetObj->setN($targetObj->getN()+$actionPoint);
    $targetObj->setP($targetObj->getP()+$actionPoint);
    $targetObj->setK($targetObj->getK()+$actionPoint);
    History::set('①　'.$_SESSION['vegetable']->getName().'にバランス肥料を与えました');
  }

  public function fertilizeN($targetObj){
    $actionPoint = 10;
    $targetObj->setN($targetObj->getN()+$actionPoint);
    History::set('①　'.$_SESSION['vegetable']->getName().'に　ちっそ肥料を与えました');
  }
  public function fertilizeP($targetObj){
    $actionPoint = 10;
    $targetObj->setP($targetObj->getP()+$actionPoint);
    History::set('①　'.$_SESSION['vegetable']->getName().'に　りん肥料を与えました');
  }
  public function fertilizeK($targetObj){
    $actionPoint = 10;
    $targetObj->setK($targetObj->getK()+$actionPoint);
    History::set('①　'.$_SESSION['vegetable']->getName().'に　かりうむ肥料を与えました');
  }
  public function fertilizeCa($targetObj){
    $actionPoint = 10;
    $targetObj->setCa($targetObj->getCa()+$actionPoint);
    History::set('①　'.$_SESSION['vegetable']->getName().'に　カルシウム肥料を与えました');
  }

  public function rain($targetObj){
    $actionPoint = 10;
    $targetObj->setWater($targetObj->getWater()+$actionPoint);
    History::set('②　雨です');
  }
  
  public function shineOn($targetObj){
    $actionPoint = 10;
    $targetObj->setSolar($targetObj->getSolar()+$actionPoint);
    History::set($_SESSION['vegetable']->getName().'②　晴れです');
  }


}
  
//=================================
//天気クラス
//=================================
class Weather extends GrowthElements{
  protected $water;
  protected $solar;
  
  public function __construct($name, $water, $solar){
    $this->name = $name;
    $this->water = $water;
    $this->solar = $solar;
  }
}

  //=================================
  //履歴管理クラス
  //=================================
interface HistoryInterface{
  public static function set($str);
  public static function clear();
}

class History implements HistoryInterface{
  public static function set($str){
    //セッションhistoryが作られてなければ作る
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    //文字列をセッションhistoryへ格納
    $_SESSION['history'] =$_SESSION['dayCount'].'日目: '.$str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}


//インスタンス生成
$vegetables[] = new Vegetable('トマト');
$vegetables[] = new Vegetable('キュウリ');
$vegetables[] = new Vegetable('キャベツ');
$soils[] = new Soil('土A',50,50,50,50,10,0);
$soils[] = new Soil('土B',30,30,30,30,10,0);
$soils[] = new Soil('土C',10,10,10,10,10,0);
$weathers[] = new Weather('晴れ',0,10);
$weathers[] = new Weather('雨',30,0);

function createVegetable(){
  global $vegetables;
  $vegetable = $vegetables[0];
  History::set($vegetable->getName().'を育てよう！');
  $_SESSION['vegetable'] = $vegetable;
}
function createSoil(){
  global $soils;
  $soil = $soils[0];
  History::set($soil->getName().'を育てよう！');
  $_SESSION['soil'] = $soil;
}
function resetWeather(){
  global $weathers;
  $weather = $weathers[mt_rand(0,1)];
  $_SESSION['weather'] = $weather;
}

function setWeather(){
  global $weathers;
  if($_POST['weather'] === 'fine'){
    $weather = $weathers[0];
  }elseif($_POST['weather'] === 'rain'){
    $weather = $weathers[1];
  }
  $_SESSION['weather'] = $weather;
}


function init(){
  History::clear();
  // History::set('初期化します');
  $_SESSION['dayCount'] = 0;
  createVegetable();
  createSoil();
  resetWeather();
  $remainDays = 11 - $_SESSION['dayCount'];
//  $resultFlg ;
}


//1.post送信されていた場合
if(empty($_POST)){
  $restartFlg = 1;
  $choiceGameFlg = 0;
  $resultFlg = 0;
}else{
  $changeFlg = (!empty($_POST['change'])) ? true : false;
  $choiceGameFlg = (!empty($_POST['choice'])) ? true : false;
  $startFlg = (!empty($_POST['start'])) ? true : false;
  $restartFlg = (!empty($_POST['restart'])) ? true : false;
//  $resultFlg = ($_SESSION['dayCount'] >= 6) ? true : false;
  $resultFlg = ($_SESSION['dayCount'] >= 11)? true : false;

  //リスタートボタンを押した場合
    if($restartFlg){
      History::clear();
      $_SESSION['dayCount'] = 0;
    }else{
    
    //スタート直後（$_POST['weather']がない場合）は、エラーになるのでsetWeatherしない。
    if(!empty($_POST['weather'])){
    setWeather();
    }
  
    //デバッグ関数
    error_log('POSTされた！');
    debug('restartFlg' .print_r($restartFlg,true));
    debug('post内容' .print_r($_POST,true));
    debug('セッション内容' .print_r($_SESSION,true));

    //ゲームスタートした場合
    if($startFlg){
      // History::set('ゲームスタート！');
      init();
      
    }elseif($_SESSION['dayCount'] >= 11)
      
      {
        $resultFlg = true;
      }
     else{
       //送信した場合
       //お世話アクション
      switch($_POST['action']){
        case 'water':
          $_SESSION['soil']->doWater($_SESSION['soil']);
        break;
        case 'n':
          $_SESSION['soil']->fertilizeN($_SESSION['soil']);
          break;
        case 'p':
          $_SESSION['soil']->fertilizeP($_SESSION['soil']);
          break;
        case 'k':
          $_SESSION['soil']->fertilizeK($_SESSION['soil']);
          break;
        case 'Ca':
          $_SESSION['soil']->fertilizeCa($_SESSION['soil']);
          break;
        case 'allFtl':
          $_SESSION['soil']->fertilizeAll($_SESSION['soil']);
          break;
        }
    //天気のアクション
    switch($_SESSION['weather']->getName()){
      case '晴れ':
        $_SESSION['soil']->shineOn($_SESSION['soil']);
      break;
      case '雨':
        $_SESSION['soil']->rain($_SESSION['soil']);
      break;
         }
    
      }
  
    //日数カウント
      $_SESSION['dayCount'] = $_SESSION['dayCount']+1;
  }
  
  
  
  
  if($_SESSION['dayCount'] >= 11){
    $resultFlg = true;
  }
  
  //栽培レベル判断
  
  $_SESSION['growLevel'] = 1;
  $waterLevel = $_SESSION['soil']->getWater();
  $nLevel = $_SESSION['soil']->getN();
  $pLevel = $_SESSION['soil']->getP();
  $kLevel = $_SESSION['soil']->getK();
  $CaLevel = $_SESSION['soil']->getCa();
  $soLevel = $_SESSION['soil']->getSolar();
  

  //レベル３
  if($waterLevel > 270 && $nLevel >60 && $pLevel > 60 && $kLevel > 60 && $CaLevel > 20 && $soLevel > 40){
    $_SESSION['growLevel'] = 3;
  }
  //レベル２
  elseif($waterLevel > 50 && $nLevel >50 && $pLevel > 50 && $kLevel > 50 && $CaLevel > 10 && $soLevel > 20){
    $_SESSION['growLevel'] = 2;
  }
  
}
  



?>


<!DOCTYPE html>
<html lang="en">



<head>
  <meta charset="UTF-8">
  <title>Grow tomatoes</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
  <div id="contents" class="site-width">

    <!--   初期画面-->
    <?php if($restartFlg){ ?>
    <div id="start" class="wrap">
     
      <div class="title-logo">
        <img src="img/cooltext330609163954278.png" alt="">
      </div>
      
      <div class="main-img">
        <img src="img/sozai_image_68166.png" alt="">
      </div>
      
      <form method="post">
       <button type="submit" name="choice" value="はじめる">はじめる</button>
<!--        <input type="submit" name="start" value="▶️スタート" height="50">-->
      </form>
    
    </div>
    <!-- ゲーム選択画面 -->
    <?php }else if($choiceGameFlg){ ?>
      はい

      <form method="post">
      <div class="action">
       <p>①水やりするか、肥料をやるか、ひとつえらんでね！</p>
        <input type="radio" id="water" class="button" name="action" value="water" checked="checked">
          <label class="radio-inline__label" for="water">水</label>
        <input type="radio" id="n" class="button" name="action" value="n">
          <label class="radio-inline__label" for="n">ちっそ</label>


      <form method="post">
      <button type="submit" name="choice" value="はじめる">はじめる</button>
      </form>

    <!--    結果発表画面-->

    <?php }else if($resultFlg){ ?>
    <div id="result" class="wrap">
      <h1>結果はっぴょ〜〜！</h1>
      <h2>栽培ステージ：<?php echo $_SESSION['growLevel']; ?></h2>
      <div class="main-img">
        <img src="
         <?php if($_SESSION['growLevel'] === 1){echo 'img/level1.png';}
              elseif($_SESSION['growLevel'] === 2){echo 'img/level2.png';}
              elseif($_SESSION['growLevel'] === 3){echo 'img/level3.png';}
          ?>" alt="">
      </div>
      <div class="button-restart">
         <form method="post">
           <input type="submit" name="restart" value="リスタート">
         </form>
      </div>
    </div>



    <!--    通常画面-->
    <?php }else{ ?>
  <div id="main" class="wrap">
    <div class="title-logo">
      <img src="img/cooltext330609163954278.png" alt="">
      <form method="post">
        <div class="btn-restart">
      <button type="submit" name="restart" value="リスタート">リスタート</button>
      </div>
      </form>
    </div>
    <div class="status">
      <span>今日は<?php echo $_SESSION['dayCount']; ?>日目です。（全10日間）</span>
      <span>
        現在の栽培ステージ：<?php echo $_SESSION['growLevel']; ?></span>
    </div>
      

<!--　ステータス非表示
       <div class="status">
      <p>水:<?php echo $_SESSION['soil']->getWater(); ?> </p>
      <p>N:<?php echo $_SESSION['soil']->getN(); ?></p>
      <p>P:<?php echo $_SESSION['soil']->getP(); ?></p>
      <p>K:<?php echo $_SESSION['soil']->getK(); ?></p>
      <p>Ca:<?php echo $_SESSION['soil']->getCa(); ?></p>
      <p>日光:<?php echo $_SESSION['soil']->getSolar(); ?></p>

      <p>天気:<?php echo $_SESSION['weather']->getName(); ?></p>
    </div>
-->

<!-- アクションボタン -->
    <form method="post">
      <div class="action">
       <p>①水やりするか、肥料をやるか、ひとつえらんでね！</p>
        <input type="radio" id="water" class="button" name="action" value="water" checked="checked">
          <label class="radio-inline__label" for="water">水</label>
        <input type="radio" id="n" class="button" name="action" value="n">
          <label class="radio-inline__label" for="n">ちっそ</label>

        <input type="radio" id="p" class="button" name="action" value="p">
          <label class="radio-inline__label" for="p">りん</label>

        <input type="radio" id="k" class="button"  name="action" value="k">
          <label class="radio-inline__label" for="k">かりうむ</label>

        <input type="radio" id="allFtl" class="button"  name="action" value="allFtl">
          <label class="radio-inline__label" for="allFtl">バランス肥料</label>

        <input type="radio" id="Ca" class="button"  name="action" value="Ca">
          <label class="radio-inline__label" for="Ca">かるしうむ</label>

      </div>
      <!-- 天気ボタン -->
      <div class="weather">
        <p>②明日の天気をえらんでね！（自由にえらべます）</p>
        <input type="radio" id="fine" class="button" name="weather" value="fine" <?php echo ($_SESSION['weather']->getName() == '晴れ') ? 'checked' : ''; ?>>
          <label class="radio-inline__label" for="fine">はれ</label>
        <input type="radio" id="rain" class="button" name="weather" value="rain" <?php echo ($_SESSION['weather']->getName() == '雨') ? 'checked' : ''; ?>>
          <label class="radio-inline__label" for="rain">あめ</label>

        <?php
        debug('天気セッション！！！' .print_r($_SESSION['weather']->getName(),true));
        ?>
      </div>
<!-- 送信ボタン -->
      <div class="submit-button">
        <button type="submit" name="" value="実行">︎︎︎︎▶︎▷▶︎︎︎︎︎　実行</button>
      </div>
      <div class="img-history">
        <div class="main-img">
          <img src="<?php if($_SESSION['growLevel'] === 1){echo 'img/level1.png';}
                elseif($_SESSION['growLevel'] === 2){echo 'img/level2.png';}
                elseif($_SESSION['growLevel'] === 3){echo 'img/level3.png';}
                  ?>" alt="">
        </div>

        <div class="history">
          <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
        </div>
      </div>
    </form>
    
  </div>

    <?php } ?>


  </div>
>


</body>

</html>