<?php

if (!defined('DEBUG'))
{
	exit('Access Denied');
}

include (GLOBAL_PHP);

class main extends HOF_Class_User
{

	var $islogin = false;


	//
	function __construct()
	{
		$this->SessionSwitch();
		$this->Set_ID_PASS();
		ob_start();
		$this->Order();
		$content = ob_get_contents();
		ob_end_clean();

		$this->Head();
		print ($content);
		$this->Debug();
		//$this->ShowSession();
		$this->Foot();
	}


	//
	function Order()
	{
		// ログイン処理する前に処理するもの
		// まだユーザデータ読んでません
		switch (true)
		{
			case ($_GET["menu"] === "auction"):

				/*
				$ItemAuction = new HOF_Class_Item_Auction(item);
				$ItemAuction->AuctionHttpQuery("auction");
				$ItemAuction->ItemCheckSuccess(); // 競売が終了した品物を調べる
				$ItemAuction->UserSaveData(); // 競売品と金額を各IDに配って保存する
				*/

				HOF_Class_Controller::newInstance('auction')->main()->_main_stop();
				return 0;

				break;

			case ($_GET["menu"] === "rank"):
				/*
				include (CLASS_RANKING);
				$Ranking = new Ranking();
				*/

				HOF_Class_Controller::newInstance($_GET["menu"])->main()->_main_stop();
				return 0;
				break;
		}

		if (true === $message = $this->CheckLogin()):
			//if( false ):
			// ログイン

			if ($this->FirstLogin())
			{
				return 0;
			}

			switch (true)
			{

				case ($this->OptionOrder()):
					return false;

				case ($_POST["delete"]):
					/*
					if ($this->DeleteMyData()) return 0;
					*/
					if (!HOF_Class_Controller::newInstance('game', 'DeleteMyData')->main()->_main_stop())
					{
						return 0;
					}

					// 設定
				case ($_SERVER["QUERY_STRING"] === "setting"):
					if ($this->SettingProcess()) $this->SaveData();

					$this->fpCloseAll();
					$this->SettingShow();
					return 0;

					// 狩場
				case ($_SERVER["QUERY_STRING"] === "hunt"):
				/*
					$this->LoadUserItem(); //アイテムデータ読む
					$this->fpCloseAll();
					$this->HuntShow();
					*/

					HOF_Class_Controller::newInstance('Battle', $_SERVER["QUERY_STRING"])->main();

					return 0;

					// 街
				case ($_SERVER["QUERY_STRING"] === "town"):
					/*
					$this->LoadUserItem(); //アイテムデータ読む
					$this->fpCloseAll();
					$this->TownShow();
					*/

					HOF_Class_Controller::newInstance($_SERVER["QUERY_STRING"])->main();

					return 0;

					// シミュれ
				case ($_SERVER["QUERY_STRING"] === "simulate"):
					/*
					$this->CharDataLoadAll(); //キャラデータ読む
					if ($this->SimuBattleProcess()) $this->SaveData();

					$this->fpCloseAll();
					$this->SimuBattleShow($result);
					*/
					HOF_Class_Controller::newInstance('Battle', $_SERVER["QUERY_STRING"])->main();
					return 0;

					// ユニオン
				case ($_GET["union"]):
					/*
					$this->CharDataLoadAll(); //キャラデータ読む

					if ($this->UnionProcess())
					{
						// 戦闘する
						$this->SaveData();
						$this->fpCloseAll();
					}
					else
					{
						// 表示
						$this->fpCloseAll();
						$this->UnionShow();
					}
					*/
					HOF_Class_Controller::newInstance('Battle', 'union')->main();
					return 0;

					// 一般モンスター
					/*
				case ($_GET["common"]):
					$this->CharDataLoadAll(); //キャラデータ読む
					$this->LoadUserItem(); //アイテムデータ読む
					if ($this->MonsterBattle())
					{
						$this->SaveData();
						$this->fpCloseAll();
					}
					else
					{
						$this->fpCloseAll();
						$this->MonsterShow();
					}
					return 0;
					*/
				case ($_GET["common"]):
					HOF_Class_Controller::newInstance('Battle', 'common')->main();
					return 0;

					// キャラステ
				case ($_GET["char"]):
					$this->CharDataLoadAll(); //キャラデータ読む

					$this->LoadUserItem(); //アイテムデータ読む
					$this->CharStatProcess();
					$this->fpCloseAll();
					$this->CharStatShow();
					return 0;

					// アイテム一覧
				case ($_SERVER["QUERY_STRING"] === "item"):
					$this->LoadUserItem(); //アイテムデータ読む
					//$this->ItemProcess();
					$this->fpCloseAll();
					$this->ItemShow();
					return 0;

					/*
					// 精錬
				case ($_GET["menu"] === "refine"):
					$this->LoadUserItem();
					$this->SmithyRefineHeader();
					if ($this->SmithyRefineProcess()) $this->SaveData();

					$this->fpCloseAll();
					$result = $this->SmithyRefineShow();
					return 0;

					// 製作
				case ($_GET["menu"] === "create"):
					$this->LoadUserItem();
					$this->SmithyCreateHeader();

					if ($this->SmithyCreateProcess()) $this->SaveData();

					$this->fpCloseAll();
					$this->SmithyCreateShow();
					*/

				case ($_GET["menu"] === "refine"):
				case ($_GET["menu"] === "create"):
					HOF_Class_Controller::newInstance('Smithy', $_GET["menu"])->main();

					return 0;
					/*
					// ショップ(旧式:買う,売る,アルバイト)
					case($_SERVER["QUERY_STRING"] === "shop"):
					$this->LoadUserItem();//アイテムデータ読む
					if($this->ShopProcess())
					$this->SaveData();
					$this->fpCloseAll();
					$this->ShopShow();
					return 0;
					*/

					/*
					// ショップ(買う)
				case ($_GET["menu"] === "buy"):
					$this->LoadUserItem(); //アイテムデータ読む
					$this->ShopHeader();
					if ($this->ShopBuyProcess()) $this->SaveData();
					$this->fpCloseAll();
					$this->ShopBuyShow();
					return 0;

					// ショップ(売る)
				case ($_GET["menu"] === "sell"):
					$this->LoadUserItem(); //アイテムデータ読む
					$this->ShopHeader();
					if ($this->ShopSellProcess()) $this->SaveData();
					$this->fpCloseAll();
					$this->ShopSellShow();
					return 0;
					// ショップ(働く)
				case ($_GET["menu"] === "work"):
					$this->ShopHeader();
					if ($this->WorkProcess()) $this->SaveData();
					$this->fpCloseAll();
					$this->WorkShow();
					return 0;
					*/
				case ($_GET["menu"] === "buy"):
				case ($_GET["menu"] === "sell"):
				case ($_GET["menu"] === "work"):
					HOF_Class_Controller::newInstance('shop', $_GET["menu"])->main();

					return 0;

					// ランキング
				/*
				case ($_GET["menu"] === "rank"):
					$this->CharDataLoadAll(); //キャラデータ読む
					$RankProcess = $this->RankProcess($Ranking);

					if ($RankProcess === "BATTLE")
					{
						$this->SaveData();
						$this->fpCloseAll();
					}
					else
						if ($RankProcess === true)
						{
							$this->SaveData();
							$this->fpCloseAll();
							$this->RankShow($Ranking);
						}
						else
						{
							$this->fpCloseAll();
							$this->RankShow($Ranking);
						}
						return 0;*/

					// 雇用
				case ($_SERVER["QUERY_STRING"] === "recruit"):
				/*
					if ($this->RecruitProcess()) $this->SaveData();

					$this->fpCloseAll();
					$this->RecruitShow($result);
					*/

					HOF_Class_Controller::newInstance($_SERVER["QUERY_STRING"])->main();

					return 0;

					// それ以外(トップ)
				default:
					$this->CharDataLoadAll(); //キャラデータ読む
					$this->fpCloseAll();
					$this->LoginMain();
					return 0;
			}
		else:
			// ログアウト
			$this->fpCloseAll();
			switch (true)
			{
				case ($this->OptionOrder()):
					return false;
					/*
				case ($_POST["Make"]):
					list($bool, $message) = $this->MakeNewData();
					if (true === $bool)
					{
						$this->LoginForm($message);
						return false;
					}
					*/
				case ($_POST["Make"]):
				case ($_SERVER["QUERY_STRING"] === "newgame"):
					/*
					$this->NewForm($message);
					*/
					HOF_Class_Controller::getInstance('game', "newgame")->main();
					return false;
				default:
					/*
					$this->LoginForm($message);
					*/
					HOF_Class_Controller::getInstance('game', "login")->main();
			}
		endif;
		}


