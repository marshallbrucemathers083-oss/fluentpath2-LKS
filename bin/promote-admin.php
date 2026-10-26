<?php
// Run only from CLI after database/migrations/002_admin_community.sql.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (($argc !== 2 && $argc !== 3) || ($argc===3 && $argv[1]!=='--revoke') || !preg_match('/^[A-Za-z0-9_]{3,30}$/', $argv[$argc-1])) {
    fwrite(STDERR, "Usage: php bin/promote-admin.php <registered-username>\n       php bin/promote-admin.php --revoke <registered-username>\n"); exit(2);
}
require_once dirname(__DIR__) . '/app/Database.php';
$db=Database::getConnection();
$name=$argv[$argc-1];$q=$db->prepare('SELECT id,username,role,account_status FROM users WHERE username=:name LIMIT 1'); $q->execute(['name'=>$name]); $user=$q->fetch();
if (!$user) { fwrite(STDERR,"No registered account named {$name} exists.\n"); exit(1); }
if ($argc===2) {
    if($user['account_status']!=='active'){fwrite(STDERR,"Cannot promote an inactive account. Restore it first.\n");exit(1);}
    $db->prepare("UPDATE users SET role='admin' WHERE id=:id")->execute(['id'=>$user['id']]);
    fwrite(STDOUT,"Administrator access granted to {$user['username']} (user id {$user['id']}).\n");
} else {
    if($user['role']==='admin'&&$user['account_status']==='active'&&(int)$db->query("SELECT COUNT(*) FROM users WHERE role='admin' AND account_status='active'")->fetchColumn()<=1){fwrite(STDERR,"Cannot revoke the last active administrator.\n");exit(1);}
    $db->prepare("UPDATE users SET role='user' WHERE id=:id")->execute(['id'=>$user['id']]);
    fwrite(STDOUT,"Administrator access revoked for {$user['username']}.\n");
}
