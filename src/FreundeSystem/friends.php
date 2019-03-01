<?php

namespace FreundeSystem;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerChatEvent;

class friends extends PluginBase implements Listener{


public $prefix = "§eFreunde §7» ";

public function onEnable(){
@mkdir($this->getDataFolder());
$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->getLogger()->info($this->prefix."§aWurde aktiviert!");
} 

public function onJoin(PlayerJoinEvent $event){
$player = $event->getPlayer();
$name = $player->getName();
if(!file_exists($this->getDataFolder().$name.".yml")){
$playerfile = new Config($this->getDataFolder().$name.".yml", Config::YAML);
$playerfile->set("Friend", array());
$playerfile->set("Invitations", array());
$playerfile->set("blocked", false);
$playerfile->save();
}else{
$playerfile = new Config($this->getDataFolder().$name.".yml", Config::YAML);
if(!empty($playerfile->get("Invitations"))){
foreach($playerfile->get("Invitations") as $e){
$player->sendMessage($this->prefix."§a".$e."ist jetzt Dein Freund!");
}
}

if(!empty($playerfile->get("Friend"))){
foreach($playerfile->get("Friend") as $f){
$v = $this->getServer()->getPlayerExact($f);
if(!$v == null){
$v->sendMessage($this->prefix."§a".$player->getName()." Ist Jetzt Online");
}
}
}
}
}
public function onQuit(PlayerQuitEvent $event){
$player = $event->getPlayer();
$name = $player->getName();
$playerfile = new Config($this->getDataFolder().$name.".yml", Config::YAML);
if(!empty($playerfile->get("Friend"))){
foreach($playerfile->get("Friend") as $f){
$v = $this->getServer()->getPlayerExact($f);
if(!$v == null){
$v->sendMessage($this->prefix."§a".$player->getName()." Ist Jetzt offline");
}
}
}
}
public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
if($cmd->getName() == "freunde"){
if($sender instanceof Player){
$playerfile = new Config($this->getDataFolder().$sender->getName().".yml", Config::YAML);
if(empty($args[0])){
$sender->sendMessage("§2» FreundeSystem Hilfe «");
$sender->sendMessage("§2/freunde » §2aktzeptieren » §f Aktzeptiere eine Anfrage");
$sender->sendMessage("§2/freunde » §2einladen » §fLade ein Freund ein");
$sender->sendMessage("§2/freunde » §2liste » §fZeigt Deine Freunde an");
$sender->sendMessage("§2/freunde » §2ablehnen » §f Lehne eine Anfrage ab");
$sender->sendMessage("§2/freunde » §2entfernen » §fEntferne einen Freund");
$sender->sendMessage("§2/freunde » §2block » §fDeaktiviere Freundschaftsanfragen");
}else{
if($args[0] == "einladen"){
if(empty($args[1])){
$sender->sendMessage($this->prefix."§eBenutze: §2/freunde einladen [Spieler]");
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if($vplayerfile->get("blocked") == false){
$einladungen = $vplayerfile->get("Invitations");
$einladungen[] = $sender->getName();
$vplayerfile->set("Invitations", $einladungen);
$vplayerfile->save();
$sender->sendMessage($this->prefix."§aDeine Freundschafftsanfrage wurde gesendet zu  ".$args[1]);
$v = $this->getServer()->getPlayerExact($args[1]);
if(!$v == null){
$v->sendMessage("§a".$sender->getName()." hat Dir eine Freundschaffts Anfrage gesendet akzeptier sie mit §2/freunde aktzeptieren ".$sender->getName()."] oder lehne sie ab mit §2 /freunde ablehnen ".$sender->getName()."§a!");
}
}else{
$sender->sendMessage($this->prefix."§aDieser Spieler hat Deine Freundschafftsanfrage nicht angenommen!");
}
}else{
$sender->sendMessage($this->prefix."§aDieser Spieler ist nicht Online!");
}
}
}
if($args[0] == "aktzeptieren"){
if(empty($args[1])){
$sender->sendMessage($this->prefix."§eBenutze: §2/freunde aktzeptieren [Spieler]");
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if(in_array($args[1], $playerfile->get("Invitations"))){
$old = $playerfile->get("Invitations");
unset($old[array_search($args[1], $old)]);
$playerfile->set("Invitations", $old);
$newfriend = $playerfile->get("Friend");
$newfriend[] = $args[1];
$playerfile->set("Friend", $newfriend);
$playerfile->save();
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
$newfriend = $vplayerfile->get("Friend");
$newfriend[] = $sender->getName();
$vplayerfile->set("Friend", $newfriend);
$vplayerfile->save();
if(!$this->getServer()->getPlayerExact($args[1]) == null){
$this->getServer()->getPlayerExact($args[1])->sendMessage($this->prefix."§a".$sender->getName()." hat Deine Freundschaffts Anfrage angenommen!");
}
$sender->sendMessage($this->prefix."§a".$args[1]." ist jetzt Dein Freund!");
}else{
$sender->sendMessage($this->prefix."§aDieser Spieler hat Dir keine Freundschaffts Anfrage gesendet!");
}
}else{
$sender->sendMessage($this->prefix."§aDiesen Spieler gibt es nicht!");
}
}
}

if($args[0] == "ablehnen"){
if(empty($args[1])){
$sender->sendMessage($this->prefix."§eBenutze: §2/freunde ablehnen [Spieler]");
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if(in_array($args[1], $playerfile->get("Invitations"))){
$old = $playerfile->get("Invitations");
unset($old[array_search($args[1], $old)]);
$playerfile->set("Invitations", $old);
$playerfile->save();
$sender->sendMessage($this->prefix."§aDie Anfrage von ".$args[1]." wurde abgelehnt!");
}else{
$sender->sendMessage($this->prefix."§aDieser Spieler hat Dir keine Freundschaffts Anfrage gesendet!");
}
}else{
$sender->sendMessage($this->prefix."§aDiesen Spieler gibt es nicht!");
}
}
}

if($args[0] == "entfernen"){
if(empty($args[1])){
$sender->sendMessage($this->prefix."§eBenutze: §2/freunde entfernen [Spieler]");
}else{
if(file_exists($this->getDataFolder().$args[1].".yml")){
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
if(in_array($args[1], $playerfile->get("Friend"))){
$old = $playerfile->get("Friend");
unset($old[array_search($args[1], $old)]);
$playerfile->set("Friend", $old);
$playerfile->save();
$vplayerfile = new Config($this->getDataFolder().$args[1].".yml", Config::YAML);
$old = $vplayerfile->get("Friend");
unset($old[array_search($sender->getName(), $old)]);
$vplayerfile->set("Friend", $old);
$vplayerfile->save();
$sender->sendMessage($this->prefix."§a".$args[1]." ist nicht mehr Dein Freund!");
}else{
$sender->sendMessage($this->prefix."§aDieser Spieler ist nicht Dein Freund!");
}
}else{
$sender->sendMessage($this->prefix."§aDiesen Spieler gibt es nicht!");
}
}
}

if($args[0] == "liste"){
if(empty($playerfile->get("Friend"))){
$sender->sendMessage($this->prefix."§aDu hast keine Freunde!");
}else{
$sender->sendMessage("§7----------- §2Deine Freunde§7 -----------");
foreach($playerfile->get("Friend") as $f){
if($this->getServer()->getPlayerExact($f) == null){
$sender->sendMessage("§e".$f." » §7(§cOffline§7)");
}else{
$sender->sendMessage("§e".$f." » §7(§aOnline§7)");
}
}
}
}
if($args[0] == "block"){
if($playerfile->get("blocked") === false){
$playerfile->set("blocked", true);
$playerfile->save();
$sender->sendMessage($this->prefix."§aDu wirst nun keine Freundschafftsanfrage mehr bekommen!");
}else{
$sender->sendMessage($this->prefix."§aDu wirst nun wieder Freundschafftsanfragen bekommen!");
$playerfile->set("blocked", false);
$playerfile->save();
}
}



}
}else{
$this->getLogger()->info($this->prefix."§aDie Console hat keine Freunde!");
}
}
return true;
}
public function onChat(PlayerChatEvent $event){
$player = $event->getPlayer();
$msg = $event->getMessage();
$playerfile = new Config($this->getDataFolder().$player->getName().".yml", Config::YAML);
$words = explode(" ", $msg);
if(in_array(str_replace("@", "", $words[0]), $playerfile->get("Friend"))){
$f = $this->getServer()->getPlayerExact(str_replace("@", "", $words[0]));
if(!$f == null){
$f->sendMessage($this->prefix." §7[§e".str_replace("@", "", $words[0])."§7] §l>>§r ".str_replace($words[0], "", $msg));
$player->sendMessage($this->prefix." §7[§e".str_replace("@", "", $words[0])."§7] §l>>§r ".str_replace($words[0], "", $msg));
}else{
$player->sendMessage($this->prefix."§c".str_replace("@", "", $words[0])." ist nicht online!");
}
$event->setCancelled();
}
}
}


?>