		//	UpDate,BBS,Manual等
		function OptionOrder()
		{
			$this->fpCloseAll();
			switch (true)
			{
				case ($_SERVER["QUERY_STRING"] === "rank"):
					RankAllShow();
					return true;
				case ($_SERVER["QUERY_STRING"] === "update"):
					ShowUpDate();
					return true;
				case ($_SERVER["QUERY_STRING"] === "bbs"):
					$this->bbs01();
					return true;
					/*
					case ($_SERVER["QUERY_STRING"] === "manual"):
					ShowManual();
					return true;
					case ($_SERVER["QUERY_STRING"] === "manual2"):
					ShowManual2();
					return true;
					case ($_SERVER["QUERY_STRING"] === "tutorial"):
					ShowTutorial();
					return true;
					*/
				case ($_SERVER["QUERY_STRING"] === "manual"):
				case ($_SERVER["QUERY_STRING"] === "manual2"):
				case ($_SERVER["QUERY_STRING"] === "tutorial"):
					HOF_Class_Controller::newInstance('manual', $_SERVER["QUERY_STRING"])->main();
					return true;

				/*
				case ($_SERVER["QUERY_STRING"] === "log"):
					ShowLogList();
					return true;
				case ($_SERVER["QUERY_STRING"] === "clog"):
					LogShowCommon();
					return true;
				case ($_SERVER["QUERY_STRING"] === "ulog"):
					LogShowUnion();
					return true;
				case ($_SERVER["QUERY_STRING"] === "rlog"):
					LogShowRanking();
					return true;
				*/
				case ($_SERVER["QUERY_STRING"] === "log"):
				case ($_SERVER["QUERY_STRING"] === "clog"):
				case ($_SERVER["QUERY_STRING"] === "ulog"):
				case ($_SERVER["QUERY_STRING"] === "rlog"):
				case ($_GET["log"]):
				case ($_GET["clog"]):
				case ($_GET["ulog"]):
				case ($_GET["rlog"]):
					HOF_Class_Controller::newInstance('log')->main();
					return true;
				case ($_GET["gamedata"]):
					/*
					ShowGameData();
					*/
					HOF_Class_Controller::newInstance('gamedata', $_GET["gamedata"])->main();
					return true;
					/*
				case ($_GET["log"]):
					ShowBattleLog($_GET["log"]);
					return true;
				case ($_GET["ulog"]):
					ShowBattleLog($_GET["ulog"], "UNION");
					return true;
				case ($_GET["rlog"]):
					ShowBattleLog($_GET["rlog"], "RANK");
					return true;
					*/

			}
		}


		//	敵の数を返す	数～数+2(max:5)
		function EnemyNumber($party)
		{
			$min = count($party); //プレイヤーのPT数
			if ($min == 5) //5人なら5匹
 					return 5;
			$max = $min + ENEMY_INCREASE; // つまり、+2なら[1人:1～3匹] [2人:2～4匹] [3:3-5] [4:4-5] [5:5]
			if ($max > 5) $max = 5;
			mt_srand();
			return mt_rand($min, $max);
		}

		//	出現する確率から敵を選んで返す
		function SelectMonster($monster)
		{
			foreach ($monster as $val) $max += $val[0]; //確率の合計
			$pos = mt_rand(0, $max); //0～合計 の中で乱数を取る
			foreach ($monster as $monster_no => $val)
			{
				$upp += $val[0]; //その時点での確率の合計
				if ($pos <= $upp) //合計より低ければ　敵が決定される
 						return $monster_no;
			}
		}

		//	敵のPTを作成、返す
		//	Specify=敵指定(配列)
		function EnemyParty($Amount, $MonsterList, $Specify = false)
		{

			// 指定モンスター
			if ($Specify)
			{
				$MonsterNumbers = $Specify;
			}

			// モンスターをとりあえず配列に全部入れる
			$enemy = array();

			if (!$Amount) return $enemy;
			mt_srand();
			for ($i = 0; $i < $Amount; $i++) $MonsterNumbers[] = $this->SelectMonster($MonsterList);

			// 重複しているモンスターを調べる
			$overlap = array_count_values($MonsterNumbers);

			// 敵情報を読んで配列に入れる。
			foreach ($MonsterNumbers as $Number)
			{
				/*
				if (1 < $overlap[$Number]) //1匹以上出現するなら名前に記号をつける。
				$enemy[] = new monster(HOF_Model_Char::getBaseMonster($Number, true));
				else  $enemy[] = new monster(HOF_Model_Char::getBaseMonster($Number));
				*/

				$enemy[] = HOF_Model_Char::newMon($Number, (1 < $overlap[$Number]));
			}

			$enemy = HOF_Class_Battle_Team::newInstance($enemy);

			return $enemy;
		}

