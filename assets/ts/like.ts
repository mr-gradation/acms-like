
import $ from "cash-dom/dist/cash.esm.js";
import Axios from "axios";

const activeClass = '-active';
const votedClass = '-voted';
const countElementSelector = '.count';

/*
/*	いいね！ボタン押下時
*/
$(document).on('click', '.js-like-button', function(e){
  var $like = $(this);
  // いいね済みならキャンセル、そうでなければアニメーション
  if( $like.hasClass(votedClass) ){
    return false;
  } else {
    $like.addClass(activeClass);
    setTimeout(function(){ 
      $like.removeClass(activeClass);
      $like.addClass(votedClass);
      $like.attr('disabled', true);
    }, 500);
  }
  // パラメータを定義
  var eid = $(this).data('like-eid');
  var category = typeof($(this).data('like-category')) !== "undefined" ? $(this).data('like-category') : "good";
  var params = new URLSearchParams();
      params.append('async', '1');
      params.append('mode', 'put');
      params.append('eid', eid);
      params.append('category', category);
      params.append('formToken', "token");
      params.append('ACMS_POST_Like', '');
  // いいねのクリックを送信
  Axios.post("", params).then((res) => {
    var count = res.data.toString();
    $like.find(countElementSelector).text(count);
  });
  return false;
});

$(document).ready(function() {
  /**
   * いいね！ボタンの表示
   */
  $('.js-like-button').each(function(){
    var $like = $(this);
    // パラメータを定義
    var eid = $(this).data('like-eid');
    var category = typeof($(this).data('like-category')) !== "undefined" ? $(this).data('like-category') : "good";
    var params = new URLSearchParams();
        params.append('async', '1');
        params.append('mode', 'get');
        params.append('eid', eid);
        params.append('category', category);
        params.append('formToken', "token");
        params.append('ACMS_POST_Like', '');
    // いいねを取得
    Axios.post("", params).then((res) => {
      var count = res.data.toString();
      if( count.indexOf("_") !== -1 ){
        // いいね済み
        $like.addClass(votedClass);
        $like.attr('disabled', true);
        $like.find(countElementSelector).text(count.replace('_', ''));
      } else {
        // いいねしていない
        $like.find(countElementSelector).text(count);
      }
    });
  });

  /**
   * いいね！数の表示
   */
  $('.js-like-numbers').each(function(){
    var $like = $(this);
    // パラメータを定義
    var eid = $like.data('like-eid');
    var params = new URLSearchParams();
        params.append('async', '1');
        params.append('mode', 'all');
        params.append('eid', eid);
        params.append('formToken', "token");
        params.append('ACMS_POST_Like', '');
    // いいねを取得
    Axios.post("", params).then((res) => {
      Object.keys(res.data).forEach(function (key) {
        if ($like.find('[data-like-category]').length) {
          var $target= $like.find('[data-like-category="'+key+'"]');
          $target.text(res.data[key]);
        } else {
          if (key == 'good') {
            var $target = $like.find(countElementSelector);
            $target.text(res.data[key]);
          }
        }
      });
    });

  });

});
