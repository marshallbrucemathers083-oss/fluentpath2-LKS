<?php require dirname(__DIR__) . '/layouts/header.php'; ?>
<header class="page-header admin-heading"><p class="eyebrow">Kelola FluentPath</p><h1><?= e($heading) ?></h1><nav class="admin-nav"><a href="/admin">Ringkasan</a><a href="/admin/materials">Materi</a><a href="/admin/reports">Moderasi</a><a href="/admin/appeals">Banding</a><a href="/forum">Forum</a></nav></header>
<?php if(isset($stats)): ?><section class="admin-summary"><div><span>Materi</span><strong><?= (int)$stats['lessons'] ?></strong></div><div><span>Terbit</span><strong><?= (int)$stats['published'] ?></strong></div><div><span>Diskusi</span><strong><?= (int)$stats['threads'] ?></strong></div><div><span>Laporan menunggu</span><strong><?= (int)$stats['pending'] ?></strong></div><div><span>Tindakan tercatat</span><strong><?= (int)$stats['actions'] ?></strong></div></section><section class="admin-section"><h2>Area kerja</h2><p>Perubahan materi berlaku pada katalog belajar. Laporan komunitas menampilkan konten yang perlu ditinjau lebih dulu.</p><p class="button-row"><a class="button button-primary" href="/admin/materials">Kelola materi</a><a class="button button-secondary" href="/admin/reports">Tinjau laporan</a><a class="button button-secondary" href="/admin/appeals">Baca banding</a></p></section>
<?php elseif(isset($material)): ?><form class="admin-section" method="post" action="<?= !empty($material['id'])?'/admin/materials/'.(int)$material['id']:'/admin/materials/new' ?>"><?= csrf_field() ?><?php if(!empty($material['id'])):?><input type="hidden" name="id" value="<?= (int)$material['id'] ?>"><?php endif; ?>
<div class="admin-form-grid"><label class="form-group"><span>Level CEFR</span><select name="level" required><?php foreach(['A1','A2','B1','B2','C1','C2'] as $v): ?><option<?= ($material['level']??'A1')===$v?' selected':'' ?>><?= $v ?></option><?php endforeach; ?></select></label><label class="form-group"><span>Keterampilan</span><select name="skill"><?php foreach(['Grammar','Vocabulary','Reading','Listening','Writing','Speaking'] as $v): ?><option<?= ($material['skill']??'Grammar')===$v?' selected':'' ?>><?= $v ?></option><?php endforeach; ?></select></label><label class="form-group"><span>Urutan</span><input type="number" name="order_num" min="1" value="<?= (int)($material['order_num']??1) ?>"></label><label class="form-group"><span>XP hadiah</span><input type="number" name="xp_reward" min="0" max="1000" value="<?= (int)($material['xp_reward']??25) ?>"></label><label class="form-group admin-wide"><span>Judul</span><input name="title" maxlength="255" value="<?= e($material['title']??'') ?>" required></label>
<?php foreach(['learning_objectives'=>'Tujuan belajar','explanation'=>'Materi','examples'=>'Contoh','notes'=>'Catatan','practice_guide'=>'Panduan latihan','further_reading'=>'Bacaan lanjutan'] as $key=>$label): ?><label class="form-group admin-wide"><span><?= e($label) ?></span><textarea name="<?= e($key) ?>"<?= in_array($key,['learning_objectives','explanation','examples'],true)?' required':'' ?>><?= e($material[$key]??'') ?></textarea></label><?php endforeach; ?></div><label class="admin-check"><input type="checkbox" name="is_published" value="1"<?= !isset($material['is_published'])||$material['is_published']?' checked':'' ?>> Terbitkan materi</label><button class="button button-primary">Simpan materi</button></form>
<?php elseif(isset($materials)): ?><p><a class="button button-primary" href="/admin/materials/new">Tambah materi</a></p><div class="table-wrap"><table><thead><tr><th>Materi</th><th>Level</th><th>Keterampilan</th><th>Status</th><th>Aksi</th></tr></thead><tbody><?php foreach($materials as $m): ?><tr><td><a href="/admin/materials/<?= (int)$m['id'] ?>"><?= e($m['title']) ?></a></td><td><?= e($m['level']) ?></td><td><?= e($m['skill']) ?></td><td><?= $m['archived_at']?'Diarsipkan':($m['is_published']?'Terbit':'Draf') ?></td><td><a href="/admin/materials/<?= (int)$m['id'] ?>">Edit</a> <?php if($m['archived_at']): ?><form class="inline-form" method="post" action="/admin/materials/<?= (int)$m['id'] ?>/restore"><?= csrf_field() ?><button class="button button-quiet">Pulihkan</button></form><?php else: ?><form class="inline-form" method="post" action="/admin/materials/<?= (int)$m['id'] ?>/archive" onsubmit="return confirm('Arsipkan materi ini?')"><?= csrf_field() ?><button class="button button-quiet">Arsipkan</button></form><?php endif; ?><form class="inline-form" method="post" action="/admin/materials/<?= (int)$m['id'] ?>/delete" onsubmit="return confirm('Hapus materi ini secara permanen? Materi dengan progres tersimpan hanya bisa diarsipkan.')"><?= csrf_field() ?><button class="button button-quiet">Hapus</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php if($page>1): ?><a href="?page=<?= $page-1 ?>">Sebelumnya</a><?php endif; ?><a href="?page=<?= $page+1 ?>">Berikutnya</a>
<?php elseif(isset($reports)): ?>
<section class="admin-reports">
    <h2>Laporan komunitas</h2>
    <?php if(!$reports): ?><p class="empty-inline">Belum ada laporan komunitas.</p><?php endif; ?>
    <?php foreach($reports as $r): ?>
    <article class="admin-report">
        <header>
            <div>
                <p class="community-meta">Laporan <?= e($r['status']) ?> · <?= e($r['reason']) ?> · <?= e($r['created_at']) ?></p>
                <h2><?= e($r['title'] ?: 'Balasan') ?> oleh <?= e($r['target_name']) ?></h2>
                <p>Dilaporkan oleh <?= e($r['reporter']) ?></p>
            </div>
            <a href="/forum/<?= (int)($r['thread_id']?:$r['post_id']) ?>">Lihat diskusi</a>
        </header>
        <blockquote><?= nl2br(e($r['body'])) ?></blockquote>
        <?php if($r['details']): ?><p>Catatan pelapor: <?= e($r['details']) ?></p><?php endif; ?>
        <form action="/admin/moderate" method="post" class="moderation-form">
            <?= csrf_field() ?>
            <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>">
            <input type="hidden" name="target_user_id" value="<?= (int)$r['target_id'] ?>">
            <input type="hidden" name="content_id" value="<?= (int)$r['post_id'] ?>">
            <label class="form-group">
                <span>Tindakan</span>
                <select name="action">
                    <option value="dismiss">Tutup laporan</option>
                    <option value="warning">Peringatan formal</option>
                    <option value="post_suspend">Bekukan posting</option>
                    <option value="account_suspend">Suspensi akun</option>
                    <option value="ban">Ban akun</option>
                    <option value="hide">Sembunyikan konten</option>
                    <option value="remove">Hapus konten permanen</option>
                    <option value="reinstate">Pulihkan status akun</option>
                </select>
            </label>
            <label class="form-group">
                <span>Durasi posting (hari)</span>
                <input type="number" name="duration_days" min="1" max="365" value="7">
            </label>
            <label class="form-group">
                <span>Alasan atau catatan penyelesaian</span>
                <input name="reason" maxlength="1000" required>
            </label>
            <button class="button button-primary">Catat keputusan</button>
        </form>
    </article>
    <?php endforeach; ?>