		//	キャラ詳細表示から送られたリクエストを処理する
		//	長い...(100行オーバー)
		function CharStatProcess()
		{
			$char = &$this->char[$_GET["char"]];
			if (!$char) return false;
			switch (true):
					// ステータス上昇
				case ($_POST["stup"]):
					//ステータスポイント超過(ねんのための絶対値)
					$Sum = abs($_POST["upStr"]) + abs($_POST["upInt"]) + abs($_POST["upDex"]) + abs($_POST["upSpd"]) + abs($_POST["upLuk"]);
					if ($char->statuspoint < $Sum)
					{
						HOF_Helper_Global::ShowError("ステータスポイント超過", "margin15");
						return false;
					}

					if ($Sum == 0) return false;

					$Stat = array(
						"Str",
						"Int",
						"Dex",
						"Spd",
						"Luk");
					foreach ($Stat as $val)
					{ //最大値を超えないかチェック
						if (MAX_STATUS < ($char->{strtolower($val)} + $_POST["up" . $val]))
						{
							HOF_Helper_Global::ShowError("最大ステータス超過(" . MAX_STATUS . ")", "margin15");
							return false;
						}
					}
					$char->str += $_POST["upStr"]; //ステータスを増やす
					$char->int += $_POST["upInt"];
					$char->dex += $_POST["upDex"];
					$char->spd += $_POST["upSpd"];
					$char->luk += $_POST["upLuk"];
					$char->SetHpSp();

					$char->statuspoint -= $Sum; //ポイントを減らす。
					print ("<div class=\"margin15\">\n");
					if ($_POST["upStr"]) HOF_Helper_Global::ShowResult("STR が <span class=\"bold\">" . $_POST[upStr] . "</span> 上がった。" . ($char->str - $_POST["upStr"]) . " -> " . $char->str . "<br />\n");
					if ($_POST["upInt"]) HOF_Helper_Global::ShowResult("INT が <span class=\"bold\">" . $_POST[upInt] . "</span> 上がった。" . ($char->int - $_POST["upInt"]) . " -> " . $char->int . "<br />\n");
					if ($_POST["upDex"]) HOF_Helper_Global::ShowResult("DEX が <span class=\"bold\">" . $_POST[upDex] . "</span> 上がった。" . ($char->dex - $_POST["upDex"]) . " -> " . $char->dex . "<br />\n");
					if ($_POST["upSpd"]) HOF_Helper_Global::ShowResult("SPD が <span class=\"bold\">" . $_POST[upSpd] . "</span> 上がった。" . ($char->spd - $_POST["upSpd"]) . " -> " . $char->spd . "<br />\n");
					if ($_POST["upLuk"]) HOF_Helper_Global::ShowResult("LUK が <span class=\"bold\">" . $_POST[upLuk] . "</span> 上がった。" . ($char->luk - $_POST["upLuk"]) . " -> " . $char->luk . "<br />\n");
					print ("</div>\n");
					$char->SaveCharData($this->id);
					return true;
					// 配置・他設定(防御)
				case ($_POST["position"]):
					if ($_POST["position"] == "front")
					{
						$char->position = FRONT;
						$pos = "前衛(Front)";
					}
					else
					{
						$char->position = BACK;
						$pos = "後衛(Back)";
					}

					$char->guard = $_POST["guard"];
					switch ($_POST["guard"])
					{
						case "never":
							$guard = "後衛を守らない";
							break;
						case "life25":
							$guard = "体力が 25%以上なら 後衛を守る";
							break;
						case "life50":
							$guard = "体力が 50%以上なら 後衛を守る";
							break;
						case "life75":
							$guard = "体力が 75%以上なら 後衛を守る";
							break;
						case "prob25":
							$guard = "25%の確率で 後衛を守る";
							break;
						case "prob50":
							$guard = "50%の確率で 後衛を守る";
							break;
						case "prob75":
							$guard = "75%の確率で 後衛を守る";
							break;
						default:
							$guard = "必ず後衛を守る";
							break;
					}
					$char->SaveCharData($this->id);
					HOF_Helper_Global::ShowResult($char->Name() . " の配置を {$pos} に。<br />前衛の時 {$guard} ように設定。\n", "margin15");
					return true;
					//行動設定
				case ($_POST["ChangePattern"]):
					$max = $char->MaxPatterns();
					//記憶するパターンと技の配列。
					for ($i = 0; $i < $max; $i++)
					{
						$judge[] = $_POST["judge" . $i];
						$quantity_post = (int)$_POST["quantity" . $i];
						if (4 < strlen($quantity_post))
						{
							$quantity_post = substr($quantity_post, 0, 4);
						}
						$quantity[] = $quantity_post;
						$action[] = $_POST["skill" . $i];
					}
					//if($char->ChangePattern($judge,$action)) {
					if ($char->PatternSave($judge, $quantity, $action))
					{
						$char->SaveCharData($this->id);
						HOF_Helper_Global::ShowResult("パターン設定保存 完了", "margin15");
						return true;
					}
					HOF_Helper_Global::ShowError("失敗したなんで？報告してみてください 03050242", "margin15");
					return false;
					break;
					//	行動設定 兼 模擬戦
				case ($_POST["TestBattle"]):
					$max = $char->MaxPatterns();
					//記憶するパターンと技の配列。
					for ($i = 0; $i < $max; $i++)
					{
						$judge[] = $_POST["judge" . $i];
						$quantity_post = (int)$_POST["quantity" . $i];
						if (4 < strlen($quantity_post))
						{
							$quantity_post = substr($quantity_post, 0, 4);
						}
						$quantity[] = $quantity_post;
						$action[] = $_POST["skill" . $i];
					}
					//if($char->ChangePattern($judge,$action)) {
					if ($char->PatternSave($judge, $quantity, $action))
					{
						$char->SaveCharData($this->id);
						$this->CharTestDoppel();
					}
					break;
					//	行動パターンメモ(交換)
				case ($_POST["PatternMemo"]):
					if ($char->ChangePatternMemo())
					{
						$char->SaveCharData($this->id);
						HOF_Helper_Global::ShowResult("パターン交換 完了", "margin15");
						return true;
					}
					break;
					//	指定行に追加
				case ($_POST["AddNewPattern"]):
					if (!isset($_POST["PatternNumber"])) return false;
					if ($char->AddPattern($_POST["PatternNumber"]))
					{
						$char->SaveCharData($this->id);
						HOF_Helper_Global::ShowResult("パターン追加 完了", "margin15");
						return true;
					}
					break;
					//	指定行を削除
				case ($_POST["DeletePattern"]):
					if (!isset($_POST["PatternNumber"])) return false;
					if ($char->DeletePattern($_POST["PatternNumber"]))
					{
						$char->SaveCharData($this->id);
						HOF_Helper_Global::ShowResult("パターン削除 完了", "margin15");
						return true;
					}
					break;
					//	指定箇所だけ装備をはずす
				case ($_POST["remove"]):
					if (!$_POST["spot"])
					{
						HOF_Helper_Global::ShowError("装備をはずす箇所が選択されていない", "margin15");
						return false;
					}
					if (!$char->{$_POST["spot"]})
					{ // $this と $char の区別注意！
						HOF_Helper_Global::ShowError("指定された箇所には装備無し", "margin15");
						return false;
					}
					$item = HOF_Model_Data::getItemData($char->{$_POST["spot"]});
					if (!$item) return false;
					$this->AddItem($char->{$_POST["spot"]});
					$this->SaveUserItem();
					$char->{$_POST["spot"]} = NULL;
					$char->SaveCharData($this->id);
					SHowResult($char->Name() . " の {$item[name]} を はずした。", "margin15");
					return true;
					break;
					//	装備全部はずす
				case ($_POST["remove_all"]):
					if ($char->weapon || $char->shield || $char->armor || $char->item)
					{
						if ($char->weapon)
						{
							$this->AddItem($char->weapon);
							$char->weapon = NULL;
						}
						if ($char->shield)
						{
							$this->AddItem($char->shield);
							$char->shield = NULL;
						}
						if ($char->armor)
						{
							$this->AddItem($char->armor);
							$char->armor = NULL;
						}
						if ($char->item)
						{
							$this->AddItem($char->item);
							$char->item = NULL;
						}
						$this->SaveUserItem();
						$char->SaveCharData($this->id);
						HOF_Helper_Global::ShowResult($char->Name() . " の装備を 全部解除した", "margin15");
						return true;
					}
					break;
					//	指定物を装備する
				case ($_POST["equip_item"]):
					$item_no = $_POST["item_no"];
					if (!$this->item["$item_no"])
					{ //そのアイテムを所持しているか
						HOF_Helper_Global::ShowError("Item not exists.", "margin15");
						return false;
					}

					$JobData = HOF_Model_Data::getJobData($char->job);
					$item = HOF_Model_Data::getItemData($item_no); //装備しようとしてる物
					if (!in_array($item["type"], $JobData["equip"]))
					{ //それが装備不可能なら?
						HOF_Helper_Global::ShowError("{$char->job_name} can't equip {$item[name]}.", "margin15");
						return false;
					}

					if (false === $return = $char->Equip($item))
					{
						HOF_Helper_Global::ShowError("Handle Over.", "margin15");
						return false;
					}
					else
					{
						$this->DeleteItem($item_no);
						foreach ($return as $no)
						{
							$this->AddItem($no);
						}
					}

					$this->SaveUserItem();
					$char->SaveCharData($this->id);
					HOF_Helper_Global::ShowResult("{$char->name} は {$item[name]} を装備した.", "margin15");
					return true;
					break;
					// スキル習得
				case ($_POST["learnskill"]):
					if (!$_POST["newskill"])
					{
						HOF_Helper_Global::ShowError("スキル未選択", "margin15");
						return false;
					}

					$char->SetUser($this->id);
					list($result, $message) = $char->LearnNewSkill($_POST["newskill"]);
					if ($result)
					{
						$char->SaveCharData();
						HOF_Helper_Global::ShowResult($message, "margin15");
					}
					else
					{
						HOF_Helper_Global::ShowError($message, "margin15");
					}
					return true;
					// クラスチェンジ(転職)
				case ($_POST["classchange"]):
					if (!$_POST["job"])
					{
						HOF_Helper_Global::ShowError("職 未選択", "margin15");
						return false;
					}
					if ($char->ClassChange($_POST["job"]))
					{
						// 装備を全部解除
						if ($char->weapon || $char->shield || $char->armor || $char->item)
						{
							if ($char->weapon)
							{
								$this->AddItem($char->weapon);
								$char->weapon = NULL;
							}
							if ($char->shield)
							{
								$this->AddItem($char->shield);
								$char->shield = NULL;
							}
							if ($char->armor)
							{
								$this->AddItem($char->armor);
								$char->armor = NULL;
							}
							if ($char->item)
							{
								$this->AddItem($char->item);
								$char->item = NULL;
							}
							$this->SaveUserItem();
						}
						// 保存
						$char->SaveCharData($this->id);
						HOF_Helper_Global::ShowResult("転職 完了", "margin15");
						return true;
					}
					HOF_Helper_Global::ShowError("failed.", "margin15");
					return false;
					//	改名(表示)
				case ($_POST["rename"]):
					$Name = $char->Name();
					$message = <<< EOD
<form action="?char={$_GET[char]}" method="post" class="margin15">
半角英数16文字 (全角1文字=半角2文字)<br />
<input type="text" name="NewName" style="width:160px" class="text" />
<input type="submit" class="btn" name="NameChange" value="Change" />
<input type="submit" class="btn" value="Cancel" />
</form>
EOD;
					print ($message);
					return false;
					// 改名(処理)
				case ($_POST["NewName"]):
					list($result, $return) = CheckString($_POST["NewName"], 16);
					if ($result === false)
					{
						HOF_Helper_Global::ShowError($return, "margin15");
						return false;
					}
					else
						if ($result === true)
						{
							if ($this->DeleteItem("7500", 1) == 1)
							{
								HOF_Helper_Global::ShowResult($char->Name() . " から " . $return . " へ改名しました。", "margin15");
								$char->ChangeName($return);
								$char->SaveCharData($this->id);
								$this->SaveUserItem();
								return true;
							}
							else
							{
								HOF_Helper_Global::ShowError("アイテムがありません。", "margin15");
								return false;
							}
							return true;
						}
					// 各種リセットの表示
				case ($_POST["showreset"]):
					$Name = $char->Name();
					print ('<div class="margin15">' . "\n");
					print ("使用するアイテム<br />\n");
					print ('<form action="?char=' . $_GET[char] . '" method="post">' . "\n");
					print ('<select name="itemUse">' . "\n");
					$resetItem = array(
						7510,
						7511,
						7512,
						7513,
						7520);
					foreach ($resetItem as $itemNo)
					{
						if ($this->item[$itemNo])
						{
							$item = HOF_Model_Data::getItemData($itemNo);
							print ('<option value="' . $itemNo . '">' . $item[name] . " x" . $this->item[$itemNo] . '</option>' . "\n");
						}
					}
					print ("</select>\n");
					print ('<input type="submit" class="btn" name="resetVarious" value="Reset">' . "\n");
					print ('<input type="submit" class="btn" value="Cancel">' . "\n");
					print ('</form>' . "\n");
					print ('</div>' . "\n");
					break;

					// 各種リセットの処理
				case ($_POST["resetVarious"]):
					switch ($_POST["itemUse"])
					{
						case 7510:
							$lowLimit = 1;
							break;
						case 7511:
							$lowLimit = 30;
							break;
						case 7512:
							$lowLimit = 50;
							break;
						case 7513:
							$lowLimit = 100;
							break;
							// skill
						case 7520:
							$skillReset = true;
							break;
					}
					// 石ころをSPD1に戻すアイテムにする
					if ($_POST["itemUse"] == 6000)
					{
						if ($this->DeleteItem(6000) == 0)
						{
							HOF_Helper_Global::ShowError("アイテムがありません。", "margin15");
							return false;
						}
						if (1 < $char->spd)
						{
							$dif = $char->spd - 1;
							$char->spd -= $dif;
							$char->statuspoint += $dif;
							$char->SaveCharData($this->id);
							$this->SaveUserItem();
							HOF_Helper_Global::ShowResult("ポイント還元成功", "margin15");
							return true;
						}
					}
					if ($lowLimit)
					{
						if (!$this->item[$_POST["itemUse"]])
						{
							HOF_Helper_Global::ShowError("アイテムがありません。", "margin15");
							return false;
						}
						if ($lowLimit < $char->str)
						{
							$dif = $char->str - $lowLimit;
							$char->str -= $dif;
							$pointBack += $dif;
						}
						if ($lowLimit < $char->int)
						{
							$dif = $char->int - $lowLimit;
							$char->int -= $dif;
							$pointBack += $dif;
						}
						if ($lowLimit < $char->dex)
						{
							$dif = $char->dex - $lowLimit;
							$char->dex -= $dif;
							$pointBack += $dif;
						}
						if ($lowLimit < $char->spd)
						{
							$dif = $char->spd - $lowLimit;
							$char->spd -= $dif;
							$pointBack += $dif;
						}
						if ($lowLimit < $char->luk)
						{
							$dif = $char->luk - $lowLimit;
							$char->luk -= $dif;
							$pointBack += $dif;
						}
						if ($pointBack)
						{
							if ($this->DeleteItem($_POST["itemUse"]) == 0)
							{
								HOF_Helper_Global::ShowError("アイテムがありません。", "margin15");
								return false;
							}
							$char->statuspoint += $pointBack;
							// 装備も全部解除
							if ($char->weapon || $char->shield || $char->armor || $char->item)
							{
								if ($char->weapon)
								{
									$this->AddItem($char->weapon);
									$char->weapon = NULL;
								}
								if ($char->shield)
								{
									$this->AddItem($char->shield);
									$char->shield = NULL;
								}
								if ($char->armor)
								{
									$this->AddItem($char->armor);
									$char->armor = NULL;
								}
								if ($char->item)
								{
									$this->AddItem($char->item);
									$char->item = NULL;
								}
								HOF_Helper_Global::ShowResult($char->Name() . " の装備を 全部解除した", "margin15");
							}
							$char->SaveCharData($this->id);
							$this->SaveUserItem();
							HOF_Helper_Global::ShowResult("ポイント還元成功", "margin15");
							return true;
						}
						else
						{
							HOF_Helper_Global::ShowError("ポイント還元失敗", "margin15");
							return false;
						}
					}
					break;

					// サヨナラ(表示)
				case ($_POST["byebye"]):
					$Name = $char->Name();
					$message = <<< HTML_BYEBYE
<div class="margin15">
{$Name} を 解雇しますか?<br>
<form action="?char={$_GET[char]}" method="post">
<input type="submit" class="btn" name="kick" value="Yes">
<input type="submit" class="btn" value="No">
</form>
</div>
HTML_BYEBYE;
					print ($message);
					return false;
					// サヨナラ(処理)
				case ($_POST["kick"]):
					//$this->DeleteChar($char->birth);
					$char->DeleteChar();
					$host = $_SERVER['HTTP_HOST'];
					$uri = rtrim(dirname($_SERVER['PHP_SELF']));
					//$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
					$extra = INDEX;
					header("Location: http://$host$uri/$extra");
					exit;
					break;
			endswitch;
		}
		////////////////////////////////////
		//	キャラクター詳細表示・装備変更などなど
		//	長すぎる...(200行以上)
		function CharStatShow()
		{
			$char = &$this->char[$_GET["char"]];
			if (!$char)
			{
				print ("Not exists");
				return false;
			}
			// 戦闘用変数の設定。
			$char->SetBattleVariable();

			// 職データ
			$JobData = HOF_Model_Data::getJobData($char->job);

			// 転職可能な職
			if ($JobData["change"])
			{
				include_once (DATA_CLASSCHANGE);
				foreach ($JobData["change"] as $job)
				{
					if (CanClassChange($char, $job)) $CanChange[] = $job; //転職できる候補。
				}
			}

			////// ステータス表示 //////////////////////////////



?>
<form action="?char=<?=

			$_GET["char"]


?>" method="post" style="padding:5px 0 0 15px">
	<?php

			// その他キャラ
			print ('<div style="padding-top:5px">');
			foreach ($this->char as $key => $val)
			{
				//if($key == $_GET["char"]) continue;//表示中キャラスキップ
				echo "<a href=\"?char={$key}\">{$val->name}</a>&nbsp;&nbsp;";
			}
			print ("</div>");


?>
	<h4>Character Status<a href="?manual#charstat" target="_blank" class="a0">?</a></h4>
	<?php

			$char->ShowCharDetail();
			// 改名
			if ($this->item["7500"]) print ('<input type="submit" class="btn" name="rename" value="ChangeName">' . "\n");
			// ステータスリセット系
			if ($this->item["7510"] || $this->item["7511"] || $this->item["7512"] || $this->item["7513"] || $this->item["7520"])
			{
				print ('<input type="submit" class="btn" name="showreset" value="Reset">' . "\n");
			}


?>
	<input type="submit" class="btn" name="byebye" value="Kick">
</form>
<?php

			// ステータス上昇 ////////////////////////////
			if (0 < $char->statuspoint)
			{
				print <<< HTML
	<form action="?char=$_GET[char]" method="post" style="padding:0 15px">
	<h4>Status <a href="?manual#statup" target="_blank" class="a0">?</a></h4>
HTML;

				$Stat = array(
					"Str",
					"Int",
					"Dex",
					"Spd",
					"Luk");
				print ("Point : {$char->statuspoint}<br />\n");
				foreach ($Stat as $val)
				{
					print ("{$val}:\n");
					print ("<select name=\"up{$val}\" class=\"vcent\">\n");
					for ($i = 0; $i < $char->statuspoint + 1; $i++) print ("<option value=\"{$i}\">+{$i}</option>\n");
					print ("</select>");
				}
				print ("<br />");
				print ('<input type="submit" class="btn" name="stup" value="Increase Status">');
				print ("\n");

				print ("</form>\n");
			}


?>
<form action="?char=<?=

			$_GET["char"]


?>" method="post" style="padding:0 15px">
	<h4>Action Pattern<a href="?manual#jdg" target="_blank" class="a0">?</a></h4>
	<?php

			// Action Pattern 行動判定 /////////////////////////
			$list = HOF_Model_Data::getJudgeList(); // 行動判定条件一覧
			print ("<table cellspacing=\"5\"><tbody>\n");
			for ($i = 0; $i < $char->MaxPatterns(); $i++)
			{
				print ("<tr><td>");
				//----- No
				print (($i + 1) . "</td><td>");
				//----- JudgeSelect(判定の種類)
				print ("<select name=\"judge" . $i . "\">\n");
				foreach ($list as $val)
				{ //判断のoption
					$exp = HOF_Model_Data::getJudgeData($val);
					print ("<option value=\"{$val}\"" . ($char->judge[$i] == $val ? " selected" : NULL) . ($exp["css"] ? ' class="select0"' : NULL) . ">" . ($exp["css"] ? '&nbsp;' : '&nbsp;&nbsp;&nbsp;') . "{$exp[exp]}</option>\n");
				}
				print ("</select>\n");
				print ("</td><td>\n");
				//----- 数値(量)
				print ("<input type=\"text\" name=\"quantity" . $i . "\" maxlength=\"4\" value=\"" . $char->quantity[$i] . "\" style=\"width:56px\" class=\"text\">");
				print ("</td><td>\n");
				//----- //SkillSelect(技の種類)
				print ("<select name=\"skill" . $i . "\">\n");
				foreach ($char->skill as $val)
				{ //技のoption
					$skill = HOF_Model_Data::getSkill($val);
					print ("<option value=\"{$val}\"" . ($char->action[$i] == $val ? " selected" : NULL) . ">");
					print ($skill["name"] . (isset($skill["sp"]) ? " - (SP:{$skill[sp]})" : NULL));
					print ("</option>\n");
				}
				print ("</select>\n");
				print ("</td><td>\n");
				print ('<input type="radio" name="PatternNumber" value="' . $i . '">');
				print ("</td></tr>\n");
			}
			print ("</tbody></table>\n");


?>
	<input type="submit" class="btn" value="Set Pattern" name="ChangePattern">
	<input type="submit" class="btn" value="Set & Test" name="TestBattle">
	&nbsp;<a href="?simulate">Simulate</a><br />
	<input type="submit" class="btn" value="Switch Pattern" name="PatternMemo">
	<input type="submit" class="btn" value="Add" name="AddNewPattern">
	<input type="submit" class="btn" value="Delete" name="DeletePattern">
</form>
<form action="?char=<?=

			$_GET["char"]


?>" method="post" style="padding:0 15px">
	<h4>Position & Guarding<a href="?manual#posi" target="_blank" class="a0">?</a></h4>
	<table>
		<tbody>
			<tr>
				<td>位置(Position) :</td>
				<td><input type="radio" class="vcent" name="position" value="front"<?php

			($char->position == "front" ? print (" checked") : NULL)


?>>
					前衛(Front)</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="radio" class="vcent" name="position" value="back"<?php

			($char->position == "back" ? print (" checked") : NULL)


?>>
					後衛(Backs)</td>
			</tr>
			<tr>
				<td>護衛(Guarding) :</td>
				<td><select name="guard">
						<?php

			// 前衛の時の後衛守り //////////////////////////////
			$option = array(
				/*
				"always"=> "Always",
				"never"	=> "Never",
				"life25"	=> "If life more than 25%",
				"life50"	=> "If life more than 50%",
				"life75"	=> "If life more than 75%",
				"prob25"	=> "Probability of 25%",
				"prpb50"	=> "Probability of 50%",
				"prob75"	=> "Probability of 75%",
				*/
				"always" => "必ず守る",
				"never" => "守らない",
				"life25" => "体力が 25%以上なら 守る",
				"life50" => "体力が 50%以上なら 守る",
				"life75" => "体力が 75%以上なら 守る",
				"prob25" => "25%の確率で 守る",
				"prpb50" => "50%の確率で 守る",
				"prob75" => "75%の確率で 守る",
				);
			foreach ($option as $key => $val) print ("<option value=\"{$key}\"" . ($char->guard == $key ? " selected" : NULL) . ">{$val}</option>");


?>
					</select></td>
			</tr>
		</tbody>
	</table>
	<input type="submit" class="btn" value="Set">
</form>
<?php

			// 装備中の物表示 ////////////////////////////////
			$weapon = HOF_Model_Data::getItemData($char->weapon);
			$shield = HOF_Model_Data::getItemData($char->shield);
			$armor = HOF_Model_Data::getItemData($char->armor);
			$item = HOF_Model_Data::getItemData($char->item);

			$handle = 0;
			$handle = $weapon["handle"] + $shield["handle"] + $armor["handle"] + $item["handle"];


?>
<div style="margin:0 15px">
	<h4>Equipment<a href="?manual#equip" target="_blank" class="a0">?</a></h4>
	<div class="bold u">
		Current Equip's
	</div>
	<table>
		<tr>
			<td class="dmg" style="text-align:right">Atk :</td>
			<td class="dmg"><?=

			$char->atk[0]


?></td>
		</tr>
		<tr>
			<td class="spdmg" style="text-align:right">Matk :</td>
			<td class="spdmg"><?=

			$char->atk[1]


?></td>
		</tr>
		<tr>
			<td class="recover" style="text-align:right">Def :</td>
			<td class="recover"><?=

			$char->def[0] . " + " . $char->def[1]


?></td>
		</tr>
		<tr>
			<td class="support" style="text-align:right">Mdef :</td>
			<td class="support"><?=

			$char->def[2] . " + " . $char->def[3]


?></td>
		</tr>
		<tr>
			<td class="charge" style="text-align:right">handle :</td>
			<td class="charge"><?=

			$handle


?>
				/
				<?=

			$char->GetHandle()


?></td>
		</tr>
	</table>
	<form action="?char=<?=

			$_GET["char"]


?>" method="post">
		<table>
			<tr>
				<td class="align-right">Weapon :</td>
				<td><input type="radio" class="vcent" name="spot" value="weapon">
					<?php

			HOF_Class_Item::ShowItemDetail(HOF_Model_Data::getItemData($char->weapon));


?></td>
			</tr>
			<tr>
				<td class="align-right">Shield :</td>
				<td><input type="radio" class="vcent" name="spot" value="shield">
					<?php

			HOF_Class_Item::ShowItemDetail(HOF_Model_Data::getItemData($char->shield));


?></td>
			</tr>
			<tr>
				<td class="align-right">Armor :</td>
				<td><input type="radio" class="vcent" name="spot" value="armor">
					<?php

			HOF_Class_Item::ShowItemDetail(HOF_Model_Data::getItemData($char->armor));


?></td>
			</tr>
			<tr>
				<td class="align-right">Item :</td>
				<td><input type="radio" class="vcent" name="spot" value="item">
					<?php

			HOF_Class_Item::ShowItemDetail(HOF_Model_Data::getItemData($char->item));


?></td>
			</tr>
				</tbody>
		</table>
		<input type="submit" class="btn" name="remove" value="Remove">
		<input type="submit" class="btn" name="remove_all" value="Remove All">
	</form>
</div>
<?php

			// 装備可能な物表示 ////////////////////////////////
			if ($JobData["equip"]) $EquipAllow = array_flip($JobData["equip"]); //装備可能な物リスト(反転)
			else  $EquipAllow = array(); //装備可能な物リスト(反転)
			$Equips = array(
				"Weapon" => "2999",
				"Shield" => "4999",
				"Armor" => "5999",
				"Item" => "9999");

			print ("<div style=\"padding:15px 15px 0 15px\">\n");
			print ("\t<div class=\"bold u\">Stock & Allowed to Equip</div>\n");
			if ($this->item)
			{

				$EquipList = new HOF_Class_Item_Style_List();
				$EquipList->SetID("equip");
				$EquipList->SetName("type_equip");
				// JSを使用しない。
				if ($this->no_JS_itemlist) $EquipList->NoJS();
				reset($this->item); //これが無いと装備変更時に表示されない
				foreach ($this->item as $key => $val)
				{
					$item = HOF_Model_Data::getItemData($key);
					// 装備できないので次
					if (!isset($EquipAllow[$item["type"]])) continue;
					$head = '<input type="radio" name="item_no" value="' . $key . '" class="vcent">';
					$head .= HOF_Class_Item::ShowItemDetail($item, $val, true) . "<br />";
					$EquipList->AddItem($item, $head);
				}
				print ($EquipList->GetJavaScript("list0"));
				print ($EquipList->ShowSelect());
				print ('<form action="?char=' . $_GET["char"] . '" method="post">' . "\n");
				print ('<div id="list0">' . $EquipList->ShowDefault() . '</div>' . "\n");
				print ('<input type="submit" class="btn" name="equip_item" value="Equip">' . "\n");
				print ("</form>\n");
			}
			else
			{
				print ("No items.<br />\n");
			}
			print ("</div>\n");


			/*
			print("\t<table><tbody><tr><td colspan=\"2\">\n");
			print("\t<span class=\"bold u\">Stock & Allowed to Equip</span></td></tr>\n");
			if($this->item):
			reset($this->item);//これが無いと装備変更時に表示されない
			foreach($Equips as $key => $val) {
			print("\t<tr><td class=\"align-right\" valign=\"top\">\n");
			print("\t{$key} :</td><td>\n");
			while( substr(key($this->item),0,4) <= $val && substr(current($this->item),0,4) !== false ) {
			$item	= HOF_Model_Data::getItemData(key($this->item));
			if(!isset( $EquipAllow[ $item["type"] ] )) {
			next($this->item);
			continue;
			}
			print("\t");
			print('<input type="radio" class="vcent" name="item_no" value="'.key($this->item).'">');
			print("\n\t");
			print(current($this->item)."x");
			HOF_Class_Item::ShowItemDetail($item);
			print("<br>\n");
			next($this->item);
			}
			print("\t</td></tr>\n");
			}
			else:
			print("<tr><td>No items.</td></tr>");
			endif;
			print("\t</tbody></table>\n");
			*/


?>
<form action="?char=<?=

			$_GET["char"]


?>" method="post" style="padding:0 15px">
	<h4>Skill<a href="?manual#skill" target="_blank" class="a0">?</a></h4>
	<?php

			// スキル表示 //////////////////////////////////////
			//include(DATA_SKILL);//ActionPatternに移動
			include_once (DATA_SKILL_TREE);
			if ($char->skill)
			{
				print ('<div class="u bold">Mastered</div>');
				print ("<table><tbody>");
				foreach ($char->skill as $val)
				{
					print ("<tr><td>");
					$skill = HOF_Model_Data::getSkill($val);
					HOF_Class_Skill::ShowSkillDetail($skill);
					print ("</td></tr>");
				}
				print ("</tbody></table>");
				print ('<div class="u bold">Learn New</div>');
				print ("Skill Point : {$char->skillpoint}");
				print ("<table><tbody>");
				$tree = LoadSkillTree($char);
				foreach (array_diff($tree, $char->skill) as $val)
				{
					print ("<tr><td>");
					$skill = HOF_Model_Data::getSkill($val);
					HOF_Class_Skill::ShowSkillDetail($skill, 1);
					print ("</td></tr>");
				}
				print ("</tbody></table>");
				//dump($char->skill);
				//dump($tree);
				print ('<input type="submit" class="btn" name="learnskill" value="Learn">' . "\n");
				print ('<input type="hidden" name="learnskill" value="1">' . "\n");
			}
			// 転職 ////////////////////////////////////////////
			if ($CanChange)
			{


?>
</form>
<form action="?char=<?=

				$_GET["char"]


?>" method="post" style="padding:0 15px">
	<h4>ClassChange</h4>
	<table>
		<tbody>
			<tr>
				<?php

				foreach ($CanChange as $job)
				{
					print ("<td valign=\"bottom\" style=\"padding:5px 30px;text-align:center\">");
					$JOB = HOF_Model_Data::getJobData($job);
					print ('<img src="' . IMG_CHAR . $JOB["img_" . ($char->gender ? "female" : "male")] . '">' . "<br />\n"); //画像
					print ('<input type="radio" value="' . $job . '" name="job">' . "<br />\n");
					print ($JOB["name_" . ($char->gender ? "female" : "male")]);
					print ("</td>");
				}


?>
			</tr>
		</tbody>
	</table>
	<input type="submit" class="btn" name="classchange" value="ClassChange">
	<input type="hidden" name="classchange" value="1">
	<?php

			}


?>
</form>
<?php

			//その他キャラ
			print ('<div  style="padding:15px">');
			foreach ($this->char as $key => $val)
			{
				//if($key == $_GET["char"]) continue;//表示中キャラスキップ
				echo "<a href=\"?char={$key}\">{$val->name}</a>&nbsp;&nbsp;";
			}
			print ('</div>');
		}

