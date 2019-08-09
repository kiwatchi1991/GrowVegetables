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
    History::set('①　水やりしました');
  }
  

  
  public function fertilizeAll($targetObj){
    $actionPoint = 10;
    $targetObj->setN($targetObj->getN()+$actionPoint);
    $targetObj->setP($targetObj->getP()+$actionPoint);
    $targetObj->setK($targetObj->getK()+$actionPoint);
    History::set('①　バランス肥料を与えました');
  }

  public function fertilizeN($targetObj){
    $actionPoint = 30;
    $targetObj->setN($targetObj->getN()+$actionPoint);
    History::set('①　ちっそ肥料を与えました');
  }
  public function fertilizeP($targetObj){
    $actionPoint = 30;
    $targetObj->setP($targetObj->getP()+$actionPoint);
    History::set('①　りん肥料を与えました');
  }
  public function fertilizeK($targetObj){
    $actionPoint = 30;
    $targetObj->setK($targetObj->getK()+$actionPoint);
    History::set('①　かりうむ肥料を与えました');
  }
  public function fertilizeCa($targetObj){
    $actionPoint = 10;
    $targetObj->setCa($targetObj->getCa()+$actionPoint);
    History::set('①　カルシウム肥料を与えました');
  }

  public function rain($targetObj){
    $actionPoint = 10;
    $targetObj->setWater($targetObj->getWater()+$actionPoint);
    History::set('②　天気：　雨です');
  }
  
  public function shineOn($targetObj){
    $actionPoint = 10;
    $targetObj->setSolar($targetObj->getSolar()+$actionPoint);
    History::set('②　天気：　晴れです');
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
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}



//インスタンス生成
$vegetables[] = new Vegetable('トマト');
$vegetables[] = new Vegetable('イモ');
$vegetables[] = new Vegetable('エダマメ');
$soils[] = new Soil('土A',50,50,50,50,30,20);
$soils[] = new Soil('土B',30,30,30,30,20,10);
$soils[] = new Soil('土C',10,10,10,10,10,0);
$weathers[] = new Weather('晴れ',0,10);
$weathers[] = new Weather('雨',30,0);

function createVegetable($num){
  global $vegetables;
  $vegetable = $vegetables[$num];
  History::set('「'.$vegetable->getName().'」　を育てよう！');
  $_SESSION['vegetable'] = $vegetable;
}
function createSoil($num){
  global $soils;
  $soil = $soils[$num];
  History::set('「'.$soil->getName().'」　をえらびました！');
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
  createVegetable($_SESSION['choiceVeg']);
  createSoil($_SESSION['choiceSoil']);
  resetWeather();
  $remainDays = 11 - $_SESSION['dayCount'];
//  $resultFlg ;
}





