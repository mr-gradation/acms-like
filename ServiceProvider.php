<?php

namespace Acms\Plugins\Like;

use ACMS_App;
use DB;
use SQL;
use Acms\Services\Common\CorrectorFactory;
use Acms\Services\Facades\Storage;
use Acms\Services\Facades\Config;

class ServiceProvider extends ACMS_App
{
    /**
     * アプリの設定
     */
    public $version = '1.0.0';
    public $name = 'Like';
    public $author = 'Mr. Gradation';
    public $module = false;
    public $menu = false;
    public $desc = 'a-blog cmsのサイトにいいね！ボタンを追加できます。';
    
    /**
     * データベースの設定
     */
    protected $installTable = array(
        'like'
    );
    protected $sequence_key = array(
        'sequence_like_id',
    );

    /**
     * サービスの初期処理
     */
    public function init()
    {
        $corrector = CorrectorFactory::singleton();
        $corrector->attach('LikeCorrector', new Corrector);
    }

    /**
     * インストールする前の環境チェック処理
     *
     * @return bool
     */
    public function checkRequirements()
    {
        return true;
    }

    /**
     * インストールするときの処理
     * データベーステーブルの初期化など
     *
     * @return void
     */
    public function install()
    {
        //------------
        //テーブル削除
        dbDropTables($this->installTable);

        //---------------------
        // テーブルデータ読み込み
        $yamlTable = preg_replace('/%{PREFIX}/', DB_PREFIX,
            Storage::get(dirname(__FILE__) . '/schema/db-schema.yaml'));
        $tablesData = Config::yamlParse($yamlTable);
        if (!is_array($tablesData)) {
            $tablesData = array();
        }
        if (!empty($tablesData[0])) {
            unset($tablesData[0]);
        }
        $tableList = array_merge(array_diff(array_keys($tablesData), array('')));

        $yamlIndex = preg_replace('/%{PREFIX}/', DB_PREFIX,
            Storage::get(dirname(__FILE__) . '/schema/db-index.yaml'));
        $indexData = Config::yamlParse($yamlIndex);
        if (!is_array($indexData)) {
            $indexData = array();
        }
        if (!empty($indexData[0])) {
            unset($indexData[0]);
        }

        //---------------
        // テーブル作成
        foreach ($tableList as $tb) {
            $index = isset($indexData[$tb]) ? $indexData[$tb] : null;
            dbCreateTables($tb, $tablesData[$tb], $index);
        }

        //---------------
        // 初期データ生成
        $DB = DB::singleton(dsn());
        foreach ( $this->sequence_key as $key ) {
            $SQL = SQL::newInsert('sequence_plugin');
            $SQL->addInsert('sequence_plugin_key', $key);
            $SQL->addInsert('sequence_plugin_value', 1);
            $DB->query($SQL->get(dsn()), 'exec');
        }
    }

    /**
     * アンインストールするときの処理
     * データベーステーブルの始末など
     *
     * @return void
     */
    public function uninstall()
    {
        dbDropTables($this->installTable);
        
        $DB = DB::singleton(dsn());
        foreach ( $this->sequence_key as $key ) {
            $SQL    = SQL::newDelete('sequence_plugin');
            $SQL->addWhereOpr('sequence_plugin_key', $key);
            $DB->query($SQL->get(dsn()), 'exec');
        }
    }

    /**
     * アップデートするときの処理
     *
     * @return bool
     */
    public function update()
    {
        return true;
    }

    /**
     * 有効化するときの処理
     *
     * @return bool
     */
    public function activate()
    {
        return true;
    }

    /**
     * 無効化するときの処理
     *
     * @return bool
     */
    public function deactivate()
    {
        return true;
    }
}