		//	('A`)...
		function CharTestDoppel()
		{
			if (!$_POST["TestBattle"]) return 0;

			$char = $this->char[$_GET["char"]];
			$this->DoppelBattle(array($char));
		}

		//	ドッペルゲンガーと戦う。
		function DoppelBattle($party, $turns = 10)
		{
			//$enemy	= $party;
			//これが無いとPHP4or5 で違う結果になるんです
			//$enemy	= unserialize(serialize($enemy));
			// ↓

			$enemy = array();

			foreach ($party as $key => $char)
			{
				/*
				$enemy[$key] = new HOF_Class_Char();
				$enemy[$key]->SetCharData(get_object_vars($char));
				*/

				$enemy[$key] = HOF_Model_Char::newChar(get_object_vars($char));
			}
			foreach ($enemy as $key => $doppel)
			{
				//$doppel->judge	= array();//コメントを取るとドッペルが行動しない。
				$enemy[$key]->ChangeName("ニセ" . $doppel->name);
			}
			//dump($enemy[0]->judge);
			//dump($party[0]->judge);

			$enemy = HOF_Class_Battle_Team::newInstance($enemy);
			$party = HOF_Class_Battle_Team::newInstance($party);

			$battle = new HOF_Class_Battle($party, $enemy);
			$battle->SetTeamName($this->name, "ドッペル");
			$battle->LimitTurns($turns); //最大ターン数は10
			$battle->NoResult();
			$battle->Process(); //戦闘開始
			return true;
		}

