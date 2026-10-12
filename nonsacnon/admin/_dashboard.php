<?php
// Read-only dashboard data. Uses the same database as the public website.
function admin_dashboard(PDO $pdo, array $query): array {
    $days = filter_var($query['days'] ?? 14, FILTER_VALIDATE_INT);
    if (!in_array($days, [7,14,30], true)) $days = 14;
    $options = ['all' => 'Tất cả hoạt động'];
    $sources = [];
    if (can('stories')) $sources['stories'] = ['series'=>'content','label'=>'Bài viết','url'=>'stories.php','title'=>'title','icon'=>'stories'];
    if (can('gallery')) $sources['gallery_items'] = ['series'=>'content','label'=>'Ảnh thư viện','url'=>'gallery.php','title'=>'title','icon'=>'gallery'];
    if (isset($sources['stories']) || isset($sources['gallery_items'])) $options['content'] = 'Bài viết & ảnh';
    if (can('messages')) {
        $sources['contact_messages'] = ['series'=>'messages','label'=>'Tin liên hệ','url'=>'messages.php','title'=>'name','icon'=>'messages'];
        $options['messages'] = 'Tin liên hệ';
    }
    $series = is_string($query['series'] ?? null) && isset($options[$query['series']]) ? $query['series'] : 'all';
    $today = new DateTimeImmutable('today', new DateTimeZone('Asia/Bangkok'));
    $start = $today->modify('-'.($days-1).' days');
    $end = $today->modify('+1 day');
    $bars = [];
    for ($i=0; $i<$days; $i++) $bars[$start->modify("+$i days")->format('Y-m-d')] = 0;
    $feed = [];
    foreach ($sources as $table => $source) {
        if ($series !== 'all' && $series !== $source['series']) continue;
        $q = $pdo->prepare("SELECT DATE(created_at) day,COUNT(*) total FROM $table WHERE created_at>=? AND created_at<? GROUP BY DATE(created_at)");
        $q->execute([$start->format('Y-m-d H:i:s'),$end->format('Y-m-d H:i:s')]);
        foreach ($q->fetchAll() as $row) if (isset($bars[$row['day']])) $bars[$row['day']] += (int)$row['total'];
        $q = $pdo->prepare("SELECT * FROM $table WHERE created_at>=? AND created_at<? ORDER BY created_at DESC,id DESC LIMIT 12");
        $q->execute([$start->format('Y-m-d H:i:s'),$end->format('Y-m-d H:i:s')]);
        foreach ($q->fetchAll() as $row) {
            $status = $source['label'];
            $url = $source['url'];
            if ($table === 'stories') { $status = $row['status']==='published'?'Đã đăng':'Bản nháp'; $url='story_edit.php?id='.(int)$row['id']; }
            if ($table === 'contact_messages') $status = $row['is_read']?'Đã đọc':'Chưa đọc';
            $feed[] = ['title'=>$row[$source['title']],'date'=>$row['created_at'],'label'=>$source['label'],'status'=>$status,'url'=>$url,'icon'=>$source['icon'],'id'=>(int)$row['id']];
        }
    }
    usort($feed, fn($a,$b)=>strcmp($b['date'],$a['date']) ?: $b['id']<=>$a['id']);
    $count = fn($sql)=>(int)$pdo->query($sql)->fetchColumn();
    $metrics = [];
    if (can('stories')) $metrics[]=['label'=>'Bài viết','value'=>$count('SELECT COUNT(*) FROM stories'),'note'=>$count("SELECT COUNT(*) FROM stories WHERE status='draft'").' bản nháp','url'=>'stories.php','icon'=>'stories'];
    if (can('gallery')) $metrics[]=['label'=>'Ảnh thư viện','value'=>$count('SELECT COUNT(*) FROM gallery_items'),'note'=>'Kho ảnh của dự án','url'=>'gallery.php','icon'=>'gallery'];
    if (can('messages')) $metrics[]=['label'=>'Tin chưa đọc','value'=>$count('SELECT COUNT(*) FROM contact_messages WHERE is_read=0'),'note'=>'Trong '.$count('SELECT COUNT(*) FROM contact_messages').' tin liên hệ','url'=>'messages.php','icon'=>'messages'];
    $current = [];
    if (can('stories')) {
        $current['Bài đã xuất bản']=$count("SELECT COUNT(*) FROM stories WHERE status='published'");
        $current['Bản nháp']=$count("SELECT COUNT(*) FROM stories WHERE status='draft'");
    }
    if (can('workshops')) $current['Workshop mở đăng ký']=$count('SELECT COUNT(*) FROM workshops WHERE is_open=1');
    if (can('messages')) $current['Tin đã đọc']=$count('SELECT COUNT(*) FROM contact_messages WHERE is_read=1');
    $projects = can('projects') ? all_projects($pdo) : [];
    // Match the workshop selected on the homepage; never invent title/date/location.
    $workshop = can('workshops') ? $pdo->query('SELECT * FROM workshops WHERE is_open=1 ORDER BY id LIMIT 1')->fetch() : false;
    return compact('days','series','options','today','start','bars','metrics','current','projects','workshop') + ['feed'=>array_slice($feed,0,12)];
}
