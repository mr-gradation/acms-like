<?php

namespace Acms\Plugins\Like\GET;

use ACMS_GET;
use DB, SQL;
use Template;
use ACMS_Corrector;

/**
 * テンプレート上では、標準のGETモジュールと同様に、
 * '<!-- BEGIN_MODULE Like -->{count}<!--END_MODULE Like -->' で呼び出されます。
 */
class Like extends ACMS_GET
{

    var $_axis = array(
        'eid' => 'self',
    );

    function get()
    {
        $Tpl = new Template($this->tpl, new ACMS_Corrector());

        // エントリーIDを取得
        if ($this->eid) {
            $eid = $this->eid;
        } else if (EID) {
            $eid = EID;
        } else {
            return false;
        }

        // いいね数を取得
        $DB  = DB::singleton(dsn());
        $SQL = SQL::newSelect('like');
        $SQL->addSelect('like_category');
        $SQL->addSelect('COUNT(`like_category`)');
        $SQL->addSelect('like_category', 'amount', null, 'COUNT');
        $SQL->addWhereOpr('like_eid', $eid, '=');
        $SQL->addGroup('like_category');
        $all = $DB->query($SQL->get(dsn()), 'all');

        // テンプレートに格納
        $count = [];
        foreach ($all as $key => $value) {
            $key = $value['like_category'];
            $count[$key] = $value['amount'];
            if ($key === 'good') {
                $count['count'] = $value['amount'];
            }
        }

        return $Tpl->render($count);
    }
}