		//




		//


		//


		function ItemProcess()
		{
		}


		//
		function ItemShow()
		{


?>
<div style="margin:15px">
<h4>Items</h4>
<div style="margin:0 20px">
	<?php

			if ($this->item)
			{

				$goods = new HOF_Class_Item_Style_List();
				$goods->SetID("my");
				$goods->SetName("type");
				// JSを使用しない。
				if ($this->no_JS_itemlist) $goods->NoJS();
				//$goods->ListTable("<table>");
				//$goods->ListTableInsert("<tr><td>No</td><td>Item</td></tr>");
				foreach ($this->item as $no => $val)
				{
					$item = HOF_Model_Data::getItemData($no);
					$string = HOF_Class_Item::ShowItemDetail($item, $val, 1) . "<br />";
					//$string	= "<tr><td>".$no."</td><td>".HOF_Class_Item::ShowItemDetail($item,$val,1)."</td></tr>";
					$goods->AddItem($item, $string);
				}
				print ($goods->GetJavaScript("list"));
				print ($goods->ShowSelect());
				print ('<div id="list">' . $goods->ShowDefault() . '</div>');
			}
			else
			{
				print ("No items.");
			}
			print ("</div></div>");
		}

		//

		//
		function ShopProcess()
		{
			switch (true)
			{
				case ($_POST["partjob"]):
					if ($this->WasteTime(100))
					{
						$this->GetMoney(500);
						HOF_Helper_Global::ShowResult("働いて " . HOF_Helper_Global::MoneyFormat(500) . " げっとした!", "margin15");
						return true;
					}
					else
					{
						HOF_Helper_Global::ShowError("時間が無い。働くなんてもったいない。", "margin15");
						return false;
					}
				case ($_POST["shop_buy"]):
					$ShopList = HOF_Model_Data::getShopList(); //売ってるものデータ
					if ($_POST["item_no"] && in_array($_POST["item_no"], $ShopList))
					{
						if (ereg("^[0-9]", $_POST["amount"]))
						{
							$amount = (int)$_POST["amount"];
							if ($amount == 0) $amount = 1;
						}
						else
						{
							$amount = 1;
						}
						$item = HOF_Model_Data::getItemData($_POST["item_no"]);
						$need = $amount * $item["buy"]; //購入に必要なお金
						if ($this->TakeMoney($need))
						{ // お金を引けるかで判定。
							$this->AddItem($_POST["item_no"], $amount);
							$this->SaveUserItem();
							if (1 < $amount)
							{
								$img = "<img src=\"" . HOF_Class_Icon::getImageUrl($item[img], IMG_ICON . 'item/') . "\" class=\"vcent\" />";
								HOF_Helper_Global::ShowResult("{$img}{$item[name]} を{$amount}個 購入した (" . HOF_Helper_Global::MoneyFormat($item["buy"]) . " x{$amount} = " . HOF_Helper_Global::MoneyFormat($need) . ")", "margin15");
								return true;
							}
							else
							{
								$img = "<img src=\"" . HOF_Class_Icon::getImageUrl($item[img], IMG_ICON . 'item/') . "\" class=\"vcent\" />";
								HOF_Helper_Global::ShowResult("{$img}{$item[name]} を購入した (" . HOF_Helper_Global::MoneyFormat($need) . ")", "margin15");
								return true;
							}
						}
						else
						{ //資金不足
							HOF_Helper_Global::ShowError("資金不足(Need " . HOF_Helper_Global::MoneyFormat($need) . ")", "margin15");
							return false;
						}
					}
					break;
				case ($_POST["shop_sell"]):
					if ($_POST["item_no"] && $this->item[$_POST["item_no"]])
					{
						if (ereg("^[0-9]", $_POST["amount"]))
						{
							$amount = (int)$_POST["amount"];
							if ($amount == 0) $amount = 1;
						}
						else
						{
							$amount = 1;
						}
						// 消した個数(超過して売られるのも防ぐ)
						$DeletedAmount = $this->DeleteItem($_POST["item_no"], $amount);
						$item = HOF_Model_Data::getItemData($_POST["item_no"]);
						$price = (isset($item["sell"]) ? $item["sell"] : round($item["buy"] * SELLING_PRICE));
						$this->GetMoney($price * $DeletedAmount);
						$this->SaveUserItem();
						if ($DeletedAmount != 1) $add = " x{$DeletedAmount}";
						$img = "<img src=\"" . HOF_Class_Icon::getImageUrl($item[img], IMG_ICON . 'item/') . "\" class=\"vcent\" />";
						HOF_Helper_Global::ShowResult("{$img}{$item[name]}{$add} を " . HOF_Helper_Global::MoneyFormat($price * $DeletedAmount) . " で売った", "margin15");
						return true;
					}
					break;
			}
		}

