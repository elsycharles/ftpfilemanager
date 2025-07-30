<?php

$sessionName = '';
$USERNAME = ''; 
$PASSWORD_HASH = '';

session_name($sessionName);
session_start();

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function isLoggedIn() {
    global $USERNAME, $PASSWORD_HASH;
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    }
    if (!isset($_SESSION['username']) || $_SESSION['username'] !== $USERNAME) {
        return false;
    }
    if (!isset($_SESSION['password_hash']) || $_SESSION['password_hash'] !== $PASSWORD_HASH) {
        return false;
    }
    return true;
}

function showLoginPage() {
    global $login_error;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - File Manager</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                background-color: #f8f9fa;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
                font-size: 14px;
                line-height: 1.5;
                color: #212529;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .login-container {
                max-width: 400px;
                width: 100%;
                margin: 20px;
                background: white;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            .login-header {
                background: #212529;
                color: white;
                padding: 30px 25px;
                text-align: center;
                border-bottom: 1px solid #dee2e6;
            }

            .login-header h1 {
                font-size: 1.5rem;
                font-weight: 600;
                margin: 0;
                margin-bottom: 8px;
            }

            .login-header .subtitle {
                color: #adb5bd;
                font-size: 0.9rem;
            }

            .login-content {
                padding: 30px 25px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            label {
                display: block;
                color: #495057;
                margin-bottom: 8px;
                font-weight: 500;
                font-size: 0.9rem;
            }

            input[type="password"], input[type="text"] {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #ced4da;
                border-radius: 4px;
                font-size: 0.9rem;
                font-family: inherit;
                transition: border-color 0.15s ease;
            }

            input[type="password"]:focus, input[type="text"]:focus {
                outline: none;
                border-color: #495057;
                box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.1);
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #212529;
                border-radius: 4px;
                background: #212529;
                color: white;
                text-decoration: none;
                font-size: 0.9rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.15s ease;
                font-family: inherit;
            }

            .btn:hover {
                background: #343a40;
                border-color: #343a40;
            }

            .error-message {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
                padding: 12px 16px;
                border-radius: 4px;
                font-size: 0.9rem;
                margin-bottom: 20px;
                text-align: center;
            }

            .login-icon {
                font-size: 2rem;
                margin-bottom: 10px;
            }

            @media (max-width: 768px) {
                .login-container {
                    margin: 10px;
                    border-radius: 0;
                }
                
                .login-content {
                    padding: 20px 15px;
                }
                
                .login-header {
                    padding: 20px 15px;
                }
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <h1>Login required</h1>
                <div class="subtitle">Access to the file manager</div>
            </div>
            
            <div class="login-content">
                <?php if (isset($login_error)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($login_error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <button type="submit" name="login" class="btn">
                        Login
                    </button>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('username').focus();
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $USERNAME && verifyPassword($password, $PASSWORD_HASH)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['username'] = $USERNAME;
        $_SESSION['password_hash'] = $PASSWORD_HASH;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = 'Incorrect username or password.';
        showLoginPage();
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (!isLoggedIn()) {
    showLoginPage();
}

function extractZip($zipFile, $destination) {
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($destination);
        $zip->close();
        return true;
    } else {
        return false;
    }
}

function deleteItems($items = []) {
    $results = [];
    foreach ($items as $item) {
        if (is_dir($item)) {
            $results[$item] = deleteDir($item);
        } elseif (file_exists($item)) {
            $results[$item] = unlink($item);
        } else {
            $results[$item] = false;
        }
    }
    return $results;
}

function deleteDir($dir) {
    if (!is_dir($dir)) return false;
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = "$dir/$file";
        if (is_dir($path)) {
            deleteDir($path);
        } else {
            unlink($path);
        }
    }
    return rmdir($dir);
}

function copyDir($src, $dst) {
    if (!is_dir($src)) return false;
    if (!is_dir($dst)) mkdir($dst, 0777, true);
    $files = array_diff(scandir($src), ['.', '..']);
    foreach ($files as $file) {
        $srcPath = "$src/$file";
        $dstPath = "$dst/$file";
        if (is_dir($srcPath)) {
            copyDir($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
        }
    }
    return true;
}

function moveItems($items, $destination) {
    global $baseDir;
    $results = [];

    $destination = trim($destination, '/\\');
    if (empty($destination)) {
        $destPath = $baseDir;
    } else {
        $destPath = $baseDir . '/' . $destination;
    }
    
    if (!is_dir($destPath)) {
        mkdir($destPath, 0777, true);
    }
    
    foreach ($items as $item) {
        $base = basename($item);
        $finalDestPath = $destPath . '/' . $base;
        $results[$item] = rename($item, $finalDestPath);
    }
    return $results;
}

function copyItems($items, $destination) {
    global $baseDir;
    $results = [];
        $destination = trim($destination, '/\\');
    if (empty($destination)) {
        $destPath = $baseDir;
    } else {
        $destPath = $baseDir . '/' . $destination;
    }
    
    if (!is_dir($destPath)) {
        mkdir($destPath, 0777, true);
    }
    
    foreach ($items as $item) {
        $base = basename($item);
        $finalDestPath = $destPath . '/' . $base;
        if (is_dir($item)) {
            $results[$item] = copyDir($item, $finalDestPath);
        } elseif (file_exists($item)) {
            $results[$item] = copy($item, $finalDestPath);
        } else {
            $results[$item] = false;
        }
    }
    return $results;
}

$baseDir = __DIR__;
$requestedPath = isset($_GET['dir']) ? $_GET['dir'] : '';

$requestedPath = str_replace('\\', '/', $requestedPath);
$requestedPath = preg_replace('#/+#', '/', $requestedPath);
$requestedPath = trim($requestedPath, '/');

if (empty($requestedPath)) {
    $currentDir = $baseDir;
} else {
    $currentDir = $baseDir . '/' . $requestedPath;
}

$realCurrentDir = realpath($currentDir);
$realBaseDir = realpath($baseDir);

if (!$realCurrentDir || strpos($realCurrentDir, $realBaseDir) !== 0) {
    $currentDir = $baseDir;
    $requestedPath = '';
}

function getRelativePath($fullPath, $basePath) {
    $fullPath = str_replace('\\', '/', $fullPath);
    $basePath = str_replace('\\', '/', $basePath);
    
    if ($fullPath === $basePath) {
        return '';
    }
    
    return substr($fullPath, strlen($basePath) + 1);
}

if (isset($_POST['extract'])) {
    if (isset($_FILES['zipFile']) && $_FILES['zipFile']['error'] == UPLOAD_ERR_OK) {
        $zipTmp = $_FILES['zipFile']['tmp_name'];
        if (extractZip($zipTmp, $currentDir)) {
            $result = '✓ Successful extraction!';
        } else {
            $result = "✗ Error extracting ZIP.";
        }
    } else {
        $result = '! Please select a ZIP file.';
    }
}

if (isset($_POST['delete']) && isset($_POST['items'])) {
    $items = $_POST['items'];
    $deleteResults = deleteItems($items);
    $success = array_filter($deleteResults);
    $fail = array_keys(array_filter($deleteResults, function($v){return !$v;}));
    $result = '✓ ' . count($success) . ' item(s) deleted.';
    if ($fail) {
        $result .= ' ✗ Error: ' . count($fail) . ' item(s) not deleted.';
    }
}

if (isset($_POST['move']) && isset($_POST['items']) && isset($_POST['destination'])) {
    $items = $_POST['items'];
    $destination = $_POST['destination'];
    $moveResults = moveItems($items, $destination);
    $success = array_filter($moveResults);
    $fail = array_keys(array_filter($moveResults, function($v){return !$v;}));
    $result = '✓ ' . count($success) . ' item(s) moved.';
    if ($fail) {
        $result .= ' ✗ Error: ' . count($fail) . ' item(s) not moved.';
    }
}

if (isset($_POST['copy']) && isset($_POST['items']) && isset($_POST['destination'])) {
    $items = $_POST['items'];
    $destination = $_POST['destination'];
    $copyResults = copyItems($items, $destination);
    $success = array_filter($copyResults);
    $fail = array_keys(array_filter($copyResults, function($v){return !$v;}));
    $result = '✓ ' . count($success) . ' item(s) copied.';
    if ($fail) {
        $result .= ' ✗ Error: ' . count($fail) . ' item(s) not copied.';
    }
}

$files = is_dir($currentDir) ? scandir($currentDir) : [];
$currentRelativePath = getRelativePath($currentDir, $baseDir);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #212529;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background: #212529;
            color: white;
            padding: 20px 25px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-content h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .header-content .subtitle {
            color: #adb5bd;
            font-size: 0.9rem;
            margin-top: 4px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.15s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .breadcrumb {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 12px 25px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .breadcrumb-path {
            color: #212529;
            font-weight: 500;
            font-family: 'Courier New', monospace;
        }

        .panel {
            border-bottom: 1px solid #dee2e6;
        }

        .panel:last-child {
            border-bottom: none;
        }

        .panel-header {
            background: #f8f9fa;
            color: #212529;
            padding: 12px 25px;
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 1px solid #dee2e6;
        }

        .panel-content {
            padding: 20px 25px;
        }

        .file-explorer {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
        }

        .file-list {
            list-style: none;
        }

        .file-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            transition: background-color 0.15s ease;
            font-size: 0.9rem;
        }

        .file-item:last-child {
            border-bottom: none;
        }

        .file-item:hover {
            background-color: #f8f9fa;
        }

        .file-item.folder {
            font-weight: 500;
        }

        .file-item.parent-folder {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .file-icon {
            margin-right: 8px;
            font-size: 1rem;
            width: 16px;
            text-align: center;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .select-all-row {
            margin-bottom: 8px;
        }

        .form-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            color: #495057;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        input[type="file"], input[type="text"] {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: border-color 0.15s ease;
        }

        input[type="file"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: #495057;
            box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background: white;
            color: #495057;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }

        .btn-primary {
            background: #212529;
            color: white;
            border-color: #212529;
        }

        .btn-primary:hover {
            background: #343a40;
            border-color: #343a40;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
            border-color: #bd2130;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .checkbox-list {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid #f8f9fa;
            font-size: 0.9rem;
        }

        .checkbox-item:last-child {
            border-bottom: none;
        }

        .checkbox-item:hover {
            background: #f8f9fa;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 8px;
        }

        .checkbox-item label {
            margin: 0;
            cursor: pointer;
            flex: 1;
        }

        .destination-input {
            flex: 1;
            min-width: 200px;
        }

        .result {
            margin: 20px 25px;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        ::-webkit-scrollbar-thumb {
            background: #ced4da;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #adb5bd;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .header, .breadcrumb, .panel-content {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .form-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
                margin-bottom: 8px;
            }
            
            .destination-input {
                min-width: auto;
            }
        }

        .icon {
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1>File Manager</h1>
                <div class="subtitle">File management and organization interface</div>
            </div>
            <a href="?logout" class="logout-btn" onclick="return confirm('Are you sure you want to log out?')">
                Logout
            </a>
        </div>

        <div class="breadcrumb">
            Location: <span class="breadcrumb-path">
                <?php echo '/' . ($currentRelativePath ?: ''); ?>
            </span>
        </div>

        <div class="panel">
            <div class="panel-header">File Explorer</div>
            <div class="panel-content">
                <div class="file-explorer">
                    <ul class="file-list">
                        <?php if ($currentRelativePath !== ''): ?>
                            <li class="file-item parent-folder" onclick="navigateToParent()">
                                <span class="file-icon">↑</span>
                                <span>.. (Parent folder)</span>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $folders = [];
                        $regularFiles = [];
                        
                        foreach ($files as $file) {
                            if ($file === '.' || $file === '..') continue;
                            
                            $filePath = $currentDir . '/' . $file;
                            if (is_dir($filePath)) {
                                $folders[] = $file;
                            } else {
                                $regularFiles[] = $file;
                            }
                        }
                        
                        foreach ($folders as $folder):
                        ?>
                            <li class="file-item folder" onclick="navigateToFolder('<?php echo htmlspecialchars($folder); ?>')">
                                <span class="file-icon">📁</span>
                                <span><?php echo htmlspecialchars($folder); ?></span>
                            </li>
                        <?php endforeach; ?>
                        
                        <?php foreach ($regularFiles as $file): ?>
                            <li class="file-item file">
                                <span class="file-icon">📄</span>
                                <span><?php echo htmlspecialchars($file); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">ZIP File Extraction</div>
            <div class="panel-content">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="current_dir" value="<?php echo htmlspecialchars($currentRelativePath); ?>">
                    <div class="form-group">
                        <label for="zipFile">ZIP File:</label>
                        <input type="file" name="zipFile" id="zipFile" accept=".zip" required>
                    </div>
                    <button type="submit" name="extract" class="btn btn-primary">
                        Extract to this folder
                    </button>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">File Management</div>
            <div class="panel-content">
                <form method="post" id="fileActionForm">
                    <input type="hidden" name="current_dir" value="<?php echo htmlspecialchars($currentRelativePath); ?>">
                    
                    <div class="form-section">
                        <div class="select-all-row">
                            <button type="button" class="btn btn-sm" onclick="toggleSelectAll()">
                                <input type="checkbox" id="selectAllMaster" style="margin-right:7px;vertical-align:middle;"> <span id="selectAllText">Tout sélectionner</span>
                            </button>
                        </div>
                        <div class="checkbox-list">
                            <?php foreach (array_merge($folders, $regularFiles) as $item): ?>
                                <?php
                                $isConfig = ($item === 'config.php');
                                ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="items[]" value="<?php echo htmlspecialchars($currentDir . '/' . $item); ?>" id="item_<?php echo htmlspecialchars($item); ?>" <?php echo $isConfig ? 'disabled' : ''; ?> >
                                    <label for="item_<?php echo htmlspecialchars($item); ?>" style="<?php echo $isConfig ? 'color:#aaa;' : ''; ?>">
                                        <?php echo is_dir($currentDir . '/' . $item) ? '📁' : '📄'; ?>
                                        <?php echo htmlspecialchars($item); ?>
                                    <?php if($isConfig) echo ' (protected)'; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-row">
                            <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete the selected items?')">
                                Delete
                            </button>
                            
                            <input type="text" name="destination" placeholder="Destination path (e.g.: folder1/subfolder)" 
                                   class="destination-input" value="/">
                            
                            <button type="submit" name="move" class="btn">
                                Move
                            </button>
                            
                            <button type="submit" name="copy" class="btn">
                                Copy
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($result)): ?>
            <div class="result <?php echo strpos($result, '✗') !== false ? 'error' : 'success'; ?>">
                <?php echo $result; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>

        function updateMasterCheckbox() {
            const checkboxes = Array.from(document.querySelectorAll('input[name="items[]"]'));
            const enabledCheckboxes = checkboxes.filter(cb => !cb.disabled);
            const checkedCount = enabledCheckboxes.filter(cb => cb.checked).length;
            const master = document.getElementById('selectAllMaster');
            const btn = document.getElementById('selectAllText');

            if (checkedCount === 0) {
                master.checked = false;
                master.indeterminate = false;
                btn.textContent = 'Select all';
            } else if (checkedCount === enabledCheckboxes.length) {
                master.checked = true;
                master.indeterminate = false;
                btn.textContent = 'Deselect all';
            } else {
                master.checked = false;
                master.indeterminate = true;
                btn.textContent = 'Deselect all';
            }
        }

        function toggleSelectAll() {
            const master = document.getElementById('selectAllMaster');
            const checkboxes = Array.from(document.querySelectorAll('input[name="items[]"]'));
            const enabledCheckboxes = checkboxes.filter(cb => !cb.disabled);
            const allChecked = enabledCheckboxes.every(cb => cb.checked);
            enabledCheckboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            updateMasterCheckbox();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="items[]"]');
            checkboxes.forEach(cb => {
                if (!cb.disabled) {
                    cb.addEventListener('change', updateMasterCheckbox);
                }
            });
            updateMasterCheckbox();
        });

        function navigateToFolder(folderName) {
            const currentPath = '<?php echo addslashes($currentRelativePath); ?>';
            let newPath;
            
            if (currentPath === '') {
                newPath = encodeURIComponent(folderName);
            } else {
                newPath = encodeURIComponent(currentPath + '/' + folderName);
            }
            
            window.location.href = '<?php echo basename($_SERVER['PHP_SELF']); ?>?dir=' + newPath;
        }

        function navigateToParent() {
            const currentPath = '<?php echo addslashes($currentRelativePath); ?>';
            const pathParts = currentPath.split('/');
            pathParts.pop();
            
            const parentPath = pathParts.join('/');
            const encodedPath = parentPath ? encodeURIComponent(parentPath) : '';
            
            window.location.href = '<?php echo basename($_SERVER['PHP_SELF']); ?>?dir=' + encodedPath;
        }

        document.getElementById('fileActionForm').addEventListener('submit', function(e) {
            if (e.submitter && e.submitter.name === 'delete') {
                const selectedItems = document.querySelectorAll('input[name="items[]"]:checked');
                if (selectedItems.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one item to delete.');
                    return false;
                }
            }
        });
    </script>
</body>
</html>