</section>

<section class="admin-section">
    <h2>Moderasi pengguna langsung</h2>
    <p>Beri peringatan, terapkan pembatasan, atau pulihkan status akun pengguna tertentu.</p>
    <form action="/admin/moderate" method="post" class="moderation-form">
        <?= csrf_field() ?>
        <label class="form-group">
            <span>Nama pengguna</span>
            <input type="text" name="target_username" placeholder="Nama pengguna terdaftar" required>
        </label>
        <label class="form-group">
            <span>Tindakan</span>
            <select name="action">
                <option value="warning">Peringatan formal</option>
                <option value="post_suspend">Bekukan posting</option>
                <option value="account_suspend">Suspensi akun</option>
                <option value="ban">Ban akun</option>
                <option value="reinstate">Pulihkan status akun</option>
            </select>
        </label>
        <label class="form-group">
            <span>Durasi posting (hari)</span>
            <input type="number" name="duration_days" min="1" max="365" value="7">
        </label>
        <label class="form-group">
            <span>Alasan tindakan</span>
            <input name="reason" maxlength="1000" placeholder="Alasan yang jelas untuk pengguna" required>
        </label>
        <button class="button button-primary">Terapkan tindakan</button>
    </form>
</section>

<section class="admin-section">
    <h2>Pengguna dibatasi saat ini</h2>
    <?php if (empty($restrictedUsers)): ?>
        <p class="empty-inline">Tidak ada akun pengguna yang sedang dibatasi.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Status akun</th>
                        <th>Posting</th>
                        <th>Batas posting</th>
                        <th>Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($restrictedUsers as $ru): ?>
                        <tr>
                            <td><strong><?= e($ru['username']) ?></strong></td>
                            <td><?= e($ru['account_status']) ?></td>
                            <td><?= e($ru['post_status']) ?></td>
                            <td><?= e($ru['post_suspended_until'] ?? '-') ?></td>
                            <td><?= e($ru['status_reason'] ?? '-') ?></td>
                            <td>
                                <form method="post" action="/admin/moderate" class="inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="target_user_id" value="<?= (int)$ru['id'] ?>">
                                    <input type="hidden" name="action" value="reinstate">
                                    <input type="hidden" name="reason" value="Pemulihan akun oleh administrator">
                                    <button class="button button-quiet" type="submit">Pulihkan</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="admin-section">
    <h2>Riwayat audit moderasi</h2>
    <?php if (empty($auditHistory)): ?>
        <p class="empty-inline">Belum ada riwayat tindakan moderasi.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Admin</th>
                        <th>Target</th>
                        <th>Tindakan</th>
                        <th>Durasi</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($auditHistory as $item): ?>
                        <tr>
                            <td><time datetime="<?= e($item['created_at']) ?>"><?= e($item['created_at']) ?></time></td>
                            <td><?= e($item['admin_name']) ?></td>
                            <td><strong><?= e($item['target_name']) ?></strong></td>
                            <td><?= e(str_replace('_', ' ', ucfirst($item['action_type']))) ?></td>
                            <td><?= $item['duration_days'] ? (int)$item['duration_days'] . ' hari' : '-' ?></td>
                            <td><?= e($item['reason']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php elseif(isset($appeals)): ?><section class="admin-reports"><?php if(!$appeals): ?><p class="empty-inline">Belum ada banding.</p><?php endif; foreach($appeals as $appeal): ?><article class="admin-report"><p class="community-meta">Banding <?= e($appeal['status']) ?> · <?= e($appeal['created_at']) ?> · dari <?= e($appeal['username']) ?></p><blockquote><?= nl2br(e($appeal['message'])) ?></blockquote><?php if($appeal['status']==='pending'): ?><form method="post" action="/admin/appeal-review" class="appeal-review-form"><?= csrf_field() ?><input type="hidden" name="appeal_id" value="<?= (int)$appeal['id'] ?>"><label class="form-group"><span>Tanggapan kepada pengguna</span><textarea name="admin_response" maxlength="3000" required></textarea></label><button class="button button-primary">Tandai telah ditinjau</button></form><?php else: ?><p><strong>Tanggapan:</strong> <?= e($appeal['admin_response']) ?></p><?php endif; ?></article><?php endforeach; ?></section>
<?php endif; ?>
<?php require dirname(__DIR__) . '/layouts/footer.php'; ?>