		//
		function ShopShow($message = NULL)
		{


?>
	<div style="margin:15px">
		<?=

			HOF_Helper_Global::ShowError($message)


?>
		<h4>Goods List</h4>
		<div style="margin:0 20px">
			<?php

			$ShopList = HOF_Model_Data::getShopList(); //売ってるものデータ

			$goods = new HOF_Class_Item_Style_List();
			$goods->SetID("JS_buy");
			$goods->SetName("type_buy");
			// JSを使用しない。
			if ($this->no_JS_itemlist) $goods->NoJS();
			foreach ($ShopList as $no)
			{
				$item = HOF_Model_Data::getItemData($no);
				$string = '<input type="radio" name="item_no" value="' . $no . '" class="vcent">';
				$string .= "<span style=\"padding-right:10px;width:10ex\">" . HOF_Helper_Global::MoneyFormat($item["buy"]) . "</span>" . HOF_Class_Item::ShowItemDetail($item, false, 1) . "<br />";
				$goods->AddItem($item, $string);
			}
			print ($goods->GetJavaScript("list_buy"));
			print ($goods->ShowSelect());

			print ('<form action="?shop" method="post">' . "\n");
			print ('<div id="list_buy">' . $goods->ShowDefault() . '</div>' . "\n");
			print ('<input type="submit" class="btn" name="shop_buy" value="Buy">' . "\n");
			print ('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)<br />' . "\n");
			print ('<input type="hidden" name="shop_buy" value="1">');
			print ('</form></div>' . "\n");

			print ("<h4>My Items<a name=\"sell\"></a></h4>\n"); //所持物売る
			print ('<div style="margin:0 20px">' . "\n");
			if ($this->item)
			{
				$goods = new HOF_Class_Item_Style_List();
				$goods->SetID("JS_sell");
				$goods->SetName("type_sell");
				// JSを使用しない。
				if ($this->no_JS_itemlist) $goods->NoJS();
				foreach ($this->item as $no => $val)
				{
					$item = HOF_Model_Data::getItemData($no);
					$price = (isset($item["sell"]) ? $item["sell"] : round($item["buy"] * SELLING_PRICE));
					$string = '<input type="radio" class="vcent" name="item_no" value="' . $no . '">';
					$string .= "<span style=\"padding-right:10px;width:10ex\">" . HOF_Helper_Global::MoneyFormat($price) . "</span>" . HOF_Class_Item::ShowItemDetail($item, $val, 1) . "<br />";
					$head = '<input type="radio" name="item_no" value="' . $no . '" class="vcent">' . HOF_Helper_Global::MoneyFormat($item["buy"]);
					$goods->AddItem($item, $string);
				}
				print ($goods->GetJavaScript("list_sell"));
				print ($goods->ShowSelect());

				print ('<form action="?shop" method="post">' . "\n");
				print ('<div id="list_sell">' . $goods->ShowDefault() . '</div>' . "\n");
				print ('<input type="submit" class="btn" name="shop_sell" value="Sell">');
				print ('Amount <input type="text" name="amount" style="width:60px" class="text vcent">(input if 2 or more)' . "\n");
				print ('<input type="hidden" name="shop_sell" value="1">');
				print ('</form>' . "\n");
			}
			else
			{
				print ("No items");
			}
			print ("</div>\n");
			/*
			if($this->item) {
			foreach($this->item as $no => $val) {
			$item	= HOF_Model_Data::getItemData($no);
			$price	= (isset($item["sell"]) ? $item["sell"] : round($item["buy"]*SELLING_PRICE));
			print('<input type="radio" class="vcent" name="item_no" value="'.$no.'">');
			print(HOF_Helper_Global::MoneyFormat($price));
			print("&nbsp;&nbsp;&nbsp;{$val}x");
			HOF_Class_Item::ShowItemDetail($item);
			print("<br>");
			}
			} else
			print("No items.<br>");
			print('Amount <input type="text" name="amount" style="width:50px" class="text vcent">(input if 2 or more)<br />'."\n");
			print('<input type="submit" class="btn vcent" name="shop_sell" value="Sell">');
			print('<input type="hidden" name="shop_sell" value="1">');
			print('</form>');*/


?>
			<form action="?shop" method="post">
				<h4>Work</h4>
				<div style="margin:0 20px">
				店でアルバイトしてお金を得ます...<br />
				<input type="submit" class="btn" name="partjob" value="Work at Shop">
				Get
				<?=

			HOF_Helper_Global::MoneyFormat("500")


?>
				for 100Time.
			</form>
		</div>
	</div>
	<?php

		}




