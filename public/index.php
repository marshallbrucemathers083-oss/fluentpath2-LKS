<?php
// public/index.php - Front Controller and Application Router

require_once dirname(__DIR__) . '/app/Helpers.php';
require_once dirname(__DIR__) . '/app/Auth.php';
require_once dirname(__DIR__) . '/app/Models/User.php';
require_once dirname(__DIR__) . '/app/Models/Lesson.php';
require_once dirname(__DIR__) . '/app/Models/Challenge.php';
require_once dirname(__DIR__) . '/app/Models/Academy.php';
require_once dirname(__DIR__) . '/app/Models/Exam.php';
require_once dirname(__DIR__) . '/app/Models/Leaderboard.php';
require_once dirname(__DIR__) . '/app/Models/Achievement.php';
require_once dirname(__DIR__) . '/app/Community.php';

// Keep query/schema failures visible to the user instead of returning an empty
// response. Details stay in the server log; the page never exposes SQL or config.
set_exception_handler(static function (Throwable $error): void {
    $isDatabaseError = $error instanceof PDOException;
    $logFile = dirname(__DIR__) . '/storage/logs/application.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    // Avoid writing driver messages, which can contain connection details. SQLSTATE
    // and the request path are enough to correlate failures with database logs.
    $sqlState = $isDatabaseError ? (string)$error->getCode() : '';
    $driverCode = $isDatabaseError ? (string)($error->errorInfo[1] ?? '') : '';
    $diagnostic = $error->getMessage();
    $diagnostic = preg_replace('/(password|passwd|pwd)\s*[=:]\s*[^ ;,]+/i', '$1=[redacted]', $diagnostic);
    $diagnostic = preg_replace("/'[^']*'@'[^']*'/", "'[redacted]'@'[redacted]'", $diagnostic);
    $diagnostic = preg_replace('/(mysql:host=)[^; ]+/i', '$1[redacted]', $diagnostic);
    $diagnostic = substr((string)$diagnostic, 0, 300);
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $record = sprintf("[%s] route=%s exception=%s sqlstate=%s at=%s:%d\n", date('c'), $path,
        get_class($error), $sqlState . ($driverCode !== '' ? '/' . $driverCode : ''), basename($error->getFile()), $error->getLine()) .
        sprintf("  diagnostic=%s\n", str_replace(["\r", "\n"], ' ', $diagnostic));
    @file_put_contents($logFile, $record, FILE_APPEND | LOCK_EX);
    http_response_code($isDatabaseError ? 503 : 500);
    require dirname(__DIR__) . '/views/pages/runtime_error.php';
    exit;
});

// Parse current URL path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');
if ($requestUri === '') {
    $requestUri = '/';
}

$method = $_SERVER['REQUEST_METHOD'];

