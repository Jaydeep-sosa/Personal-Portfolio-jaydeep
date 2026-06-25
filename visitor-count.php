<?php
// visitor-count.php - Live visitor counter with file-based storage

$counterFile = 'visitor_count.txt';

header('Content-Type: application/json');

try {
    // Open the file for reading and writing. Create if it doesn't exist.
    // We suppress warnings with @ so we can catch and return a clean JSON error.
    $fp = @fopen($counterFile, 'c+');
    
    if ($fp === false) {
        throw new Exception("Unable to open or create counter file. Please ensure the web server has write permissions for this directory or the visitor_count.txt file.");
    }

    // Acquire an exclusive lock to prevent race conditions
    if (flock($fp, LOCK_EX)) {
        // Clear the cache to get the real file size
        clearstatcache(true, $counterFile);
        $size = filesize($counterFile);
        
        $count = 0;
        if ($size > 0) {
            $count = (int)fread($fp, $size);
        }

        // Increment count
        $count++;

        // Truncate file, rewind pointer, and write new count
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, (string)$count);

        // Release the lock
        flock($fp, LOCK_UN);
    } else {
        throw new Exception("Unable to lock the counter file.");
    }
    
    fclose($fp);

    echo json_encode([
        'success' => true,
        'count' => $count
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
