<?php
require_once __DIR__ . '/Database.php';

final class Community {
    public static function textLength(string $value): int {
        if(function_exists('mb_strlen'))return mb_strlen($value,'UTF-8');
        $count=preg_match_all('/./us',$value);return $count===false?strlen($value):$count;
    }
    public static function pageData(string $path, int $page = 1): array {
        $db = Database::getConnection();
        $limit = 20; $offset = max(0, $page - 1) * $limit;
        if ($path === '/forum') {
            $q = $db->prepare("SELECT t.id,t.title,t.created_at,t.category,u.username,COUNT(p.id) AS replies FROM forum_posts t JOIN users u ON u.id=t.user_id LEFT JOIN forum_posts p ON p.thread_id=t.id AND p.hidden_at IS NULL WHERE t.thread_id IS NULL AND t.hidden_at IS NULL GROUP BY t.id ORDER BY t.created_at DESC LIMIT :lim OFFSET :off");
            $q->bindValue('lim', $limit, PDO::PARAM_INT); $q->bindValue('off', $offset, PDO::PARAM_INT); $q->execute();
            return ['threads' => $q->fetchAll(), 'page' => $page];
        }
        return [];
    }

    public static function thread(int $id, int $page = 1): ?array {
        $db=Database::getConnection(); $q=$db->prepare("SELECT p.*,u.username FROM forum_posts p JOIN users u ON u.id=p.user_id WHERE p.id=:id AND p.thread_id IS NULL AND p.hidden_at IS NULL"); $q->execute(['id'=>$id]); $thread=$q->fetch();
        if (!$thread) return null;
        $r=$db->prepare("SELECT p.id,p.user_id,p.body,p.created_at,p.updated_at,u.username FROM forum_posts p JOIN users u ON u.id=p.user_id WHERE p.thread_id=:id AND p.hidden_at IS NULL ORDER BY p.created_at LIMIT 50 OFFSET :off"); $r->bindValue('id',$id,PDO::PARAM_INT); $r->bindValue('off',max(0,$page-1)*50,PDO::PARAM_INT); $r->execute(); $thread['replies']=$r->fetchAll(); return $thread;
    }

    public static function post(int $uid, ?int $thread, string $title, string $body, string $category): void {
        $db=Database::getConnection();
        $check=$db->prepare("SELECT post_status,post_suspended_until FROM users WHERE id=:id"); $check->execute(['id'=>$uid]); $u=$check->fetch();
        if (!$u) throw new RuntimeException('Akun tidak ditemukan.');
        if ($u['post_status']==='suspended') {
            if ($u['post_suspended_until'] && strtotime($u['post_suspended_until']) <= time()) {
                $db->prepare("UPDATE users SET post_status='active',post_suspended_until=NULL WHERE id=:id")->execute(['id'=>$uid]);
            } else {
                throw new RuntimeException('Akun Anda sedang tidak dapat memposting. Buka profil untuk melihat alasan atau mengajukan banding.');
            }
        }
        if (!in_array($category,['Grammar','Vocabulary','Reading','Listening','Writing','Speaking','General'],true)) $category='General';
        if (self::textLength(trim($body))<3 || self::textLength($body)>10000 || self::textLength($title)>180) throw new RuntimeException('Isi harus 3–10.000 karakter dan judul maksimal 180 karakter.');
        $recent=$db->prepare("SELECT COUNT(*) FROM forum_posts WHERE user_id=:u AND created_at>DATE_SUB(NOW(),INTERVAL 30 SECOND)"); $recent->execute(['u'=>$uid]); if ((int)$recent->fetchColumn()) throw new RuntimeException('Tunggu sebentar sebelum mengirim posting berikutnya.');
        if ($thread) { $q=$db->prepare("INSERT INTO forum_posts(user_id,thread_id,body,created_at) SELECT :u,id,:b,NOW() FROM forum_posts WHERE id=:t AND thread_id IS NULL AND hidden_at IS NULL"); $q->execute(['u'=>$uid,'b'=>trim($body),'t'=>$thread]); }
        else { $q=$db->prepare("INSERT INTO forum_posts(user_id,title,body,category,created_at) VALUES(:u,:t,:b,:c,NOW())"); $q->execute(['u'=>$uid,'t'=>trim($title),'b'=>trim($body),'c'=>$category]); }
        if ($q->rowCount()!==1) throw new RuntimeException('Diskusi tujuan tidak ditemukan.');
    }

