<?php

 /*
 *    phpMumbleAdmin (PMA), web php administration tool for murmur ( mumble server daemon ).
 *    Copyright (C) 2010 - 2013  Dadon David. PMA@ipnoz.net
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$TEXT['registername_info'] = 'サーバ名';
$TEXT['host_info'] = 'バーチャルサーバのIPアドレス。<br>再起動必要';
$TEXT['port_info'] = 'バーチャルサーバのポート<br>再起動必要';
$TEXT['password_info'] = '未登録ユーザが接続するためのパスワード。 公開mumbleサーバの場合はここを空欄にしてください。';
$TEXT['timeout_info'] = 'デッドコネクションを切断するまでのタイムアウト（秒単位）';
$TEXT['bandwidth_info'] = '最大帯域（ビット/秒） クライアントが発言を送信できる量。';
$TEXT['users_info'] = '同時に接続可能な最大クライアント数。';
$TEXT['defaultchannel_info'] = 'デフォルトチャンネルのID';
$TEXT['registerpassword_info'] = '公開サーバリストのためのパスワード。';
$TEXT['registerhostname_info'] = '公開サーバリストのためのホスト名。';
$TEXT['registerurl_info'] = '公開サーバリストのためのURL。';
$TEXT['username_info'] = 'ユーザ名を検証するための正規表現。';
$TEXT['channelname_info'] = 'チャンネル名を検証するための正規表現。';
$TEXT['textmessagelength_info'] = 'テキストメッセージの最大文字数。 0で無制限。';
$TEXT['allowhtml_info'] = 'メッセージやユーザコメント、チャンネル説明でHTMLタグを使うことをクライアントを許可します。';
$TEXT['bonjour_info'] = 'bonjourサービンス探索を有効にします。';
$TEXT['certrequired_info'] = 'この設定が有効なら、証明書を持っているユーザのみが接続を許可されます。';
$TEXT['usersperchannel_info'] = 'この設定はすべてのチャンネルで有効です。１チャンネルあたりのユーザ数を制限します。';
$TEXT['rememberchannel_info'] = 'この設定はユーザに最後に接続詞たチャンネルへの再接続を許可します。';
$TEXT['imagemessagelength_info'] = '画像データを含むテキストメッセージ中の最大文字数。 0で無制限。';

$TEXT['reset_param'] = '%s パラメータをリセットする'; // %s = parameter key
$TEXT['enable'] = '有効';
$TEXT['disable'] = '無効';
$TEXT['enabled'] = '有効';
$TEXT['disabled'] = '無効';
$TEXT['invalid_cert'] = '不正な証明書';
$TEXT['confirm_reset_cert'] = '本当に証明書を削除しますか？';
$TEXT['welcometext'] = 'ようこそテキスト';
$TEXT['certificate'] = '証明書';
$TEXT['add_certificate'] = '証明書とプライベートキーを追加する';



?>