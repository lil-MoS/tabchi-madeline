<?php 
/*

@GPYAB
https://t.me/GPYAB
*/
ini_set('display_errors', 0);
error_reporting(0);
if (file_exists('atom.madeline') && file_exists('update/atom.madeline') && (time() - filectime('atom.madeline')) > 120) {
unlink('atom.madeline.lock');
unlink('atom.madeline');
unlink('madeline.phar.version');
unlink('madeline.php');
unlink('MadelineProto.log');
unlink('bot.lock');
copy('update/atom.madeline', 'atom.madeline');
}
if (!file_exists('member.json')) {
file_put_contents('member.json', '{"list":{}}');
}
if (!file_exists('data.json')) {
file_put_contents('data.json', '{"autochatpv":"off","autochatgroup":"off","autojoin":"on","autosave":"on","admins":{}}');
}
if (!file_exists('SEND.json')) {
file_put_contents('SEND.json', '{"list":{}}');
}
if (!is_dir('update')) {
mkdir('update');
}
if (!file_exists('madeline.php')) {
copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';
$settings['logger']['logger'] = 0;
$settings['serialization']['serialization_interval'] = 30;
$MadelineProto = new \danog\MadelineProto\API('atom.madeline', $settings);
$MadelineProto->start();
class EventHandler extends \danog\MadelineProto\EventHandler
{
public function construct($MadelineProto)
{
parent::construct($MadelineProto);
}
public function onUpdateNewChannelMessage($update)
{
yield $this->onUpdateNewMessage($update);
}
public function onUpdateNewMessage($update)
{
if (!file_exists('update/atom.madeline')) {
copy('atom.madeline', 'update/atom.madeline');
}
$userID = isset($update['message']['from_id']) ? $update['message']['from_id'] : '';
$msg = isset($update['message']['message']) ? $update['message']['message'] : '';
$msg_id = isset($update['message']['id']) ? $update['message']['id'] : '';
$me = yield $this->get_self();
$me_id = $me['id'];
$chID = yield $this->get_info($update);
$chatID = $chID['bot_api_id'];
$type = $chID['type'];
@$data = json_decode(file_get_contents("data.json"), true);
@$member = json_decode(file_get_contents("member.json"), true);
@$SEND = json_decode(file_get_contents("SEND.json"), true);
$admin = "208549102";// آیدی ادمین اینجا جایگذین نماید.
try {
if (strpos($msg, 't.me/joinchat/') !== false && @$data['autojoin'] == 'on') {
$a = explode('t.me/joinchat/', "$msg")[1];
$b = explode("\n", "$a")[0];
try {
yield $this->channels->joinchannel(['channel' => "https://t.me/joinchat/$b"]);
yield $this->messages->sendMessage(['peer' => $admin, 'message' => '🚶‍♂️ Join to a group!']);
} catch (Exception $p) {
} catch (\danog\MadelineProto\RPCErrorException $p) {
}
}
if ($userID == $admin || isset($data['admins'][$userID])) {
if (preg_match('/^\/?(Sendgroup)$/ui', $msg)) {
if (isset($update['message']['reply_to_msg_id'])) {
$rid = $update['message']['reply_to_msg_id'];
if ($type == "supergroup" || $type == "channel") {
$messeg = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$rid], ]);
} else {
$messeg = yield $this->messages->getMessages(['id' => [$rid], ]);
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$messeg = $messeg['messages'][0];
if (!isset($messeg['media'])) {
$text = (isset($messeg['message'])) ? $messeg['message'] : null;
} else {
$media = $messeg['media'];
$text = (isset($messeg['message'])) ? $messeg['message'] : null;

}
$i = 0;
$dialogs = yield $this->get_dialogs();
foreach ($dialogs as $peer) {
$type = yield $this->get_info($peer);
$type3 = $type['type'];
try {
if ($type3 == 'supergroup' || $type3 == 'chat') {
if (!isset($media)) {
yield $this->messages->sendMessage(['peer' => $peer, 'message' => $text, 'parse_mode' => 'Markdown']);
} else {
yield $this->messages->sendMedia(['peer' => $peer, 'message' => $text, 'media' => $media, 'parse_mode' => 'Markdown']);
}
$i++;
}
} catch (\danog\MadelineProto\RPCErrorException $e) {
if (strpos($e->getMessage(), "FLOOD_WAIT_") !== false) {
$time = str_replace("FLOOD_WAIT_", "", $e->getMessage());
$t = $time / 60;
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⏰ wait $t minet"]);
break;
} elseif ($e->getMessage() == "PEER_FLOOD") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛔ Telegram Ristrect"]);
break;
}
yield $this->messages->sendMessage(['peer' => $admin, 'message' => '❗️<code>' . $e->getMessage() . '</code>', 'parse_mode' => 'html']);
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "📬 Post send to $i groups!"]);
}
} elseif (preg_match('/^\/?(sendpv)$/ui', $msg)) {
if (isset($update['message']['reply_to_msg_id'])) {
$rid = $update['message']['reply_to_msg_id'];
if ($type == "supergroup" || $type == "channel") {
$messeg = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$rid], ]);
} else {
$messeg = yield $this->messages->getMessages(['id' => [$rid], ]);
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$messeg = $messeg['messages'][0];
if (!isset($messeg['media'])) {
$text = (isset($messeg['message'])) ? $messeg['message'] : null;
} else {
$media = $messeg['media'];
$text = (isset($messeg['message'])) ? $messeg['message'] : null;

}
$i = 0;
$dialogs = yield $this->get_dialogs();
foreach ($dialogs as $peer) {
$type = yield $this->get_info($peer);
$type3 = $type['type'];
try {
if ($type3 == 'user') {
if (!isset($media)) {
yield $this->messages->sendMessage(['peer' => $peer, 'message' => $text, 'parse_mode' => 'Markdown']);
} else {
yield $this->messages->sendMedia(['peer' => $peer, 'message' => $text, 'media' => $media, 'parse_mode' => 'Markdown']);
}
$i++;
}
} catch (\danog\MadelineProto\RPCErrorException $e) {
if (strpos($e->getMessage(), "FLOOD_WAIT_") !== false) {
$time = str_replace("FLOOD_WAIT_", "", $e->getMessage());
$t = $time / 60;
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⏰ wait $t minet"]);
break;
} elseif ($e->getMessage() == "PEER_FLOOD") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛔ Telegram Ristrect"]);
break;
}
yield $this->messages->sendMessage(['peer' => $admin, 'message' => '❗️<code>' . $e->getMessage() . '</code>', 'parse_mode' => 'html']);
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "📬 Post send to $i pv!"]);
}
} elseif (preg_match('/^\/?(sendall)$/ui', $msg)) {
if (isset($update['message']['reply_to_msg_id'])) {
$rid = $update['message']['reply_to_msg_id'];
if ($type == "supergroup" || $type == "channel") {
$messeg = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$rid], ]);
} else {
$messeg = yield $this->messages->getMessages(['id' => [$rid], ]);
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$messeg = $messeg['messages'][0];
if (!isset($messeg['media'])) {
$text = (isset($messeg['message'])) ? $messeg['message'] : null;
} else {
$media = $messeg['media'];
$text = (isset($messeg['message'])) ? $messeg['message'] : null;

}
$i = 0;
$dialogs = yield $this->get_dialogs();
foreach ($dialogs as $peer) {
$type = yield $this->get_info($peer);
$type3 = $type['type'];
try {
if ($type3 == 'user' || $type3 == "supergroup" || $type3 == "chat") {
if (!isset($media)) {
yield $this->messages->sendMessage(['peer' => $peer, 'message' => $text, 'parse_mode' => 'Markdown']);
} else {
yield $this->messages->sendMedia(['peer' => $peer, 'message' => $text, 'media' => $media, 'parse_mode' => 'Markdown']);
}
$i++;
}
} catch (\danog\MadelineProto\RPCErrorException $e) {
if (strpos($e->getMessage(), "FLOOD_WAIT_") !== false) {
$time = str_replace("FLOOD_WAIT_", "", $e->getMessage());
$t = $time / 60;
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⏰ wait $t minet"]);
break;
} elseif ($e->getMessage() == "PEER_FLOOD") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛔ Telegram Ristrect"]);
break;
}
yield $this->messages->sendMessage(['peer' => $admin, 'message' => '❗️<code>' . $e->getMessage() . '</code>', 'parse_mode' => 'html']);
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "📬 Post send to $i groups,supergroup and pv!"]);
}
} elseif (preg_match('/^\/?(CleanSENDList)$/ui', $msg)) {
unlink('SEND.json');
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "🗑 list Removed"]);
} else if (preg_match('/^\/?(SendMember)$/ui', $msg)) {
if (isset($update['message']['reply_to_msg_id'])) {
$rid = $update['message']['reply_to_msg_id'];
if ($type == "supergroup" || $type == "channel") {
$messeg = yield $this->channels->getMessages(['channel' => $peer, 'id' => [$rid], ]);
} else {
$messeg = yield $this->messages->getMessages(['id' => [$rid], ]);
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$messeg = $messeg['messages'][0];
if (!isset($messeg['media'])) {
$text = (isset($messeg['message'])) ? $messeg['message'] : null;
} else {
$media = $messeg['media'];
$text = (isset($messeg['message'])) ? $messeg['message'] : null;
}
$i = 0;
foreach ($member['list'] as $id) {
if (!in_array($id, $SEND['list'])) {
$SEND['list'][] = $id;
file_put_contents("SEND.json", json_encode($SEND));
try {
if (!isset($media)) {
yield $this->messages->sendMessage(['peer' => $id, 'message' => $text, 'parse_mode' => 'Markdown']);
} else {
yield $this->messages->sendMedia(['peer' => $id, 'message' => $text, 'media' => $media, 'parse_mode' => 'Markdown']);
}
$i++;
} catch (danog\MadelineProto\RPCErrorException $e) {
if (strpos($e->getMessage(), "FLOOD_WAIT_") !== false) {
$time = str_replace("FLOOD_WAIT_", "", $e->getMessage());
$t = $time / 60;
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⏰ wait $t minet"]);
break;
} elseif ($e->getMessage() == "PEER_FLOOD") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛔ Telegram Ristrect"]);
break;
}
yield $this->messages->sendMessage(['peer' => $admin, 'message' => '❗️<code>' . $e->getMessage() . '</code>', 'parse_mode' => 'html']);
}
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "📬 Post send to $i Member"]);
}
} elseif (preg_match('/^\/?(forwardpv)$/ui', $msg)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$rid = $update['message']['reply_to_msg_id'];
$dialogs = yield $this->get_dialogs();
$i = 0;
foreach ($dialogs as $peer) {
$type = yield $this->get_info($peer);
if ($type['type'] == 'user') {
try {
yield $this->messages->forwardMessages(['from_peer' => $chatID, 'to_peer' => $peer, 'id' => [$rid]]);
$i++;
} catch (\danog\MadelineProto\RPCErrorException $e) {
if (strpos($e->getMessage(), "FLOOD_WAIT_") !== false) {
$time = str_replace("FLOOD_WAIT_", "", $e->getMessage());
$t = $time / 60;
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⏰ wait $t minet"]);
break;
} elseif ($e->getMessage() == "PEER_FLOOD") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛔ Telegram Ristrect"]);
break;
}
yield $this->messages->sendMessage(['peer' => $admin, 'message' => '❗️<code>' . $e->getMessage() . '</code>', 'parse_mode' => 'html']);
}
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "📬 Post forwarded to $i pv"]);
} elseif (preg_match('/^\/?(forwardgroup)$/ui', $msg)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$rid = $update['message']['reply_to_msg_id'];
$dialogs = yield $this->get_dialogs();
$i = 0;
foreach ($dialogs as $peer) {
$type = yield $this->get_info($peer);
if ($type['type'] == 'supergroup' || $type['type'] == 'chat') {
try {
yield $this->messages->forwardMessages(['from_peer' => $chatID, 'to_peer' => $peer, 'id' => [$rid]]);
$i++;
} catch (\danog\MadelineProto\RPCErrorException $e) {
if (strpos($e->getMessage(), "FLOOD_WAIT_") !== false) {
$time = str_replace("FLOOD_WAIT_", "", $e->getMessage());
$t = $time / 60;
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⏰ wait $t minet"]);
break;
} elseif ($e->getMessage() == "PEER_FLOOD") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛔ Telegram Ristrect"]);
break;
}
yield $this->messages->sendMessage(['peer' => $admin, 'message' => '❗️<code>' . $e->getMessage() . '</code>', 'parse_mode' => 'html']);
}
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "📬 Post forwarded to $i groups"]);
} elseif (preg_match('/^\/?(forwardall)$/ui', $msg)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$rid = $update['message']['reply_to_msg_id'];
$dialogs = yield $this->get_dialogs();
$i = 0;
foreach ($dialogs as $peer) {
$type = yield $this->get_info($peer);
if ($type['type'] == 'user' || $type['type'] == 'supergroup' || $type['type'] == 'chat') {
try {
yield $this->messages->forwardMessages(['from_peer' => $chatID, 'to_peer' => $peer, 'id' => [$rid]]);
$i++;
} catch (\danog\MadelineProto\RPCErrorException $e) {
if (strpos($e->getMessage(), "FLOOD_WAIT_") !== false) {
$time = str_replace("FLOOD_WAIT_", "", $e->getMessage());
$t = $time / 60;
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⏰ wait $t minet"]);
break;
} elseif ($e->getMessage() == "PEER_FLOOD") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛔ Telegram Ristrect"]);
break;
}
yield $this->messages->sendMessage(['peer' => $admin, 'message' => '❗️<code>' . $e->getMessage() . '</code>', 'parse_mode' => 'html']);
}
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "📬 Post forwarded to $i groups and pv"]);
} elseif (preg_match('/^\/?(forwardmember)$/ui', $msg)) {
if (isset($update['message']['reply_to_msg_id'])) {
$rid = $update['message']['reply_to_msg_id'];

$i = 0;
foreach ($member['list'] as $id) {
if (!in_array($id, $SEND['list'])) {
$SEND['list'][] = $id;
file_put_contents("SEND.json", json_encode($SEND));
try {
yield $this->messages->forwardMessages(['from_peer' => $chatID, 'to_peer' => $id, 'id' => [$rid]]);
$i++;
} catch (danog\MadelineProto\RPCErrorException $e) {
if (strpos($e->getMessage(), "FLOOD_WAIT_") !== false) {
$time = str_replace("FLOOD_WAIT_", "", $e->getMessage());
$t = $time / 60;
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⏰ wait $t minet"]);
break;
} elseif ($e->getMessage() == "PEER_FLOOD") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛔ Telegram Ristrect"]);
break;
}
yield $this->messages->sendMessage(['peer' => $admin, 'message' => '❗️<code>' . $e->getMessage() . '</code>', 'parse_mode' => 'html']);
}
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "📬 Post forward to $i Member"]);
}
} elseif (preg_match('/^\/?(autoforward) (.*)$/ui', $msg)) {
if (isset($update['message']['reply_to_msg_id'])) {
preg_match('/^\/?(autoforward) (.*)$/ui', $msg, $text1);
if ($text1[2] < 10) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '**❗️خطا: عدد وارد شده باید بیشتر از 10 دقیقه باشد.**', 'parse_mode' => 'MarkDown']);
} else {
$time = $text1[2] * 60;
if (!is_dir('ForTime')) {
mkdir('ForTime');
}
file_put_contents("ForTime/msgid.txt", $update['message']['reply_to_msg_id']);
file_put_contents("ForTime/chatid.txt", $chatID);
file_put_contents("ForTime/time.txt", $time);
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "✅ فروارد زماندار باموفقیت روی این پُست درهر $text1[2] دقیقه تنظیم شد.", 'reply_to_msg_id' => $update['message']['reply_to_msg_id']]);
}
}
} elseif (preg_match('/^\/?(deleteforward)$/ui', $msg)) {
foreach (glob("ForTime/*") as $files) {
unlink("$files");
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '✅ Removed !', 'reply_to_msg_id' => $msg_id]);
} elseif (preg_match('/^\/?(forwarddev) (on|off)$/ui', $msg, $m)) {
$data['autosave'] = $m[2];
file_put_contents("data.json", json_encode($data));
if ($m[2] == 'on') {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '✅ Forward to admin actived!', 'reply_to_msg_id' => $msg_id]);
} else {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '❌ Forward to admin deactived!', 'reply_to_msg_id' => $msg_id]);
}
} elseif (preg_match('/^\/?(export) (.*)$/ui', $msg, $text1)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛏ Extracting..."]);
$chat = yield $this->getPwrChat($text1[2]);
$i = 0;
foreach ($chat['participants'] as $pars) {
$id = $pars['user']['id'];
if (!in_array($id, $member['list'])) {
$member['list'][] = $id;
file_put_contents("member.json", json_encode($member));
$i++;
}
if ($i == 1000) break;
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "✅ Done $i member Extracted.if want more send agien"]);
} elseif (preg_match('/^\/?(add) (.*)$/ui', $msg, $text1)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "🔄 Extracted Member is adding..."]);
$gpid = $text1[2];
if (!file_exists("$gpid.json")) {
file_put_contents("$gpid.json", '{"list":{}}');
}
@$addmember = json_decode(file_get_contents("$gpid.json"), true);
$c = 0;
$add = 0;
foreach ($member['list'] as $id) {
if (!in_array($id, $addmember['list'])) {
$addmember['list'][] = $id;
file_put_contents("$gpid.json", json_encode($addmember));
$c++;
try {
yield $this->channels->inviteToChannel(['channel' => $gpid, 'users' => ["$id"]]);
$add++;
} catch (danog\MadelineProto\RPCErrorException $e) {
if ($e->getMessage() == "PEER_FLOOD") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "⛔ Telegram Ristrect"]);
break;
}
}
}

}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "✅ $add Member add successfuly , Total try $c"]);
} elseif (preg_match('/^\/?(addall) (.*)$/ui', $msg, $text1)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$user = $text1[2];
$dialogs = yield $this->get_dialogs();
$i = 0;
foreach ($dialogs as $peer) {
$type = yield $this->get_info($peer);
$type3 = $type['type'];
if ($type3 == 'supergroup') {
try {
yield $this->channels->inviteToChannel(['channel' => $peer, 'users' => ["$user"]]);
$i++;
} catch (danog\MadelineProto\RPCErrorException $e) {
}
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "✅ user added to $i groups.", 'parse_mode' => 'MarkDown']);
} elseif (preg_match('/^\/?(addpv) (.*)$/ui', $msg, $text1)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$gpid = $text1[2];
$dialogs = yield $this->get_dialogs();
$add = 0;
foreach ($dialogs as $peer) {
$type = yield $this->get_info($peer);
$type3 = $type['type'];
if ($type3 == 'user') {
$pvid = $type['user_id'];
try {
yield $this->channels->inviteToChannel(['channel' => $gpid, 'users' => [$pvid]]);
$add++;
} catch (danog\MadelineProto\RPCErrorException $e) {
}
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "✅ $add Member added to $gpid"]);
} elseif (preg_match('/^\/?(deletemember)$/ui', $msg)) {
$member['list'] = [];
file_put_contents("member.json", json_encode($member));
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "🗑 Removed!"]);
} elseif (preg_match('/^\/?(clean)$/ui', $msg)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$all = yield $this->get_dialogs();
$i = 0;
foreach ($all as $peer) {
$type = yield $this->get_info($peer);
if ($type['type'] == 'supergroup') {
$info = yield $this->channels->getChannels(['id' => [$peer]]);
@$banned = $info['chats'][0]['banned_rights']['send_messages'];
if ($banned == 1) {
yield $this->channels->leaveChannel(['channel' => $peer]);
$i++;
}
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "✅ $i Groups Lefted!"]);
} elseif (preg_match('/^\/?(cleangroup)$/ui', $msg)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$all = yield $this->get_dialogs();
$count = 0;
foreach ($all as $peer) {
try {
$type = yield $this->get_info($peer);
$type3 = $type['type'];
if ($type3 == 'supergroup' || $type3 == 'chat') {
$id = $type['bot_api_id'];
if ($chatID != $id) {
yield $this->channels->leaveChannel(['channel' => $id]);
$count++;
}
}
} catch (\danog\MadelineProto\RPCErrorException $e) {
}
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "✅ $count Group lefted!"]);
} elseif (preg_match('/^\/?(cleanchannel)$/ui', $msg)) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '🔄 Please Wait...', 'reply_to_msg_id' => $msg_id]);
$count = 0;
$all = yield $this->get_dialogs();
foreach ($all as $peer) {
$type = yield $this->get_info($peer);
$type3 = $type['type'];
if ($type3 == 'channel') {
$id = $type['bot_api_id'];
yield $this->channels->leaveChannel(['channel' => $id]);
$count++;
}
}
yield $MadelineProto->messages->sendMessage(['peer' => $chatID, 'message' => "✅ $count Channel lefted!"]);
} elseif (preg_match('/^\/?(autochatpv) (on|off)$/ui', $msg, $m)) {
$data['autochatpv'] = $m[2];
file_put_contents("data.json", json_encode($data));
if ($m[2] == 'on') {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '✅ Auto Chat pv actived!']);
} else {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '❌ Auto Chat pv deactived!']);
}
} elseif (preg_match('/^\/?(autochatgroup) (on|off)$/ui', $msg, $m)) {
$data['autochatgroup'] = $m[2];
file_put_contents("data.json", json_encode($data));
if ($m[2] == 'on') {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '✅ Auto Chat Group actived!']);
} else {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '❌ Auto Chat Group deactived!']);
}
} elseif (preg_match('/^\/?(autojoin) (on|off)$/ui', $msg, $m)) {
$data['autojoin'] = $m[2];
file_put_contents("data.json", json_encode($data));
if ($m[2] == 'on') {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '✅ Auto join actived!']);
} else {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '❌ Auto join deactived!']);
}
} elseif (preg_match('/^\/?(join) (.*)$/ui', $msg, $text1)) {
$id = $text1[2];
try {
yield $this->channels->joinChannel(['channel' => "$id"]);
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '✅ Joined', 'reply_to_msg_id' => $msg_id]);
} catch (Exception $e) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '❗️<code>' . $e->getMessage() . '</code>', 'parse_mode' => 'html', 'reply_to_msg_id' => $msg_id]);
}
} elseif ($msg == 'ورژن ربات') {
yield $this->messages->sendMessage(['peer' => $chatID, 'reply_to_msg_id' => $msg_id, 'message' => '**⚙️ نسخه سورس تبچی  : 1.1**', 'parse_mode' => 'MarkDown']);
} elseif ($msg == 'شناسه' || $msg == 'ایدی' || $msg == 'مشخصات') {
$name = $me['first_name'];
$phone = '+' . $me['phone'];
yield $this->messages->sendMessage(['peer' => $chatID, 'reply_to_msg_id' => $msg_id, 'message' => "💚 مشخصات من

👑 ادمین‌اصلی: [$admin](tg://user?id=$admin)
👤 نام: $name
#⃣ ایدی‌عددیم: `$me_id`
📞 شماره‌تلفنم: `$phone`
", 'parse_mode' => 'MarkDown']);
} elseif ($msg == "رستارت" || $msg == "restart") {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "🔄 ربات دوباره راه اندازی شد."]);
yield $this->messages->deleteHistory(['just_clear' => false, 'revoke' => true, 'peer' => $chatID, 'max_id' => $msg_id]);
$this->restart();
} elseif (preg_match('/^\/?(name) (.*)$/ui', $msg, $text1)) {
$new = $text1[2];
yield $this->account->updateProfile(['first_name' => "$new"]);
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "🔸نام جدید : $new"]);
} elseif (preg_match('/^\/?(lastname) (.*)$/ui', $msg, $text1)) {
$new = $text1[2];
yield $this->account->updateProfile(['last_name' => "$new"]);
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "🔹نام خانوادگی جدید تبچی: $new"]);
} elseif (preg_match('/^\/?(bio) (.*)$/ui', $msg, $text1)) {
$new = $text1[2];
yield $this->account->updateProfile(['about' => "$new"]);
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "🔸بیوگرافی جدید تبچی: $new"]);
} elseif ($msg == 'ربات' || $msg == 'ping' || $msg == 'انلاین') {

yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "💥 ATOM <b>5.4</b> is ONLINE 💥", 'parse_mode' => 'html', 'reply_to_msg_id' => $msg_id]);
} elseif (preg_match('/^\/?(addadmin) (.*)$/ui', $msg, $text1)) {
$id = $text1[2];
if (!isset($data['admins'][$id])) {
$data['admins'][$id] = $id;
file_put_contents("data.json", json_encode($data));
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '👨‍💻 New Admin added!']);
} else {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "👨‍💻 This Admin saved Befor!"]);
}
} elseif (preg_match('/^\/?(CleanList)$/ui', $msg, $text1)) {
$data['admins'] = [];
file_put_contents("data.json", json_encode($data));
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "لیست ادمین خالی شد !"]);
} elseif (preg_match('/^\/?(adminlist)$/ui', $msg, $text1)) {
if (count($data['admins']) > 0) {
$txxxt = "لیست ادمین ها :";
$counter = 1;
foreach ($data['admins'] as $k) {
$txxxt .= "$counter: <code>$k</code>\n";
$counter++;
}
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => $txxxt, 'parse_mode' => 'html']);
} else {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "👨‍💻 No Admins !"]);
}
} elseif ($msg == 'امار' || $msg == 'آمار' || $msg == 'stats') {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => 'لطفا کمی صبر کنید...', 'reply_to_msg_id' => $msg_id]);
$mem_using = round((memory_get_usage() / 1024) / 1024, 0) . 'MB';
$mem_total = 'NotAccess!';
$CpuCores = 'NotAccess!';
try {
if (strpos(@$_SERVER['SERVER_NAME'], '000webhost') === false) {
if (strpos(PHP_OS, 'L') !== false || strpos(PHP_OS, 'l') !== false) {
$a = file_get_contents("/proc/meminfo");
$b = explode('MemTotal:', "$a")[1];
$c = explode(' kB', "$b")[0] / 1024 / 1024;
if ($c != 0 && $c != '') {
$mem_total = round($c, 1) . 'GB';
} else {
$mem_total = 'NotAccess!';
}
} else {
$mem_total = 'NotAccess!';
}
if (strpos(PHP_OS, 'L') !== false || strpos(PHP_OS, 'l') !== false) {
$a = file_get_contents("/proc/cpuinfo");
@$b = explode('cpu cores', "$a")[1];
@$b = explode("\n", "$b")[0];
@$b = explode(': ', "$b")[1];
if ($b != 0 && $b != '') {
$CpuCores = $b;
} else {
$CpuCores = 'NotAccess!';
}
} else {
$CpuCores = 'NotAccess!';
}
}
} catch (Exception $f) {
}
$ch = 0;
$sgps = 0;
$gps = 0;
$pvs = 0;
$dgs = yield $this->getFullDialogs();
foreach ($dgs as $dg) {
if (isset($dg['peer'])) {
$peer = $dg['peer'];
$info = yield $this->getInfo($peer);
$type = $info['type'];
switch ($type) {
case "channel":
$ch++;
break;
case "user":
$pvs++;
break;
case "chat":
$gps++;
break;
case "supergroup":
$sgps++;
break;
default:
continue;
}
}
}
$all = $ch + $sgps + $gps + $pvs;
$list = count($member['list']);
$SENDlist = count($SEND['list']);
$gp = $data['autochatgroup'];
$pv = $data['autochatpv'];
$save = $data['autosave'];
$join = $data['autojoin'];
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => "Sᴛᴀᴛs :