		//





		//


		//


		//

		function SettingProcess()
		{
			if ($_POST["NewName"])
			{
				$NewName = $_POST["NewName"];
				if (is_numeric(strpos($NewName, "\t")))
				{
					HOF_Helper_Global::ShowError('error1');
					return false;
				}
				$NewName = trim($NewName);
				$NewName = stripslashes($NewName);
				if (!$NewName)
				{
					HOF_Helper_Global::ShowError('Name is blank.');
					return false;
				}
				$length = strlen($NewName);
				if (0 == $length || 16 < $length)
				{
					HOF_Helper_Global::ShowError('1 to 16 letters?');
					return false;
				}
				$userName = userNameLoad();
				if (in_array($NewName, $userName))
				{
					HOF_Helper_Global::ShowError("その名前は使用されている。", "margin15");
					return false;
				}
				if (!$this->TakeMoney(NEW_NAME_COST))
				{
					HOF_Helper_Global::ShowError('money not enough');
					return false;
				}
				$OldName = $this->name;
				$NewName = htmlspecialchars($NewName, ENT_QUOTES);
				if ($this->ChangeName($NewName))
				{
					HOF_Helper_Global::ShowResult("Name Changed ({$OldName} -> {$NewName})", "margin15");
					//return false;
					userNameAdd($NewName);
					return true;
				}
				else
				{
					HOF_Helper_Global::ShowError("?"); //名前が同じ？
					return false;
				}
			}

			if ($_POST["setting01"])
			{
				if ($_POST["record_battle_log"]) $this->record_btl_log = 1;
				else  $this->record_btl_log = false;

				if ($_POST["no_JS_itemlist"]) $this->no_JS_itemlist = 1;
				else  $this->no_JS_itemlist = false;
			}
			if ($_POST["color"])
			{
				if (strlen($_POST["color"]) != 6 && !ereg("^[0369cf]{6}", $_POST["color"])) return "error 12072349";
				$this->UserColor = $_POST["color"];
				HOF_Helper_Global::ShowResult("Setting changed.", "margin15");
				return true;
			}
		}

		//	設定表示画面
		function SettingShow()
		{
			print ('<div style="margin:15px">' . "\n");
			if ($this->record_btl_log) $record_btl_log = " checked";
			if ($this->no_JS_itemlist) $no_JS_itemlist = " checked";


?>
	<h4>Setting</h4>
	<form action="?setting" method="post">
		<table>
			<tbody>
				<tr>
					<td><input type="checkbox" name="record_battle_log" value="1" <?=

			$record_btl_log


?>></td>
					<td>戦闘ログの記録</td>
				</tr>
				<tr>
					<td><input type="checkbox" name="no_JS_itemlist" value="1" <?=

			$no_JS_itemlist


?>></td>
					<td>アイテムリストにJavaScriptを使わない</td>
				</tr>
			</tbody>
		</table>
		<!--<tr><td>None</td><td><input type="checkbox" name="none" value="1"></td></tr>-->
		Color :
		<?php

			$color = file(COLOR_FILE);
			print ('<select name="color" class="bgcolor">' . "\n");
			foreach ($color as $value)
			{
				$value = trim($value);
				print ("<option value=\"{$value}\" style=\"color:{$value}\" " . ($this->UserColor == $value ? " selected" : "") . ">");
				print ("SampleColor</option>\n");
			}
			print ('</select>');


?>
		<br />
		<input type="submit" class="btn" name="setting01" value="modify" style="width:100px">
		<input type="hidden" name="setting01" value="1">
	</form>
	<h4>Logout</h4>
	<form action="<?=

			INDEX


?>" method="post">
		<input type="submit" class="btn" name="logout" value="logout" style="width:100px">
	</form>
	<h4>チーム名の変更</h4>
	<form action="?setting" method="post">
		費用 :
		<?=

			HOF_Helper_Global::MoneyFormat(NEW_NAME_COST)


?>
		<br />
		16文字まで(全角=2文字)<br />
		新しい名前 :
		<input type="text" class="text" name="NewName" size="20">
		<input type="submit" class="btn" value="change" style="width:100px">
	</form>
	<h4>脱出口</h4>
	<div class="u">
		※データの削除
	</div>
	<form action="?setting" method="post">
		PassWord :
		<input type="text" class="text" name="deletepass" size="20">
		<input type="submit" class="btn" name="delete" value="delete" style="width:100px">
	</form>
</div>
<?php

			return $Result;
		}
		////////// Show ////
		/*
		* ShowCharStat
		* ShowHunt
		* ShowItem
		* ShowShop
		* ShowRank
		* ShowRecruit
		* ShowSetting
		*/


		//

		////////////////////



		//	ログインした画面
		function LoginMain()
		{
			$this->ShowTutorial();
			$this->ShowMyCharacters();
			RegularControl($this->id);
		}

		//	チュウトリアル
		function ShowTutorial()
		{
			$last = $this->last;
			$start = substr($this->start, 0, 10);
			$term = 60 * 60 * 1;
			if (($last - $start) < $term)
			{


?>
<div style="margin:5px 15px">
	<a href="?tutorial">チュートリアル</a>- 戦闘の基本(登録後,1時間だけ表示されます)
</div>
<?php

			}
		}


