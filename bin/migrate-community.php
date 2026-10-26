<?php
// Creates and verifies a logical dump before applying the one-time additive migration.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if ($argc !== 2 || !str_starts_with($argv[1], '/')) {
    fwrite(STDERR, "Usage: php bin/migrate-community.php /absolute/path/to/fluentpath-backup.sql\n"); exit(2);
}
require_once dirname(__DIR__) . '/app/Database.php';
$backup=$argv[1];$config=require dirname(__DIR__).'/config/database.php';
if(file_exists($backup)){fwrite(STDERR,"Backup destination already exists; choose a new path.\n");exit(2);}
$dir=dirname($backup);if(!is_dir($dir)||!is_writable($dir)){fwrite(STDERR,"Backup directory must exist and be writable.\n");exit(2);}
$root=realpath(dirname(__DIR__));$realDir=realpath($dir);
if($realDir===$root||str_starts_with($realDir,$root.DIRECTORY_SEPARATOR)){fwrite(STDERR,"Store the backup outside the project directory.\n");exit(2);}
$defaults=tempnam(sys_get_temp_dir(),'fp-db-');
$quote=static fn($v)=>'"'.str_replace(['\\','"'],['\\\\','\\"'],(string)$v).'"';
file_put_contents($defaults,"[client]\n".'host='.$config['host']."\nport=".$config['port']."\nuser=".$config['user']."\npassword=".$quote($config['password'])."\n");chmod($defaults,0600);
$cmd=['mysqldump','--defaults-extra-file='.$defaults,'--single-transaction','--routines','--triggers',$config['dbname']];
$out=fopen($backup,'xb');if(!$out){unlink($defaults);fwrite(STDERR,"Cannot create backup file.\n");exit(1);}chmod($backup,0600);
$pipes=[];$proc=proc_open($cmd,[0=>['pipe','r'],1=>$out,2=>['pipe','w']],$pipes);
fclose($pipes[0]);$error=stream_get_contents($pipes[2]);fclose($pipes[2]);fclose($out);$status=proc_close($proc);unlink($defaults);
if($status!==0||!is_file($backup)||filesize($backup)<100){@unlink($backup);fwrite(STDERR,"Database backup failed: ".substr((string)$error,0,300)."\n");exit(1);}
// Confirm the dump contains the core tables rather than only a header or warning.
$verify=fopen($backup,'rb');$found=['users'=>false,'lessons'=>false];
while(($line=fgets($verify))!==false){foreach(array_keys($found) as $table){if(str_contains($line,'CREATE TABLE')&&str_contains($line,'`'.$table.'`'))$found[$table]=true;}}
fclose($verify);
if(!$found['users']||!$found['lessons']){@unlink($backup);fwrite(STDERR,"Backup did not contain both users and lessons tables. Migration stopped.\n");exit(1);}
echo "Verified backup created: {$backup} (".filesize($backup)." bytes)\n";
$db=Database::getConnection();
$tables=$db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
if(!in_array('users',$tables,true)||!in_array('lessons',$tables,true)){fwrite(STDERR,"Expected existing users and lessons tables; migration stopped. Backup retained.\n");exit(1);}
$cols=$db->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_COLUMN);
foreach(['role','account_status','post_status','post_suspended_until','status_reason'] as $col) if(in_array($col,$cols,true)){fwrite(STDERR,"Column {$col} already exists; migration stopped to avoid partial reapplication. Backup retained.\n");exit(1);}
foreach(['forum_posts','forum_reports','moderation_history','user_appeals'] as $table) if(in_array($table,$tables,true)){fwrite(STDERR,"Table {$table} already exists; migration stopped. Backup retained.\n");exit(1);}
$sql=file_get_contents(dirname(__DIR__).'/database/migrations/002_admin_community.sql');
if($sql===false){fwrite(STDERR,"Migration file is unavailable; backup retained.\n");exit(1);}
try{$db->exec($sql);echo "Community and admin migration applied successfully.\n";}catch(Throwable $e){fwrite(STDERR,"Migration failed after backup; retain backup and inspect database state: ".$e->getMessage()."\n");exit(1);}
