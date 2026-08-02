<?php
// programs.php
require_once 'common/config.php';
require_once 'common/loginFunctions.php';

$search     = trim($_GET['search'] ?? '');
$categoryId = (int) ($_GET['category'] ?? 0);
$sort       = $_GET['sort'] ?? 'popular';

$categories = $connection->query('SELECT * FROM course_category ORDER BY Category_Name ASC');

$where  = 'WHERE c.Is_Active = 1';
$params = [];
$types  = '';

if ($search) {
    $where .= ' AND (c.Title LIKE ? OR c.Description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types   .= 'ss';
}
if ($categoryId) {
    $where .= ' AND c.Category_Id = ?';
    $params[] = $categoryId;
    $types   .= 'i';
}

$orderBy = match ($sort) {
    'newest' => 'c.Created_At DESC',
    'title'  => 'c.Title ASC',
    default  => 'enrollments DESC',
};

$sql = "SELECT c.Course_Id, c.Title, c.Description, c.Intro_Image, c.Due_Date,
               cat.Category_Name, u.First_Name, u.Last_Name, u.User_Name AS provider_username,
               COUNT(DISTINCT e.Enrollment_Id) AS enrollments,
               COALESCE(AVG(r.Rating), 0) AS avg_rating,
               COUNT(DISTINCT r.Review_Id) AS review_count,
               (SELECT COUNT(*) FROM weekly_course wc WHERE wc.Course_Id = c.Course_Id) AS week_count
        FROM course c
        LEFT JOIN course_category cat ON cat.Category_Id = c.Category_Id
        LEFT JOIN registered_user u   ON u.Registered_User_Id = c.Provider_Id
        LEFT JOIN enrollment e        ON e.Course_Id = c.Course_Id
        LEFT JOIN review r            ON r.Course_Id = c.Course_Id
        $where
        GROUP BY c.Course_Id
        ORDER BY $orderBy";

if ($params) {
    $stmt = $connection->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $courses = $stmt->get_result();
} else {
    $courses = $connection->query($sql);
}

$totalCount = $courses->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Programmes — Educaster</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/programs.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include 'common/header.php'; ?>

<section class="prog-hero">
  <div class="page-wrapper" style="padding-top:0;padding-bottom:0">
    <h1>Browse Programmes</h1>
    <p>Find the perfect teacher training course for your career goals</p>
    <form action="programs.php" method="GET" class="prog-search-form">
      <div class="prog-search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search courses, topics, skills...">
        <button type="submit" class="btn btn-primary">Search</button>
      </div>
    </form>
  </div>
</section>

<div class="page-wrapper prog-layout">

  <aside class="prog-sidebar">
    <div class="filter-box">
      <h3><i class="fas fa-filter"></i> Filter</h3>
      <form action="programs.php" method="GET" id="filterForm">
        <?php if ($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
        <div class="filter-group">
          <label class="filter-group-label">Category</label>
          <label class="filter-option">
            <input type="radio" name="category" value="0" <?= !$categoryId ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()">
            All Categories
          </label>
          <?php $categories->data_seek(0); while ($cat = $categories->fetch_assoc()): ?>
          <label class="filter-option">
            <input type="radio" name="category" value="<?= (int) $cat['Category_Id'] ?>" <?= $categoryId == $cat['Category_Id'] ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()">
            <?= htmlspecialchars($cat['Category_Name']) ?>
          </label>
          <?php endwhile; ?>
        </div>
        <div class="filter-group">
          <label class="filter-group-label">Sort By</label>
          <select name="sort" class="form-control" onchange="document.getElementById('filterForm').submit()">
            <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Most Popular</option>
            <option value="newest"  <?= $sort === 'newest'  ? 'selected' : '' ?>>Newest First</option>
            <option value="title"   <?= $sort === 'title'   ? 'selected' : '' ?>>A–Z Title</option>
          </select>
        </div>
        <?php if ($search || $categoryId): ?>
        <a href="programs.php" class="btn btn-outline btn-sm btn-block"><i class="fas fa-times"></i> Clear Filters</a>
        <?php endif; ?>
      </form>
    </div>
  </aside>

  <main class="prog-main">
    <div class="prog-toolbar">
      <p class="result-count"><strong><?= $totalCount ?></strong> programme<?= $totalCount != 1 ? 's' : '' ?> found</p>
    </div>

    <?php if ($totalCount === 0): ?>
    <div class="empty-state">
      <i class="fas fa-magnifying-glass"></i>
      <h3>No courses found</h3>
      <p>Try adjusting your filters or search term.</p>
      <a href="programs.php" class="btn btn-primary">View All Courses</a>
    </div>
    <?php else: ?>
    <div class="prog-grid">
      <?php while ($row = $courses->fetch_assoc()):
          $stars   = round((float) $row['avg_rating']);
          $expired = $row['Due_Date'] && $row['Due_Date'] < date('Y-m-d');
          $instructor = trim($row['First_Name'] . ' ' . $row['Last_Name']) ?: $row['provider_username'];
      ?>
      <a href="courses/course_overview.php?id=<?= (int) $row['Course_Id'] ?>" class="prog-card reveal">
        <div class="p-img">
          <?php if ($row['Intro_Image']): ?>
            <img src="uploads/<?= htmlspecialchars($row['Intro_Image']) ?>" alt="<?= htmlspecialchars($row['Title']) ?>">
          <?php else: ?>
            <div class="img-placeholder"><i class="fas fa-book-open"></i></div>
          <?php endif; ?>
          <span class="status-dot <?= $expired ? 'expired' : 'active' ?>"><?= $expired ? 'Expired' : 'Active' ?></span>
        </div>
        <div class="p-body">
          <span class="pill"><?= htmlspecialchars($row['Category_Name'] ?? 'Teacher Training') ?></span>
          <h4><?= htmlspecialchars($row['Title']) ?></h4>
          <p class="p-desc"><?= htmlspecialchars(truncate_text($row['Description'] ?? '', 100)) ?></p>
          <div class="p-meta">
            <span class="stars"><?= render_stars((float) $row['avg_rating']) ?> <small>(<?= (int) $row['review_count'] ?>)</small></span>
            <span><i class="fas fa-users"></i> <?= (int) $row['enrollments'] ?></span>
            <span><i class="fas fa-list"></i> <?= (int) $row['week_count'] ?> weeks</span>
          </div>
          <div class="p-footer">
            <span class="p-instructor"><i class="fas fa-user"></i> <?= htmlspecialchars($instructor) ?></span>
            <span class="p-enroll">Enroll Free <i class="fas fa-arrow-right"></i></span>
          </div>
        </div>
      </a>
      <?php endwhile; ?>
    </div>
    <?php endif; ?>
  </main>
</div>

<?php include 'common/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>