❖ Aʟʟ : $all

⫹⫺ CHᴀɴɴᴇʟ  :「<b>$ch</b>」 
↯
⫹⫺ SᴜᴘᴇʀGʀᴏᴜᴘ :「<b>$sgps</b>」 
↯
⫹⫺ NᴏʀᴍᴀʟGʀᴏᴜᴘ :「<b>$gps</b>」
↯
⫹⫺ Usᴇʀ :「<b>$pvs</b>」
↯
⫹⫺ SENDʟɪsᴛ :「<b>$SENDlist</b>」
↯
⫹⫺ FORWARD DEV :「<b>$save</b>」
↯
⫹⫺ AUTOJOIN :「<b>$join</b>」
↯
⫹⫺ AUTOCHAT Group :「<b>$gp</b>」
↯
⫹⫺ AUTOCHAT pv :「<b>$pv</b>」
↯
⫹⫺ CPU Cores :「<b>$CpuCores</b>」
↯
⫹⫺ MemTotal :「<b>$mem_total</b>」
↯
⫹⫺ MemUsage :「<b>$mem_using</b>」", 'parse_mode' => 'html']);
if ($sgps > 400 || $pvs > 1500) {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '⚠️ اخطار: به دلیل کم بودن منابع هاست تعداد گروه ها نباید بیشتر از 400 و تعداد پیوی هاهم نباید بیشتراز 1.5K باشد.
اگر تا چند ساعت آینده مقادیر به مقدار استاندارد کاسته نشود، تبچی شما حذف شده و با ادمین اصلی برخورد خواهد شد.']);
}
} elseif ($msg == 'راهنما') {
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => '↯⌬ راهنماے تبچی ↯⌬:
━┈┈┈┈┈┈┈®┈┈┈┈┈┈┈━
௸◉ <b> SendAll ʳᵉᵖˡʸ </b> 
⌬〔<i> ارسال کردن پیام به همه </i>〕
௸◉ <b> SendPv ʳᵉᵖˡʸ </b> 
⌬〔<i> ارسال کردن پیام به همه کاربران </i>〕
௸◉ <b> SendGroup ʳᵉᵖˡʸ </b> 
⌬〔<i> ارسال کردن پیام به همه گروه ها و سوپرگروه ها </i>〕
௸◉ <b> SendMember ʳᵉᵖˡʸ </b> 
⌬〔<i> ارسال کردن پیام به همه اعضایی گروه که قبلا استخراج شده </i>〕
௸◉ <b> CleanSendList </b> 
⌬〔<i> پاکسازی لیست افراد که پیام ارسال شده </i>〕
━┈┈┈┈┈┈┈®┈┈┈┈┈┈┈━
௸◉ <b> Forwardall ʳᵉᵖˡʸ </b> 
⌬〔<i> فروارد کردن پیام ریپلاے شده به همه گروه ها و کاربران </i>〕
௸◉ <b> ForwardPv ʳᵉᵖˡʸ </b> 
⌬〔<i>  فروارد کردن پیام ریپلاے شده به همه کاربران </i>〕
௸◉ <b> ForwardGroup ʳᵉᵖˡʸ </b> 
⌬〔<i>  فروارد کردن پیام ریپلاے شده به همه گروه ها و سوپرگروه ها  </i>〕
௸◉ <b> ForwardMember ʳᵉᵖˡʸ </b> 
⌬〔<i>  فوروارد کردن پیام به همه اعضایی گروه استخراج شده  </i>〕
௸◉ <b> CleanForwardList </b> 
⌬〔<i>  پاکسازی لیست افراد که پیام فوروارد شده  </i>〕
௸◉ <b> AutoForward ᴛɪᴍᴇ-ᴍɪɴ </b> 
⌬〔<i> فعالسازے فروارد خودکار زماندار </i>〕
௸◉ <b> DeleteForward </b> 
⌬〔<i> حذف فروارد خودکار زماندار </i>〕
௸◉ <b> ForwardDev ᵒⁿ ᵒᶠᶠ </b> 
⌬〔<i> روشن یا خاموش کردن فوروارد خودکار پیام های پیوی به ادمین </i>〕
━┈┈┈┈┈┈┈®┈┈┈┈┈┈┈━
௸◉ <b> Export ᵍʳᵒᵘᵖɪᴅ </b> 
⌬〔<i> استخراج افرادے گروه </i>〕
௸◉ <b> Add ᵍʳᵒᵘᵖɪᴅ </b> 
⌬〔<i> ادد کردن افرادے استخراج شده به یڪ گروه </i>〕
௸◉ <b> DeleteMember </b> 
⌬〔<i> پاکسازی افرادے استخراج شده </i>〕
௸◉ <b> AddPv ᵘˢᵉʳɪᴅ </b> 
⌬〔<i> ادد کردن همه ے افرادے که در پیوے هستن به یڪ گروه </i>〕
௸◉ <b> AddAll ᵍʳᵒᵘᵖɪᴅ</b> 
⌬〔<i> ادد کردن یڪ کاربر به همه گروه ها </i>〕
━┈┈┈┈┈┈┈®┈┈┈┈┈┈┈━
௸◉ <b> Clean </b> 
⌬〔<i> خروج از گروه هایے که مسدود کردند </i>〕
௸◉ <b> CleanChannel </b> 
⌬〔<i> خروج از همه ے کانال ها </i>〕
௸◉ <b> CleanGroup </b> 
⌬〔<i> خروج ازهمه گروه ها </i>〕
━┈┈┈┈┈┈┈®┈┈┈┈┈┈┈━
௸◉ <b> AutoChatPv ᵒⁿ ᵒᶠᶠ </b> 
⌬〔<i> روشن یا خاموش کردن چت خودکار پیوی </i>〕
௸◉ <b> AutoChatGroup ᵒⁿ ᵒᶠᶠ </b> 
⌬〔<i> روشن یا خاموش کردن چت خودکار گروه </i>〕
௸◉ <b> AutoJoin ᵒⁿ ᵒᶠᶠ </b> 
⌬〔<i> روشن یا خاموش کردن جوین خودکار </i>〕
௸◉ <b> Join @ɪᴅ </b> 
⌬〔<i> عضویت در یڪ کانال یا گروه </i>〕
━┈┈┈┈┈┈┈®┈┈┈┈┈┈┈━
௸◉ <b> ربات ~ ping ~ انلاین </b> 
⌬〔<i> دریافت وضعیت ربات </i>〕
௸◉ <b> ربات ~ شناسه ~ مشخصات </b> 
⌬〔<i> دریافت مشخصات ربات تبچی </i>〕
௸◉ <b> ربات ~ stats </b> 
⌬〔<i> دریافت آمار گروه ها و کاربران </i>〕
௸◉ <b> ورژن ربات </b> 
⌬〔<i> نمایش نسخه سورس تبچے شما </i>〕
௸◉ <b> Name </b> 
⌬〔<i> تنظیم نام ربات </i>〕
௸◉ <b> lastname </b> 
⌬〔<i> تنظیم نام فامیلی ربات </i>〕
௸◉ <b> bio </b> 
⌬〔<i> تنظیم بیو ربات </i>〕
௸◉ <b> restart ~ ریستارت </b> 
⌬〔<i> راه اندازی مجدد ربات </i>〕
௸◉ <b> راهنما </b> 
⌬〔<i> راهنما و لیست دستورات </i>〕
━┈┈┈┈┈┈┈®┈┈┈┈┈┈┈━
௸◉ <b> AddAdmin ᵘˢᵉʳɪᴅ </b> 
⌬〔<i> افزودن ادمین جدید </i>〕
௸◉ <b> CleanList </b> 
⌬〔<i> حذف همه ادمین ها </i>〕
௸◉ <b> AdminList </b> 
⌬〔<i> لیست همه ادمین ها </i>〕
', 'parse_mode' => 'html']);
}
} elseif ($type == "supergroup" && $data['autochatgroup'] == "on") {
if ($userID !== $me_id) {
if ($msg == "سلام") {
yield $this->sleep(4);
$txt = array('سلام', 'سلام', 'سلام خوبی', 'سلام چطوری', );
$text = $txt[rand(0, count($txt) - 1)];
yield $this->sleep(2);
yield $this->messages->setTyping(['peer' => $chatID, 'action' => ['_' => 'sendMessageTypingAction']]);
yield $this->sleep(1);
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => $text, 'reply_to_msg_id' => $msg_id]);
}
}
$mension = yield $this->messages->getUnreadMentions(['peer' => $chatID, 'offset_id' => 0, 'add_offset' => 0, 'limit' => 3, 'max_id' => $msg_id + 1, 'min_id' => $msg_id - 50]);
foreach ($mension['messages'] as $texts) {
$this->messages->readMentions(['peer' => $texts, ]);
yield $this->sleep(9);
$textid = $texts['id'];
$messeg = $texts['message'];
if (strpos($messeg, "خوش") !== false) {
$txt = array('تشکر', 'مرسی', 'ممنون', 'تشکر از لطف شما!');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->messages->sendMessage(['peer' => $texts, 'message' => $text, 'reply_to_msg_id' => $textid]);
} elseif (strpos($messeg, "خوبی") !== false || strpos($messeg, "چطوری") !== false) {
$txt = array('تشکر', 'مرسی', 'تو چطوری', 'مرسی شما خوبین؟', 'ممنون شما خوبین', 'ممنون', 'خوبم', 'تشکر از لطف شما!');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->messages->sendMessage(['peer' => $texts, 'message' => $text, 'reply_to_msg_id' => $textid]);
} elseif (strpos($messeg, "خبر") !== false) {
$txt = array('سلامتی', 'آرامی', 'همه چی خوبه', 'خبر خاصی نیست', 'هیچی از شما چه خبر');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->messages->sendMessage(['peer' => $texts, 'message' => $text, 'reply_to_msg_id' => $textid]);
} elseif (strpos($messeg, "سلام") !== false || strpos($messeg, "علیک") !== false) {
$txt = array('خوبی', 'خبی', 'چطوری', 'چه خبر');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->messages->sendMessage(['peer' => $texts, 'message' => $text, 'reply_to_msg_id' => $textid]);
} elseif (strpos($messeg, "ممنون") !== false || strpos($messeg, "تشکر") !== false) {
$txt = array('قابل نداره', 'خواهش', 'خواهش میکنم', 'قابل نداشت');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->messages->sendMessage(['peer' => $texts, 'message' => $text, 'reply_to_msg_id' => $textid]);
} elseif (strpos($messeg, "اصل") !== false) {
$txt = array('شگوفه 22 ساله', 'شگوفه هستم', 'شگوفه 21 ساله شما؟');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->messages->sendMessage(['peer' => $texts, 'message' => $text, 'reply_to_msg_id' => $textid]);
} elseif (strpos($messeg, "ربات") !== false || strpos($messeg, "رباط") !== false) {
$txt = array('من؟؟', 'نه متاسفانه', 'با اجازه نیستم', 'نه', 'شاید باشم😊', 'ربات مگه حرف هم میزنه', 'رباتتتتت؟؟؟🤣');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->messages->sendMessage(['peer' => $texts, 'message' => $text, 'reply_to_msg_id' => $textid]);
}
}
} elseif ($type == "user" && $userID !== $me_id) {
if (@$data['autosave'] == 'on') {
yield $this->messages->forwardMessages(['from_peer' => $userID, 'to_peer' => $admin, 'id' => [$msg_id]]);
}
if (@$data['autochatpv'] == 'on' && rand(0, 2) == 1) {
$this->sleep(3);
if (strpos($msg, "سلام") !== false) {
$txt = array('سلام', 'سلام', 'سلام خوبی', 'سلام چطوری', );
$text = $txt[rand(0, count($txt) - 1)];
yield $this->sleep(2);
yield $this->messages->readHistory(['peer' => $userID, 'max_id' => $msg_id]);
yield $this->sleep(2);
yield $this->messages->setTyping(['peer' => $chatID, 'action' => ['_' => 'sendMessageTypingAction']]);
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => $text, 'reply_to_msg_id' => $msg_id]);
} elseif (strpos($msg, "خبر") !== false) {
yield $this->sleep(2);
yield $this->messages->readHistory(['peer' => $userID, 'max_id' => $msg_id]);
yield $this->sleep(2);
yield $this->messages->setTyping(['peer' => $chatID, 'action' => ['_' => 'sendMessageTypingAction']]);
$txt = array('سلامتی', 'آرامی', 'همه چی خوبه', 'خبر خاصی نیست', 'هیچی از شما چه خبر');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => $text, 'reply_to_msg_id' => $msg_id]);
} elseif (strpos($msg, "ربات") !== false || strpos($msg, "رباط") !== false) {
yield $this->sleep(2);
yield $this->messages->readHistory(['peer' => $userID, 'max_id' => $msg_id]);
yield $this->sleep(2);
yield $this->messages->setTyping(['peer' => $chatID, 'action' => ['_' => 'sendMessageTypingAction']]);
$txt = array('من؟؟', 'نه متاسفانه', 'با اجازه نیستم', 'نه', 'نمیفامم شاید باشم😊', 'ربات مگه حرف هم میزنه', 'رباتتتتت؟؟؟🤣🤣');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => $text, 'reply_to_msg_id' => $msg_id]);
} elseif (strpos($msg, "خوبین") !== false || strpos($msg, "خوب") !== false || strpos($msg, "خبی") !== false) {
$txt = array('تشکر', 'مرسی', 'ممنون', 'خوبم', 'تشکر از لطف شما!');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->sleep(2);
yield $this->messages->readHistory(['peer' => $userID, 'max_id' => $msg_id]);
yield $this->sleep(2);
yield $this->messages->setTyping(['peer' => $chatID, 'action' => ['_' => 'sendMessageTypingAction']]);
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => $text, 'reply_to_msg_id' => $msg_id]);
} elseif (strpos($msg, "اصل") !== false || strpos($msg, "اشنا") !== false || strpos($msg, "معریفی") !== false || strpos($msg, "آشنا") !== false) {
$txt = array('شگوفه 23 ساله', 'شگوفه 23 ساله هستم از ', 'شگوفه هستم 23 ساله', 'شگوفه 23 ساله شما؟');
$text = $txt[rand(0, count($txt) - 1)];
yield $this->sleep(2);
yield $this->messages->readHistory(['peer' => $userID, 'max_id' => $msg_id]);
yield $this->sleep(2);
yield $this->messages->setTyping(['peer' => $chatID, 'action' => ['_' => 'sendMessageTypingAction']]);
yield $this->messages->sendMessage(['peer' => $chatID, 'message' => $text, 'reply_to_msg_id' => $msg_id]);
}
}
}
if (file_exists('ForTime/time.txt')) {
if ((time() - filectime('ForTime/time.txt')) >= file_get_contents('ForTime/time.txt')) {
$tt = file_get_contents('ForTime/time.txt');
unlink('ForTime/time.txt');
file_put_contents('ForTime/time.txt', $tt);
$dialogs = yield $this->get_dialogs();
foreach ($dialogs as $peer) {
$type = yield $this->get_info($peer);
if ($type['type'] == 'supergroup' || $type['type'] == 'chat') {
$this->messages->forwardMessages(['from_peer' => file_get_contents('ForTime/chatid.txt'), 'to_peer' => $peer, 'id' => [file_get_contents('ForTime/msgid.txt')]]);
}
}
}
}
} catch (RPCErrorException $e) {
}
}
}
$MadelineProto->async(true);
$MadelineProto->loop(function () use ($MadelineProto) {
yield $MadelineProto->setEventHandler('\EventHandler');
});
$MadelineProto->loop();

/*
 
@GPYAB
https://t.me/GPYAB
*/
