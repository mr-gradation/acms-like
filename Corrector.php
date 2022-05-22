<?php

namespace Acms\Plugins\Like;

use DB, SQL;

class Corrector
{
    /**
     * count_like
     * いいね数を返す
     *
     * @param  string $eid      - エントリーID（{entry:loop.eid}など）
     * @param  string $category - いいねのカテゴリー
     * @return string           - 校正後の文字列
     */
    public function count_like($eid, $args = array())
    {
        // カテゴリーを取得
        $category = isset($args[0]) ? $args[0] : "good";
        // いいね数を取得
        $DB  = DB::singleton(dsn());
        $SQL = SQL::newSelect('like');
        $SQL->setSelect('COUNT(*)');
        $SQL->addWhereOpr('like_eid', $eid, '=');
        $SQL->addWhereOpr('like_category', $category, '=');
        $one = $DB->query($SQL->get(dsn()), 'one');
        return $one;
    }
}
