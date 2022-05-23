<?php

namespace Acms\Plugins\Like\POST;

use ACMS_POST;
use DB, SQL;

/**
 * テンプレート上では、標準のPOSTモジュールと同様に、
 * '<input type="submit" name="ACMS_POST_Like" value="送信" />' で呼び出されます。
 */
class Like extends ACMS_POST
{
    public $isCSRF = false;

    function post()
    {
        $DB  = DB::singleton(dsn());

        // パラメータを取得
        $eid = $this->Post->get("eid") != null ? $this->Post->get("eid") * 1 : "";
        $mode = $this->Post->get("mode") != null ? $this->Post->get("mode") : "get";
        $category = $this->Post->get("category") != null ? $this->Post->get("category") : "good";
        $async = $this->Post->get("async");

        // パラメータがない場合は中断
        if (!$eid) {
            return false;
        }

        // アカウントIDを取得
        if (SUID) {
            $uid = SUID;
        } else {
            $uid = filter_input(INPUT_COOKIE, 'like_uid');
            if (!$uid) {
                $ticket = md5(uniqid(mt_rand(), true));
                $expire = time() + (60 * 60 * 24 * 365); //1年間間保持
                setcookie('like_uid', $ticket, $expire, '/');
                $uid = $ticket;
            }
        }

        // 投票済みか取得
        $SQL = SQL::newSelect('like');
        $SQL->setSelect('COUNT(*)');
        $SQL->addWhereOpr('like_eid', $eid, '=');
        $SQL->addWhereOpr('like_uid', $uid, '=');
        $SQL->addWhereOpr('like_category', $category, '=');
        $voted = $DB->query($SQL->get(dsn()), 'one');

        // 追加処理
        if ($mode == 'put') {
            if ($voted == 0) {
                $id = $DB->query(SQL::nextval('like_id', dsn(), true), 'seq');
                $SQL = SQL::newInsert('like');
                $SQL->addInsert("like_id", $id);
                $SQL->addInsert("like_eid", $eid);
                $SQL->addInsert("like_uid", $uid);
                $SQL->addInsert("like_category", $category);
                $SQL->addInsert("like_created", date('Y-m-d H:i:s', REQUEST_TIME));
                $DB->query($SQL->get(dsn()), 'exec');
            }
        }

        // Ajaxからの送信の場合、数値のみ返す
        if ($async) {
            if ($mode == 'all') {
                // 全カテゴリーの値をJSON形式で返す
                $SQL = SQL::newSelect('like');
                $SQL->addSelect('like_category');
                $SQL->addSelect('COUNT(`like_category`)');
                $SQL->addGroup('like_category');
                $SQL->addWhereOpr('like_eid', $eid, '=');
                $all = $DB->query($SQL->get(dsn()), 'all');
                $result = [];
                foreach ($all as $key => $value) {
                    $result[$value['like_category']] = $value["COUNT(`like_category`)"];
                }
                echo json_encode($result);
            } else {
                // 特定の値を返す
                $SQL = SQL::newSelect('like');
                $SQL->setSelect('COUNT(*)');
                $SQL->addWhereOpr('like_eid', $eid, '=');
                $SQL->addWhereOpr('like_category', $category, '=');
                $count = $DB->query($SQL->get(dsn()), 'one');
                // 投票済みの場合は、アンダースコア付きで返す
                if ($voted > 0) {
                    echo '_' . $count;
                } else {
                    echo $count;
                }
            }
            exit();
        }

        return $this->Post;
    }
}