//1.post送信がない場合
if(empty($_POST)){
  $restartFlg = 1;
  $choiceGameFlg = 0;
  $resultFlg = 0;
}else{

  //2.post送信されていた場合

    //ヒストリーをリセット
  function historyClear(){
     History::clear();
  };

  historyClear();


  $changeFlg = (!empty($_POST['change'])) ? true : false;
  $choiceGameFlg = (!empty($_POST['choice'])) ? true : false;
  $startFlg = (!empty($_POST['choiceVeg'])) ? true : false;
  $restartFlg = (!empty($_POST['restart'])) ? true : false;
  $resultFlg = ($_SESSION['dayCount'] >= 11)? true : false;
  $actionFlg = (!empty($_POST['action'])) ? true : false;

  //リスタートボタンを押した場合
    if($restartFlg){
      History::clear();
      $_SESSION['dayCount'] = 0;
    }else{
    

    //ゲーム選択画面
      if($startFlg){
    switch($_POST['choiceVeg']){
      case 'tomato':
      $_SESSION['choiceVeg'] = 0;
      break;
      case 'sweet_potato':
      $_SESSION['choiceVeg'] = 1;
      break;
      case 'edamame':
      $_SESSION['choiceVeg'] = 2;
      break;
    }

    switch($_POST['choiceSoil']){
      case 'soilA':
      $_SESSION['choiceSoil'] = 0;
      break;
      case 'soilB':
      $_SESSION['choiceSoil'] = 1;
      break;
      case 'soilC':
      $_SESSION['choiceSoil'] = 2;
      break;
    }

  }
    
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
     elseif($actionFlg){
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
  
  $vegName = $_SESSION['vegetable']->getName();
  $soilName = $_SESSION['soil']->getName();

//①トマトの場合
if($vegName === 'トマト'){

    // 土Aの場合
  if($soilName === '土A'){
      //レベル３
    if($waterLevel >= 90 && $nLevel >= 60 && $pLevel >= 60 && $kLevel >= 60 && $CaLevel >= 40 && $soLevel >= 30){
    $_SESSION['growLevel'] = 3;
    }
     //レベル２
    elseif($waterLevel >= 90 && $nLevel >= 50 && $pLevel >= 50 && $kLevel >= 50 && $CaLevel >= 30 && $soLevel >= 20){
    $_SESSION['growLevel'] = 2;
    }

    // 土Bの場合  
  }elseif($soilName === "土B"){
     //レベル３
     if($waterLevel >= 150 && $nLevel >= 40 && $pLevel >= 40 && $kLevel >= 40 && $CaLevel >= 30 && $soLevel >= 40){
      $_SESSION['growLevel'] = 3;
      }
       //レベル２
      elseif($waterLevel >= 70 && $nLevel >= 30 && $pLevel >= 30 && $kLevel >= 30 && $CaLevel >= 20 && $soLevel >= 20){
      $_SESSION['growLevel'] = 2;
      }

      //土Cの場合
  }elseif($soilName === '土C'){
      //レベル３
      if($waterLevel >= 170 && $nLevel >= 30 && $pLevel >= 30 && $kLevel >= 30 && $CaLevel >= 30 && $soLevel >= 40){
        $_SESSION['growLevel'] = 3;
        }
         //レベル２
        elseif($waterLevel >= 90 && $nLevel >= 20 && $pLevel >= 20 && $kLevel >= 20 && $CaLevel >= 10 && $soLevel >= 20){
        $_SESSION['growLevel'] = 2;
        }
  
  }

  //②イモ、③エダマメの場合
}else{

  // 土Aの場合
  if($soilName === '土A'){
    //レベル３
  if($waterLevel >= 90 && $nLevel >= 50 && $pLevel >= 60 && $kLevel >= 60 && $CaLevel >= 30 && $soLevel >= 30){
  $_SESSION['growLevel'] = 3;
  }
   //レベル２
  elseif($waterLevel >= 90 && $nLevel >= 50 && $pLevel >= 50 && $kLevel >= 50 && $CaLevel >= 30 && $soLevel >= 20){
  $_SESSION['growLevel'] = 2;
  }

  // 土Bの場合  
}elseif($soilName === "土B"){
   //レベル３
   if($waterLevel >= 120 && $nLevel >= 40 && $pLevel >= 50 && $kLevel >= 50 && $CaLevel >= 20 && $soLevel >= 40){
    $_SESSION['growLevel'] = 3;
    }
     //レベル２
    elseif($waterLevel >= 70 && $nLevel >= 30 && $pLevel >= 30 && $kLevel >= 30 && $CaLevel >= 20 && $soLevel >= 20){
    $_SESSION['growLevel'] = 2;
    }

    //土Cの場合
}elseif($soilName === '土C'){
    //レベル３
    if($waterLevel >= 140 && $nLevel >= 30 && $pLevel >= 90 && $kLevel >= 90 && $CaLevel >= 10 && $soLevel >= 40){
      $_SESSION['growLevel'] = 3;
      }
       //レベル２
      elseif($waterLevel >= 90 && $nLevel >= 20 && $pLevel >= 30 && $kLevel >= 30 && $CaLevel >= 10 && $soLevel >= 20){
      $_SESSION['growLevel'] = 2;
      }

}

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

    <!--   ==============================
    初期画面
  ==============================-->
    <?php if($restartFlg){ ?>
    <div id="start" class="wrap">
     
      <div class="title-logo">
        <img src="img/top_logo.png" alt="">
      </div>
      
      <div class="main-img">
        <img src="img/title_img.png" alt="">
      </div>
      
      <form method="post">
       <button type="submit" name="choice" value="choice">はじめる</button>
      </form>
    
    </div>

<!--  ==============================
    結果発表画面
  ==============================-->

  <?php }else if($resultFlg){ ?>
  <div id="result" class="wrap">
      <h1>結果はっぴょ〜〜！</h1>
      <h2>栽培ステージ：<?php echo $_SESSION['growLevel']; ?></h2>
      
      <div class="main-img">
          <img src="<?php echo 'img/'.$_SESSION['vegetable']->getName().''.$_SESSION['growLevel'].'.png'; ?>" alt="">
      </div>

    <div class="container">
      <div class="comment">
          <p><?php 
          if($_SESSION['growLevel'] === 1){
            echo "今年は収穫ゼロです。。。。";
          }elseif($_SESSION['growLevel'] === 2){
            echo "生育不十分でした...惜しい！";
          }elseif($_SESSION['growLevel'] === 3){
            echo "おめでとう！<br>たくさん収穫できました♬";
          }
          ?></p>
      </div>

      <div class="btn-restart">
        <form method="post">
        <button type="submit" name="restart" value="リスタート">リスタート</button>
        </form>
      </div>
    </div>
  </div>



 <!-- ==============================
    ゲーム　通常画面
  =================================--->
 <?php }else if($startFlg || (!empty($_POST['action']))){ ?>
  <div id="main" class="wrap">
    <div class="title-logo">
      <img src="img/top_logo.png" alt="">
      <form method="post">
        <div class="btn-restart">
      <button type="submit" name="restart" value="リスタート">リスタート</button>
      </div>
      </form>
    </div>
    <div class="status">
      <span>今日は<?php echo $_SESSION['dayCount']; ?>日目です。(全10日間)　</span>
      <span>
        現在の栽培ステージ：</span><span class="grow-stage"><?php echo $_SESSION['growLevel']; ?></span>
    </div>
    

<!-- 　ステータス（非表示）
       <div class="status">
      <p>水:<?php echo $_SESSION['soil']->getWater(); ?> </p>
      <p>N:<?php echo $_SESSION['soil']->getN(); ?></p>
      <p>P:<?php echo $_SESSION['soil']->getP(); ?></p>
      <p>K:<?php echo $_SESSION['soil']->getK(); ?></p>
      <p>Ca:<?php echo $_SESSION['soil']->getCa(); ?></p>
      <p>日光:<?php echo $_SESSION['soil']->getSolar(); ?></p>

      <p>天気:<?php echo $_SESSION['weather']->getName(); ?></p>
    </div> -->


<!-- アクションボタン -->
<!-- <?php
     var_dump($choiceGameFlg);
     var_dump($startFlg);
     var_dump($_SESSION['vegetable']->getName());
    ?> -->

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
                <img src="<?php echo 'img/'.$_SESSION['vegetable']->getName().''.$_SESSION['growLevel'].'.png'; ?>" alt="">
        </div>

        <div class="weather-img">
          <img src="<?php echo 'img/'.$_SESSION['weather']->getName().'.png'; ?>" alt="">
        </div>

        <div class="history">
          <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
        </div>
      </div>
    </form>
    
  </div>

    <!-- ==============================
    ゲーム選択画面
  ============================== -->
    <?php }else if($choiceGameFlg){ ?>
    <div id="choice" class="wrap">

    <div class="title-logo">
      <img src="img/top_logo.png" alt="">
    </div>

    <form method="post">
      <div class="choice-veg">
       <h2>①どの野菜を育てるか、ひとつえらんでね！</h2>
       　<div class="img-container">
         <h3>- トマト -</h3>
         <!-- <p>< 注意 ></p>
         <p>尻腐れ病に<br>気をつけろ！</p> -->
          <input type="radio" id="tomato" class="button" name="choiceVeg" value="tomato" checked="checked">
          <label class="radio-inline__label" for="tomato"><img src="img/トマトimg.png" alt=""></label>
        </div>

       　<div class="img-container">
         <h3>- さつまいも -</h3>
         <!-- <p>< 注意 ></p>
         <p>つるぼけに<br>気をつけろ！</p> -->
           <input type="radio" id="sweet_potato" class="button" name="choiceVeg" value="sweet_potato">
          <label class="radio-inline__label" for="sweet_potato"><img src="img/イモimg.png" alt=""></label>
        </div>

        <div class="img-container">
        <h3>- えだまめ -</h3>
         <!-- <p>< 注意 ></p>
         <p>ちっその与えすぎに<br>気をつけろ！</p> -->
          <input type="radio" id="edamame" class="button" name="choiceVeg" value="edamame">
          <label class="radio-inline__label" for="edamame"><img src="img/エダマメimg.png" alt=""></label>
        </div>

      </div>
      <div class="choice-soil">
       <h2>②栽培に使う土を、ひとつえらんでね！</h2>

       　<div class="img-container">
         <h3>- 土A -</h3>
         <p>栽培難易度：★</p>
         <p>< 特徴 ></p>
         <p>水も肥料もたっぷり！<br>育てやすい土だよ！</p>
        <input type="radio" id="soilA" class="button" name="choiceSoil" value="soilA" checked="checked">
          <label class="radio-inline__label" for="soilA"><img src="img/soil_img.jpg" alt=""></label>
          </div>

          　<div class="img-container">
          <h3>- 土B -</h3>
          <p>栽培難易度：★★</p>
          <p>< 特徴 ></p>
          <p>水が乾きやすい<br>けど、肥料はたっぷり！</p>
        <input type="radio" id="soilB" class="button" name="choiceSoil" value="soilB">
          <label class="radio-inline__label" for="soilB"><img src="img/soil_img.jpg" alt=""></label>
          </div>

          　<div class="img-container">
          <h3>- 土C -</h3>
          <p>栽培難易度：★★★</p>
         <p>< 特徴 ></p>
         <p>からっからで肥料も<br>少ない！難しい土だよ！</p>
        <input type="radio" id="soilC" class="button" name="choiceSoil" value="soilC">
          <label class="radio-inline__label" for="soilC"><img src="img/soil_img.jpg" alt=""></label>
          </div>

      </div>
      <button type="submit" name="choice" value="はじめる">はじめる</button>
    </form>

    </div>

    <?php } ?>

  </div>
  
</body>

</html>