		//	自分のキャラを表示する
		function ShowMyCharacters($array = NULL)
		{ // $array ← 色々受け取る
			if (!$this->char) return false;
			$divide = (count($this->char) < CHAR_ROW ? count($this->char) : CHAR_ROW);
			$width = floor(100 / $divide); //各セル横幅

			print ('<table cellspacing="0" style="width:100%"><tbody><tr>'); //横幅100%
			foreach ($this->char as $val)
			{
				if ($i % CHAR_ROW == 0 && $i != 0) print ("\t</tr><tr>\n");
				print ("\t<td valign=\"bottom\" style=\"width:{$width}%\">"); //キャラ数に応じて%で各セル分割
				$val->ShowCharLink($array);
				print ("</td>\n");
				$i++;
			}
			print ("</tr></tbody></table>");
		}







		//	変数の表示
		function Debug()
		{
			if (DEBUG) print ("<pre>" . print_r(get_object_vars($this), 1) . "</pre>");
		}


		//	セッション情報を表示する。
		function ShowSession()
		{
			echo "this->id:$this->id<br>";
			echo "this->pass:$this->pass<br>";
			echo "SES[id]:$_SESSION[id]<br>";
			echo "SES[pass]:$_SESSION[pass]<br>";
			echo "SES[pass]:" . $this->CryptPassword($_SESSION[pass]) . "(crypted)<br>";
			echo "CK[NO]:$_COOKIE[NO]<br>";
			echo "SES[NO]:" . session_id();
			dump($_COOKIE);
			dump($_SESSION);
		}


		//	ログインした時間を設定する
		function RenewLoginTime()
		{
			$this->login = time();
		}

		//	pass と id を設定する
		function Set_ID_PASS()
		{
			$id = ($_POST["id"]) ? $_POST["id"] : $_GET["id"];
			//if($_POST["id"]) {
			if ($id)
			{
				$this->id = $id; //$_POST["id"];
				// ↓ログイン処理した時だけ
				if (is_registered($_POST["id"]))
				{
					$_SESSION["id"] = $this->id;
				}
			}
			else
				if ($_SESSION["id"]) $this->id = $_SESSION["id"];

			$pass = ($_POST["pass"]) ? $_POST["pass"] : $_GET["pass"];
			//if($_POST["pass"])
			if ($pass) $this->pass = $pass; //$_POST["pass"];
			else
				if ($_SESSION["pass"]) $this->pass = $_SESSION["pass"];

			if ($this->pass) $this->pass = $this->CryptPassword($this->pass);
		}


		//	保存されているセッション番号を変更する。
		function SessionSwitch()
		{
			// session消滅の時間(?)
			// how about "session_set_cookie_params()"?
			session_cache_expire(COOKIE_EXPIRE / 60);
			if ($_COOKIE["NO"]) //クッキーに保存してあるセッションIDのセッションを呼び出す
 					session_id($_COOKIE["NO"]);

			session_start();
			if (!SESSION_SWITCH) //switchしないならここで終了
 					return false;
			//print_r($_SESSION);
			//dump($_SESSION);
			$OldID = session_id();
			$temp = serialize($_SESSION);

			session_regenerate_id();
			$NewID = session_id();
			setcookie("NO", $NewID, time() + COOKIE_EXPIRE);
			$_COOKIE["NO"] = $NewID;

			session_id($OldID);
			session_start();

			if ($_SESSION):
				//	session_destroy();//Sleipnirだとおかしい...?(最初期)
				//	unset($_SESSION);//こっちは大丈夫(やっぱりこれは駄目かも)(修正後)
				//結局,セッションをforeachでループして1個づつunset(2007/9/14 再修正)
				foreach ($_SESSION as $key => $val) unset($_SESSION["$key"]);
			endif;

			session_id($NewID);
			session_start();
			$_SESSION = unserialize($temp);
		}


		//

		//


		//	上部に表示されるメニュー。
		//	ログインしてる人用とそうでない人。
		function MyMenu()
		{
			if ($this->name && $this->islogin)
			{ // ログインしてる人用
				print ('<div id="menu">' . "\n");
				//print('<span class="divide"></span>');//区切り
				print ('<a href="' . INDEX . '">Top</a><span class="divide"></span>');
				print ('<a href="?hunt">Hunt</a><span class="divide"></span>');
				print ('<a href="?item">Item</a><span class="divide"></span>');
				print ('<a href="?town">Town</a><span class="divide"></span>');
				print ('<a href="?setting">Setting</a><span class="divide"></span>');
				print ('<a href="?log">Log</a><span class="divide"></span>');
				if (BBS_OUT) print ('<a href="' . BBS_OUT . '">BBS</a><span class="divide"></span>' . "\n");
				print ('</div><div id="menu2">' . "\n");


?>
<div style="width:100%">
	<div style="width:33%;float:left">
		<?=

				$this->name


?>
	</div>
	<div style="width:67%;float:right">
		<div style="width:50%;float:left">
			<span class="bold">Funds</span>:
			<?=

				HOF_Helper_Global::MoneyFormat($this->money)


?>
		</div>
		<div style="width:50%;float:right">
			<span class="bold">Time</span>:
			<?=

				floor($this->time)


?>
			/
			<?=

				MAX_TIME


?>
		</div>
	</div>
	<div class="c-both">
	</div>
</div>
<?php

				print ('</div>');
			}
			else
				if (!$this->name && $this->islogin)
				{ // 初回ログインの人
					print ('<div id="menu">');
					print ("First login. Thankyou for the entry.");
					print ('</div><div id="menu2">');
					print ("fill the blanks. てきとーに埋めてください。");
					print ('</div>');
				}
				else
				{ //// ログアウト状態の人、来客用の表示
					print ('<div id="menu">');
					print ('<a href="' . INDEX . '">トップ</a><span class="divide"></span>' . "\n");
					print ('<a href="?newgame">新規</a><span class="divide"></span>' . "\n");
					print ('<a href="?manual">ルールとマニュアル</a><span class="divide"></span>' . "\n");
					print ('<a href="?gamedata=job">ゲームデータ</a><span class="divide"></span>' . "\n");
					print ('<a href="?log">戦闘ログ</a><span class="divide"></span>' . "\n");
					if (BBS_OUT) print ('<a href="' . BBS_OUT . '">総合BBS</a><span class="divide"></span>' . "\n");

					print ('</div><div id="menu2">');
					print ("Welcome to [ " . TITLE . " ]");
					print ('</div>');
				}
		}


		//	HTML開始部分
		function Head()
		{


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<?php

			$this->HtmlScript();


?>
	<title>
	<?=

			TITLE


?>
	</title>
</head>
<body>
	<a name="top"></a>
	<div id="main_frame">
		<div id="title">
			<img src="<?php

			echo HOF_Class_Icon::getImageUrl('title03', './static/image/');


?>">
		</div>
		<?php

			$this->MyMenu();


?>
		<div id="contents">
			<?php

		}


		//	スタイルシートとか。
		function HtmlScript()
		{


?>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<link rel="stylesheet" href="./static/style/basis.css" type="text/css">
			<link rel="stylesheet" href="./static/style/style.css" type="text/css">
			<script type="text/javascript" src="http://code.jquery.com/jquery-latest.pack.js"></script>
			<script type="text/javascript" src="./static/js/jquery-core.js"></script>
			<style>

.flip-h {
    -moz-transform: scaleX(-1);
    -o-transform: scaleX(-1);
    -webkit-transform: scaleX(-1);
    transform: scaleX(-1);
    filter: FlipH;
    -ms-filter: "FlipH";
}

</style>
			<?php

		}


		//

		//	普通の1行掲示板
		function bbs01()
		{
			if (!BBS_BOTTOM_TOGGLE) return false;
			$file = BBS_BOTTOM;


?>
<div style="margin:15px">
<h4>one line bbs</h4>
バグ報告,バランスについての意見とかはこちらでどうぞ。
<form action="?bbs" method="post">
	<input type="text" maxlength="60" name="message" class="text" style="width:300px"/>
	<input type="submit" value="post" class="btn" style="width:100px" />
</form>
<?php

			if (!file_exists($file)) return false;
			$log = file($file);
			if ($_POST["message"] && strlen($_POST["message"]) < 121)
			{
				$_POST["message"] = htmlspecialchars($_POST["message"], ENT_QUOTES);
				$_POST["message"] = stripslashes($_POST["message"]);

				$name = ($this->name ? "<span class=\"bold\">{$this->name}</span>" : "名無し");
				$message = $name . " > " . $_POST["message"];
				if ($this->UserColor) $message = "<span style=\"color:{$this->UserColor}\">" . $message . "</span>";
				$message .= " <span class=\"light\">(" . gc_date("Mj G:i") . ")</span>\n";
				array_unshift($log, $message);
				while (150 < count($log)) // ログ保存行数あ
 						array_pop($log);
				HOF_Class_File::WriteFile($file, implode(null, $log));
			}
			foreach ($log as $mes) print (nl2br($mes));
			print ('</div>');
		}
		//end of class
		////////////////////
	}


?>