<?php
class IndexStatus {
  public function __construct($file) {
    $this->connected = false;
    $dir = dirname($file);

    if (is_readable($file) && is_writable($dir)) {
      $this->db = new SQLite3($file, SQLITE3_OPEN_READONLY);
      $this->connected = true;
    } else {
      echo "          <tr class='danger'>" . PHP_EOL;

      if (!is_readable($file)) {
        echo "            <td colspan='4'>{$file} doesn't exist or is not readable!</td>" . PHP_EOL;
      } elseif (!is_writable($dir)) {
        echo "            <td colspan='4'>{$dir} is not writable!</td>" . PHP_EOL;
      }

      echo "          </tr>" . PHP_EOL;
    }
  }

  private function runSummaryQuery($filters) {
    $query = <<<EOQ
SELECT metadata_items.library_section_id AS 'library_id', library_sections.name AS 'library_name', COUNT(*) AS 'count'
FROM metadata_items
JOIN library_sections ON metadata_items.library_section_id = library_sections.id
JOIN media_items ON metadata_items.id = media_items.metadata_item_id
JOIN media_parts ON media_items.id = media_parts.media_item_id
{$filters}
GROUP BY metadata_items.library_section_id
EOQ;
    return $this->db->query($query);
  }

  private function runDetailedQuery($filters, $library_id) {
    $query = <<<EOQ
SELECT metadata_items.library_section_id AS 'library_id', library_sections.name AS 'library_name', metadata_items.id AS 'item_id', media_parts.file AS 'file'
FROM metadata_items
JOIN library_sections ON metadata_items.library_section_id = library_sections.id
JOIN media_items ON metadata_items.id = media_items.metadata_item_id
JOIN media_parts ON media_items.id = media_parts.media_item_id
{$filters}
AND metadata_items.library_section_id = {$library_id}
ORDER BY metadata_items.library_section_id, media_parts.file
EOQ;
    return $this->db->query($query);
  }

  public function showStatus($status) {
    if (!$this->connected) return;

    switch($status) {
      case 'complete':
        $filters = <<<EOF
WHERE media_parts.extra_data LIKE '%indexes%'
AND media_parts.extra_data NOT LIKE '%failureBIF%'
AND media_parts.extra_data NOT LIKE ''
EOF;
        $class = 'success';
        break;
      case 'pending':
        $filters = <<<EOQ
WHERE media_parts.extra_data NOT LIKE '%indexes%'
AND media_parts.extra_data NOT LIKE '%failureBIF%'
AND media_parts.extra_data NOT LIKE ''
EOQ;
        $class = 'info';
        break;
      case 'failed':
        $filters = <<<EOQ
WHERE media_parts.extra_data NOT LIKE '%indexes%'
AND media_parts.extra_data LIKE '%failureBIF%'
AND media_parts.extra_data NOT LIKE ''
EOQ;
        $class = 'warning';
        break;
      case 'unknown':
        $filters = <<<EOQ
WHERE media_parts.extra_data NOT LIKE '%indexes%'
AND media_parts.extra_data NOT LIKE '%failureBIF%'
AND media_parts.extra_data LIKE ''
EOQ;
        $class = 'danger';
        break;
    }

    $summaries = $this->runSummaryQuery($filters);

    while ($summary = $summaries->fetchArray(SQLITE3_ASSOC)) {
      $item_count = number_format($summary['count']);

      echo "          <tr class='{$class}' data-toggle='collapse' data-target='.{$summary['library_id']}-{$status}'>" . PHP_EOL;
      echo "            <td>{$summary['library_id']}</td>" . PHP_EOL;
      echo "            <td>{$summary['library_name']}</td>" . PHP_EOL;
      echo "            <td><span class='btn badge'>{$item_count}</span></td>" . PHP_EOL;
      echo "            <td>{$status}</td>" . PHP_EOL;
      echo "          </tr>" . PHP_EOL;

      if ($summary['count'] < 250 || ($summary['count'] < 500 && isset($_REQUEST['omg'])) || ($summary['count'] < 1000 && isset($_REQUEST['wtf'])) || isset($_REQUEST['insanity'])) {
        $details = $this->runDetailedQuery($filters, $summary['library_id']);

        while ($detail = $details->fetchArray(SQLITE3_ASSOC)) {
          $exclude_from_file_path = '/mnt/media/';
          $file_path = substr($detail['file'], strpos($detail['file'], $exclude_from_file_path) + strlen($exclude_from_file_path));

          echo "          <tr class='collapse {$summary['library_id']}-{$status}'>" . PHP_EOL;
          echo "            <td>{$detail['item_id']}</td>" . PHP_EOL;
          echo "            <td colspan='3'>{$file_path}</td>" . PHP_EOL;
          echo "          </tr>" . PHP_EOL;
        }
      } else {
          echo "          <tr class='collapse {$summary['library_id']}-{$status}'>" . PHP_EOL;
          echo "            <td colspan='4'>Details unavailable due to size!</td>" . PHP_EOL;
          echo "          </tr>" . PHP_EOL;

      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>Plex Index Status</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/3/darkly/bootstrap.min.css'>
  </head>
  <body>
    <div class='container'>
      <h1>Plex Index Status</h1>
      <p>View the status of how Plex is indexing your media!</p>
      <table class='table table-striped table-hover table-condensed'>
        <thead>
          <tr>
            <th>Library/File&nbsp;ID</th>
            <th>Library/File&nbsp;Name</th>
            <th>Item&nbsp;Count</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
<?php
$obj = new IndexStatus('/data/com.plexapp.plugins.library.db');
$obj->showStatus('unknown');
$obj->showStatus('failed');
$obj->showStatus('pending');
$obj->showStatus('complete');
?>
        </tbody>
      </table>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js' integrity='sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' integrity='sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa' crossorigin='anonymous'></script>
  </body>
</html>