    public static function moderate(int $admin, int $target, string $action, string $reason, ?int $report=null, ?int $duration=null, ?int $content=null): void {
        $reason=trim($reason);
        if (!in_array($action,['warning','post_suspend','account_suspend','ban','reinstate','dismiss','hide','remove'],true) || $reason==='' || self::textLength($reason)>3000) throw new RuntimeException('Jenis tindakan dan alasan 1–3.000 karakter wajib diisi.');
        if($action==='post_suspend')$duration=max(1,min(365,$duration??7));
        $db=Database::getConnection();
        if ($target===$admin && in_array($action,['account_suspend','ban','post_suspend'],true)) throw new RuntimeException('Anda tidak dapat membatasi akun sendiri.');
        if (in_array($action,['account_suspend','ban'],true)) { $q=$db->prepare("SELECT COUNT(*) FROM users WHERE role='admin' AND account_status='active'"); $q->execute(); $active=(int)$q->fetchColumn(); $q=$db->prepare("SELECT role,account_status FROM users WHERE id=:u");$q->execute(['u'=>$target]);$u=$q->fetch(); if ($u && $u['role']==='admin' && $u['account_status']==='active' && $active<=1) throw new RuntimeException('Administrator aktif terakhir tidak dapat dinonaktifkan.'); }
        $db->beginTransaction();
        try {
            if ($action==='warning') { $db->prepare("UPDATE users SET status_reason=:r WHERE id=:u")->execute(['r'=>'Peringatan: '.$reason,'u'=>$target]); }
            elseif ($action==='post_suspend') { $days=$duration; $db->prepare("UPDATE users SET post_status='suspended',post_suspended_until=DATE_ADD(NOW(),INTERVAL {$days} DAY),status_reason=:r WHERE id=:u")->execute(['r'=>$reason,'u'=>$target]); }
            elseif ($action==='account_suspend') $db->prepare("UPDATE users SET account_status='suspended',status_reason=:r WHERE id=:u")->execute(['r'=>$reason,'u'=>$target]);
            elseif ($action==='ban') $db->prepare("UPDATE users SET account_status='banned',status_reason=:r WHERE id=:u")->execute(['r'=>$reason,'u'=>$target]);
            elseif ($action==='reinstate') $db->prepare("UPDATE users SET account_status='active',post_status='active',post_suspended_until=NULL,status_reason=NULL WHERE id=:u")->execute(['u'=>$target]);
            elseif ($action==='hide') $db->prepare("UPDATE forum_posts SET hidden_at=NOW(),hidden_reason=:r WHERE id=:root OR thread_id=:thread")->execute(['r'=>$reason,'root'=>$content??0,'thread'=>$content??0]);
            elseif ($action==='remove') { if ($content) { $db->prepare("DELETE FROM forum_posts WHERE id=:root OR thread_id=:thread")->execute(['root'=>$content,'thread'=>$content]); } }
            if ($report) $db->prepare("UPDATE forum_reports SET status=:s,resolution_reason=:r,resolved_by=:a,resolved_at=NOW() WHERE id=:id")->execute(['s'=>$action==='dismiss'?'dismissed':'resolved','r'=>$reason,'a'=>$admin,'id'=>$report]);
            $db->prepare("INSERT INTO moderation_history(admin_id,target_user_id,action_type,reason,duration_days,report_id,content_id,created_at) VALUES(:a,:u,:t,:r,:d,:rp,:c,NOW())")->execute(['a'=>$admin,'u'=>$target,'t'=>$action,'r'=>$reason,'d'=>$duration,'rp'=>$report,'c'=>$content]);
            $db->commit();
        } catch(Throwable $e) { $db->rollBack(); throw $e; }
    }

    public static function updateOwnPost(int $uid, int $post, string $body, bool $admin=false): bool {
        $body=trim($body); if(self::textLength($body)<3||self::textLength($body)>10000) throw new RuntimeException('Isi harus 3–10.000 karakter.');
        $db=Database::getConnection();$sql=$admin?'UPDATE forum_posts SET body=:b,updated_at=NOW() WHERE id=:p AND hidden_at IS NULL':'UPDATE forum_posts SET body=:b,updated_at=NOW() WHERE id=:p AND user_id=:u AND hidden_at IS NULL';$q=$db->prepare($sql);$p=['b'=>$body,'p'=>$post];if(!$admin)$p['u']=$uid;$q->execute($p);return $q->rowCount()>0;
    }

    public static function deleteOwnPost(int $uid, int $post, bool $admin=false): bool {
        $db=Database::getConnection();$sql=$admin?'DELETE FROM forum_posts WHERE id=:p':'DELETE FROM forum_posts WHERE id=:p AND user_id=:u';$q=$db->prepare($sql);$p=['p'=>$post];if(!$admin)$p['u']=$uid;$q->execute($p);return $q->rowCount()>0;
    }
}