// Route dispatching
switch (true) {
    // 1. Landing Page
    case $requestUri === '/':
        if (is_authenticated()) {
            redirect('/dashboard');
        }
        $weeklyBoard = Leaderboard::getWeekly(5);
        $weeklyRankings = $weeklyBoard['rankings'];
        view('pages.home', compact('weeklyRankings'));
        break;

    // 2. Authentication: Register
    case $requestUri === '/register':
        Auth::requireGuest();
        if ($method === 'POST') {
            if (!validate_csrf()) {
                flash('error', 'Token keamanan CSRF tidak valid.');
                redirect('/register');
            }
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            $result = Auth::register($username, $password, $passwordConfirm);
            if ($result['success']) {
                // Auto login user
                Auth::login($username, $password);
                flash('success', 'Selamat datang di FluentPath! Akun Anda berhasil dibuat.');
                redirect('/dashboard');
            } else {
                $errors = $result['errors'];
                $oldUsername = $username;
                view('pages.register', compact('errors', 'oldUsername'));
            }
        } else {
            view('pages.register');
        }
        break;

    // 3. Authentication: Login
    case $requestUri === '/login':
        Auth::requireGuest();
        if ($method === 'POST') {
            if (!validate_csrf()) {
                flash('error', 'Token keamanan CSRF tidak valid.');
                redirect('/login');
            }
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $result = Auth::login($username, $password);
            if ($result['success']) {
                if (($result['account_status'] ?? 'active') !== 'active') {
                    flash('error', 'Akun Anda sedang dibatasi. Status dan banding dapat dibuka di halaman ini.');
                    redirect('/appeal');
                }
                flash('success', 'Berhasil masuk ke FluentPath.');
                redirect('/dashboard');
            } else {
                $error = $result['error'];
                $oldUsername = $username;
                view('pages.login', compact('error', 'oldUsername'));
            }
        } else {
            view('pages.login');
        }
        break;

    // 4. Authentication: Logout
    case $requestUri === '/logout':
        if ($method !== 'POST' || !validate_csrf()) {
            http_response_code(405);
            echo 'Permintaan tidak valid.';
            break;
        }
        Auth::logout();
        flash('info', 'Anda telah berhasil keluar dari akun.');
        redirect('/login');
        break;

    // 5. Dashboard
    case $requestUri === '/dashboard':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $stats = User::getStats($userId);
        $recommendedLesson = Lesson::getRecommendedNext($userId);
        $weeklyData = Leaderboard::getWeekly(5, $userId);
        view('pages.dashboard', compact('user', 'stats', 'recommendedLesson', 'weeklyData'));
        break;

    // 6. Learning Path Catalog
    case $requestUri === '/lessons':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $groupedLessons = Lesson::getAllGroupedByLevel($userId);
        view('pages.lessons.index', compact('groupedLessons', 'user'));
        break;

    // 7. Individual Lesson View (/lessons/{id})
    case preg_match('#^/lessons/([0-9]+)$#', $requestUri, $matches):
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $lessonId = (int)$matches[1];
        $lesson = Lesson::getById($lessonId, $userId);
        if (!$lesson) {
            http_response_code(404);
            echo "Pelajaran tidak ditemukan.";
            exit;
        }
        view('pages.lessons.view', compact('lesson', 'user'));
        break;

    // 8. Academy Hub
    case $requestUri === '/academy':
        Auth::requireAuth();
        $user = current_user();
        view('pages.academy.index', compact('user'));
        break;

    // 9. Academy Practice Session
    case preg_match('#^/practice/(Grammar|Vocabulary|Reading|Listening|Writing|Speaking)$#', $requestUri, $matches):
        Auth::requireAuth();
        $user = current_user();
        $skill = $matches[1];
        if (in_array($skill, ['Writing', 'Speaking'], true)) {
            view('pages.skill_guide', compact('user', 'skill'));
            break;
        }
        $userId = (int)$user['id'];
        $filters = ['skill' => $skill, 'level' => $_GET['level'] ?? null, 'difficulty' => null, 'mode' => null, 'search' => $_GET['q'] ?? null];
        $questions = Academy::getQuestions($filters, 10, $userId);
        $_SESSION['academy_question_ids'] = array_map('intval', array_column($questions, 'id'));
        view('pages.academy.practice', compact('questions', 'filters', 'user'));
        break;

    // 9. Academy Practice Session
    case $requestUri === '/academy/practice':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $filters = [
            'skill'      => $_GET['skill'] ?? null,
            'level'      => $_GET['level'] ?? null,
            'difficulty' => $_GET['difficulty'] ?? null,
            'mode'       => $_GET['mode'] ?? null,
            'search'     => $_GET['q'] ?? null,
        ];
        $questions = Academy::getQuestions($filters, 10, $userId);
        $_SESSION['academy_question_ids'] = array_map('intval', array_column($questions, 'id'));
        view('pages.academy.practice', compact('questions', 'filters', 'user'));
        break;

    // 10. Exam Lab Hub
    case $requestUri === '/exam':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $history = Exam::getUserExamHistory($userId);
        view('pages.exam.index', compact('history', 'user'));
        break;

    // 11. Timed Exam Session
    case $requestUri === '/exam/take':
        Auth::requireAuth();
        $user = current_user();
        $questions = Exam::getExamQuestions();
        if (!$questions) { flash('error', 'Belum ada soal tersedia saat ini.'); redirect('/exam'); }
        $_SESSION['exam_question_ids'] = array_map('intval', array_column($questions, 'id'));
        view('pages.exam.take', compact('questions', 'user'));
        break;

    // 12. Exam Result
    case $requestUri === '/exam/result':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $attemptId = (int)($_GET['id'] ?? 0);
        $attempt = Exam::getAttemptById($attemptId, $userId);
        if (!$attempt) {
            flash('error', 'Catatan ujian tidak ditemukan.');
            redirect('/exam');
        }
        view('pages.exam.result', compact('attempt', 'user'));
        break;

    // 13. Leaderboard
    case $requestUri === '/leaderboard':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $weeklyData = Leaderboard::getWeekly(20, $userId);
        $allTimeData = Leaderboard::getAllTime(20, $userId);
        view('pages.leaderboard', compact('weeklyData', 'allTimeData', 'user'));
        break;

    // 14. User Profile
    case $requestUri === '/profile':
        Auth::requireAuth();
        $user = current_user();
        $userId = (int)$user['id'];
        $stats = User::getStats($userId);
        $achievements = Achievement::getUserAchievements($userId);
        view('pages.profile', compact('user', 'stats', 'achievements'));
        break;

    case $requestUri === '/forum':
        Auth::requireAuth(); $user=current_user();
        $data=Community::pageData('/forum', max(1,(int)($_GET['page']??1)));
        view('pages.forum', ['title'=>'Forum komunitas','heading'=>'Forum komunitas','body'=>'Ruang diskusi berdasarkan keterampilan belajar. Tulis pertanyaan yang jelas dan hormati peserta lain.','threads'=>$data['threads'],'user'=>$user,'page'=>$data['page']]);
        break;

    case preg_match('#^/forum/([0-9]+)$#',$requestUri,$matches):
        Auth::requireAuth(); $replyPage=max(1,(int)($_GET['page']??1));$thread=Community::thread((int)$matches[1],$replyPage);
        if (!$thread) { http_response_code(404); echo 'Diskusi tidak ditemukan.'; break; }
        view('pages.forum', ['title'=>$thread['title'],'heading'=>$thread['title'],'thread'=>$thread,'user'=>current_user(),'page'=>$replyPage]); break;

    case $requestUri === '/forum/post':
        Auth::requireAuth();
        if ($method!=='POST' || !validate_csrf()) { http_response_code(405); break; }
        try { Community::post((int)current_user()['id'], !empty($_POST['thread_id'])?(int)$_POST['thread_id']:null, (string)($_POST['title']??''),(string)($_POST['body']??''),(string)($_POST['category']??'General')); flash('success','Posting berhasil dikirim.'); }
        catch(Throwable $e) { flash('error',$e->getMessage()); }
        redirect(!empty($_POST['thread_id'])?'/forum/'.(int)$_POST['thread_id']:'/forum');

    case $requestUri === '/forum/edit' || $requestUri === '/forum/delete':
        Auth::requireAuth(); if($method!=='POST'||!validate_csrf()){http_response_code(405);break;}
        $db=Database::getConnection();$id=(int)($_POST['post_id']??0);$q=$db->prepare('SELECT id,thread_id FROM forum_posts WHERE id=:id');$q->execute(['id'=>$id]);$post=$q->fetch();
        if(!$post){flash('error','Posting tidak ditemukan.');redirect('/forum');}
        try { $admin=Auth::isAdmin(); $ok=$requestUri==='/forum/edit'?Community::updateOwnPost((int)current_user()['id'],$id,(string)($_POST['body']??''),$admin):Community::deleteOwnPost((int)current_user()['id'],$id,$admin); if(!$ok)throw new RuntimeException('Anda tidak memiliki izin untuk mengubah posting ini.');flash('success',$requestUri==='/forum/edit'?'Posting diperbarui.':'Posting dihapus.'); }
        catch(Throwable $e){flash('error',$e->getMessage());}
        redirect($post['thread_id']?'/forum/'.$post['thread_id']:'/forum');

    case $requestUri === '/forum/report':
        Auth::requireAuth();
        if ($method!=='POST' || !validate_csrf()) { http_response_code(405); break; }
        $db=Database::getConnection(); $q=$db->prepare("INSERT INTO forum_reports(post_id,reporter_id,reason,details,created_at) SELECT id,:reporter,:reason,:details,NOW() FROM forum_posts WHERE id=:post_id AND hidden_at IS NULL AND user_id<>:own_user AND NOT EXISTS(SELECT 1 FROM forum_reports WHERE post_id=:pending_post AND reporter_id=:pending_user AND status='pending')");
        $reason=$_POST['reason']??'';$q->execute(['reporter'=>(int)current_user()['id'],'reason'=>in_array($reason,['harassment','spam','disruptive','other'],true)?$reason:'other','details'=>substr(trim((string)($_POST['details']??'')),0,1000),'post_id'=>(int)($_POST['post_id']??0),'own_user'=>(int)current_user()['id'],'pending_post'=>(int)($_POST['post_id']??0),'pending_user'=>(int)current_user()['id']]);
        flash($q->rowCount()?'success':'error',$q->rowCount()?'Laporan dikirim untuk ditinjau.':'Laporan tidak dapat dikirim atau masih menunggu peninjauan.'); redirect('/forum');

    case $requestUri === '/appeal':
        Auth::requireAuth();
        if ($method==='POST' && validate_csrf()) { $message=trim((string)($_POST['message']??'')); if (Community::textLength($message)>=10 && Community::textLength($message)<=3000) { $db=Database::getConnection();$q=$db->prepare("INSERT INTO user_appeals(user_id,message) VALUES(:u,:m)");$q->execute(['u'=>(int)current_user()['id'],'m'=>$message]);flash('success','Banding Anda telah diteruskan kepada administrator.'); } else flash('error','Banding harus berisi 10–3.000 karakter.'); redirect('/appeal'); }
        $db=Database::getConnection();$q=$db->prepare("SELECT account_status,post_status,status_reason,post_suspended_until FROM users WHERE id=:u");$q->execute(['u'=>(int)current_user()['id']]);$restriction=$q->fetch();$q=$db->prepare("SELECT action_type,reason,duration_days,created_at FROM moderation_history WHERE target_user_id=:u ORDER BY created_at DESC LIMIT 20");$q->execute(['u'=>(int)current_user()['id']]);$history=$q->fetchAll();$q=$db->prepare("SELECT message,status,admin_response,created_at,reviewed_at FROM user_appeals WHERE user_id=:u ORDER BY created_at DESC LIMIT 20");$q->execute(['u'=>(int)current_user()['id']]);$appeals=$q->fetchAll();
        view('pages.forum',['title'=>'Status akun dan banding','heading'=>'Status akun dan banding','body'=>'Jika Anda merasa keputusan moderator keliru, kirimkan penjelasan untuk ditinjau.','restriction'=>$restriction,'history'=>$history,'appeals'=>$appeals]); break;

    case $requestUri === '/admin':
        Auth::requireAdmin(); $db=Database::getConnection();
        $stats=['lessons'=>(int)$db->query('SELECT COUNT(*) FROM lessons')->fetchColumn(),'published'=>(int)$db->query('SELECT COUNT(*) FROM lessons WHERE is_published=1 AND archived_at IS NULL')->fetchColumn(),'threads'=>(int)$db->query('SELECT COUNT(*) FROM forum_posts WHERE thread_id IS NULL AND hidden_at IS NULL')->fetchColumn(),'pending'=>(int)$db->query("SELECT COUNT(*) FROM forum_reports WHERE status='pending'")->fetchColumn(),'actions'=>(int)$db->query('SELECT COUNT(*) FROM moderation_history')->fetchColumn()];
        view('pages.admin',['title'=>'Administrasi','heading'=>'Administrasi FluentPath','stats'=>$stats]); break;

    case $requestUri === '/admin/materials' || $requestUri === '/admin/materials/new' || preg_match('#^/admin/materials/([0-9]+)$#',$requestUri,$matches):
        Auth::requireAdmin(); $db=Database::getConnection(); $edit=preg_match('#^/admin/materials/([0-9]+)$#',$requestUri,$matches)?(int)$matches[1]:0;
        if ($method==='POST') {
            if (!validate_csrf()) { http_response_code(400); echo 'CSRF invalid.'; break; }
            $id=(int)($_POST['id']??0); $fields=['level','skill','order_num','title','learning_objectives','explanation','examples','notes','practice_guide','further_reading','xp_reward'];
            $data=[]; foreach($fields as $f)$data[$f]=trim((string)($_POST[$f]??''));
            if (!in_array($data['level'],['A1','A2','B1','B2','C1','C2'],true)||!in_array($data['skill'],['Grammar','Vocabulary','Reading','Listening','Writing','Speaking'],true)||$data['title']===''||$data['learning_objectives']===''||$data['explanation']===''||$data['examples']==='') { flash('error','Level, skill, judul, tujuan, materi, dan contoh wajib diisi.'); redirect($id?'/admin/materials/'.$id:'/admin/materials/new'); }
            $data['order_num']=max(1,(int)$data['order_num']);$data['xp_reward']=max(0,min(1000,(int)$data['xp_reward']));
            if ($id) { $sql='UPDATE lessons SET level=:level,skill=:skill,order_num=:order_num,title=:title,learning_objectives=:learning_objectives,explanation=:explanation,examples=:examples,notes=:notes,practice_guide=:practice_guide,further_reading=:further_reading,xp_reward=:xp_reward,is_published=:pub WHERE id=:id'; $data['id']=$id;$data['pub']=isset($_POST['is_published'])?1:0; }
            else { $sql='INSERT INTO lessons(level,skill,order_num,title,learning_objectives,explanation,examples,notes,practice_guide,further_reading,xp_reward,is_published) VALUES(:level,:skill,:order_num,:title,:learning_objectives,:explanation,:examples,:notes,:practice_guide,:further_reading,:xp_reward,:pub)';$data['pub']=isset($_POST['is_published'])?1:0; }
            $db->prepare($sql)->execute($data); flash('success','Materi tersimpan.'); redirect('/admin/materials');
        }
        if ($_SERVER['REQUEST_METHOD']==='POST') break;
        if ($edit) { $q=$db->prepare('SELECT * FROM lessons WHERE id=:id');$q->execute(['id'=>$edit]);$material=$q->fetch();if(!$material){http_response_code(404);echo 'Materi tidak ditemukan.';break;} view('pages.admin',['title'=>'Edit materi','heading'=>'Edit materi','material'=>$material]); }
        elseif ($requestUri==='/admin/materials/new') view('pages.admin',['title'=>'Materi baru','heading'=>'Materi baru','material'=>[]]);
        else { $page=max(1,(int)($_GET['page']??1));$q=$db->prepare('SELECT id,level,skill,title,is_published,archived_at FROM lessons ORDER BY level,order_num,id LIMIT 30 OFFSET :off');$q->bindValue('off',($page-1)*30,PDO::PARAM_INT);$q->execute();view('pages.admin',['title'=>'Materi belajar','heading'=>'Materi belajar','materials'=>$q->fetchAll(),'page'=>$page]); }
        break;

    case preg_match('#^/admin/materials/([0-9]+)/(archive|restore|delete)$#',$requestUri,$matches):
        Auth::requireAdmin(); if($method!=='POST'||!validate_csrf()){http_response_code(405);break;} $db=Database::getConnection();$id=(int)$matches[1];
        if($matches[2]==='archive')$db->prepare('UPDATE lessons SET archived_at=NOW() WHERE id=:id')->execute(['id'=>$id]);
        elseif($matches[2]==='restore')$db->prepare('UPDATE lessons SET archived_at=NULL,is_published=1 WHERE id=:id')->execute(['id'=>$id]);
        else { $q=$db->prepare('SELECT COUNT(*) FROM user_lesson_progress WHERE lesson_id=:id');$q->execute(['id'=>$id]);if((int)$q->fetchColumn()>0){flash('error','Materi memiliki riwayat belajar. Arsipkan agar progres tetap terjaga.');redirect('/admin/materials');}$db->prepare('DELETE FROM lessons WHERE id=:id')->execute(['id'=>$id]); }
        flash('success',$matches[2]==='archive'?'Materi diarsipkan.':($matches[2]==='restore'?'Materi dipulihkan dan diterbitkan.':'Materi dihapus.'));redirect('/admin/materials');

    case $requestUri === '/admin/reports':
        Auth::requireAdmin();
        $db = Database::getConnection();
        $q = $db->query("SELECT r.*,p.body,p.title,p.thread_id,p.user_id target_id,u.username reporter,t.username target_name FROM forum_reports r JOIN forum_posts p ON p.id=r.post_id JOIN users u ON u.id=r.reporter_id JOIN users t ON t.id=p.user_id ORDER BY FIELD(r.status,'pending','resolved','dismissed'),r.created_at DESC LIMIT 100");
        $reports = $q->fetchAll();
        $restrictedUsers = $db->query("SELECT id, username, role, account_status, post_status, post_suspended_until, status_reason FROM users WHERE account_status != 'active' OR post_status != 'active' ORDER BY username ASC")->fetchAll();
        $auditHistory = $db->query("SELECT m.*, a.username AS admin_name, t.username AS target_name FROM moderation_history m JOIN users a ON a.id = m.admin_id JOIN users t ON t.id = m.target_user_id ORDER BY m.created_at DESC LIMIT 50")->fetchAll();
        view('pages.admin', ['title' => 'Moderasi', 'heading' => 'Moderasi dan Laporan Komunitas', 'reports' => $reports, 'restrictedUsers' => $restrictedUsers, 'auditHistory' => $auditHistory]);
        break;

    case $requestUri === '/admin/appeals':
        Auth::requireAdmin();$db=Database::getConnection();$q=$db->query("SELECT a.*,u.username FROM user_appeals a JOIN users u ON u.id=a.user_id ORDER BY FIELD(a.status,'pending','reviewed'),a.created_at DESC LIMIT 100");view('pages.admin',['title'=>'Banding pengguna','heading'=>'Banding pengguna','appeals'=>$q->fetchAll()]);break;

    case $requestUri === '/admin/appeal-review':
        Auth::requireAdmin();if($method!=='POST'||!validate_csrf()){http_response_code(405);break;}$response=trim((string)($_POST['admin_response']??''));if($response===''){flash('error','Tanggapan wajib diisi.');redirect('/admin/appeals');}$db=Database::getConnection();$db->prepare("UPDATE user_appeals SET status='reviewed',admin_response=:r,reviewed_by=:a,reviewed_at=NOW() WHERE id=:id")->execute(['r'=>substr($response,0,3000),'a'=>(int)current_user()['id'],'id'=>(int)($_POST['appeal_id']??0)]);flash('success','Tanggapan banding tersimpan.');redirect('/admin/appeals');

    case $requestUri === '/admin/moderate':
        Auth::requireAdmin();
        if ($method !== 'POST' || !validate_csrf()) { http_response_code(405); break; }
        try {
            $targetId = (int)($_POST['target_user_id'] ?? 0);
            if ($targetId === 0 && !empty($_POST['target_username'])) {
                $uStmt = Database::getConnection()->prepare("SELECT id FROM users WHERE username = :u LIMIT 1");
                $uStmt->execute(['u' => trim((string)$_POST['target_username'])]);
                $targetId = (int)$uStmt->fetchColumn();
                if ($targetId === 0) throw new RuntimeException('Pengguna dengan nama tersebut tidak ditemukan.');
            }
            if ($targetId === 0) throw new RuntimeException('Pengguna target tidak valid.');
            Community::moderate(
                (int)current_user()['id'],
                $targetId,
                (string)($_POST['action'] ?? ''),
                trim((string)($_POST['reason'] ?? '')),
                !empty($_POST['report_id']) ? (int)$_POST['report_id'] : null,
                !empty($_POST['duration_days']) ? (int)$_POST['duration_days'] : null,
                !empty($_POST['content_id']) ? (int)$_POST['content_id'] : null
            );
            flash('success', 'Tindakan moderasi berhasil dicatat.');
        } catch (Throwable $e) { flash('error', $e->getMessage()); }
        redirect('/admin/reports');

    default:
        http_response_code(404);
        echo "<!doctype html><html lang='id'><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>404 · FluentPath</title><link rel='stylesheet' href='/assets/css/style.css'></head><body><main class='system-message'><p class='eyebrow'>404</p><h1>Halaman tidak ditemukan</h1><p>Alamat ini mungkin sudah berubah atau tidak tersedia.</p><a href='/' class='button button-primary'>Kembali ke beranda</a></main></body></html>";
        break;
}
