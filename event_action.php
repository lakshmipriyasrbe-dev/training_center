<?php 
require_once 'common_file.php'; 
$action = $_POST['action'] ?? $_REQUEST['action'] ?? '';

if (isset($_POST['event_name']) && isset($_POST['event_date'])) {
    if (!checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'event', empty($_POST['view_event_id']) ? PERMISSION_ADD : PERMISSION_EDIT)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $event_name = $bf->sanitize($_POST['event_name'] ?? '');
    $event_date = $bf->sanitize($_POST['event_date'] ?? '');
    $event_description = $bf->sanitize($_POST['event_description'] ?? '');
    $view_event_id = $bf->sanitize($_POST['view_event_id'] ?? '');

    $errors = [];

    if (empty($event_name)) {
        $errors['event_name'] = 'enter event name';
    }
    if (empty($event_date)) {
        $errors['event_date'] = 'enter event date';
    }

    if (empty($errors)) {
        $uploaded_files = [];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (isset($_FILES['attachments'])) {
            $files = $_FILES['attachments'];
            $file_count = count($files['name']);
            
            for ($i = 0; $i < $file_count; $i++) {
                $file_name = $files['name'][$i];
                $file_tmp = $files['tmp_name'][$i];
                $file_error = $files['error'][$i];
                
                if ($file_error === UPLOAD_ERR_NO_FILE || empty($file_name)) {
                    continue;
                }
                
                if ($file_error !== UPLOAD_ERR_OK) {
                    $errors['attachments'] = 'Error uploading file: ' . $file_name;
                    break;
                }
                
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($file_ext, $allowed_extensions)) {
                    $errors['attachments'] = 'Invalid file type: ' . $file_name . '. Allowed: ' . implode(', ', $allowed_extensions);
                    break;
                }
                
                $unique_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $file_name);
                $upload_path = 'upload/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                
                if (move_uploaded_file($file_tmp, $upload_path . $unique_name)) {
                    $uploaded_files[] = $unique_name;
                } else {
                    $errors['attachments'] = 'Failed to save uploaded file: ' . $file_name;
                    break;
                }
            }
        }

        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            exit;
        }

        $existing_attachments = $bf->sanitize($_POST['existing_attachments'] ?? '');
        $all_attachments = [];
        if (!empty($existing_attachments)) {
            $all_attachments = array_filter(array_map('trim', explode(',', $existing_attachments)));
        }
        $all_attachments = array_merge($all_attachments, $uploaded_files);

        $data = [
            'event_name' => $event_name,
            'event_date' => $event_date,
            'event_description' => $event_description,
            'images' => implode(',', $all_attachments),
            'updated_date_time' => date('Y-m-d H:i:s')
        ];

        if (empty($view_event_id)) {
            $event_num = $bf->automate_number($GLOBALS['event_table'], 'event_number', '', '');
            $data['event_number'] = $event_num;
            $data['company_id'] = $_SESSION['company_id'];
            $data['user_id'] = $_SESSION['user_id'];
            $data['role_id'] = $_SESSION['role_id'];
            $data['created_date_time'] = date('Y-m-d H:i:s');
            
            $bf->InsertSQL(
                $GLOBALS['event_table'],
                $data,
                'event_id',
                '',
                'ADD Event'
            );
            echo json_encode(['status' => 'success', 'message' => 'Event added successfully']);
        } else {
            $bf->UpdateSQL(
                $GLOBALS['event_table'],
                $data,
                'id = :id',
                [':id' => $view_event_id]
            );
            echo json_encode(['status' => 'success', 'message' => 'Event updated successfully']);
        }
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

if (isset($_REQUEST['view_event_id'])) {
    $view_event_id = $bf->sanitize($_REQUEST['view_event_id'] ?? '');
    $event_id = '';
    $event_date = date('Y-m-d');
    $event_name = '';
    $event_description = '';
    $attachments_str = '';

    if (!empty($view_event_id)) {
        $event_list = $bf->getTableRecords($GLOBALS['event_table'], 'id', $view_event_id);
        if (!empty($event_list)) {
            $event = $event_list[0];
            $event_id = $event['event_id'] ?? '';
            $event_date = $event['event_date'] ?? date('Y-m-d');
            $event_name = $event['event_name'] ?? '';
            $event_description = $event['event_description'] ?? '';
            $attachments_str = $event['images'] ?? '';
        }
    }
    ?>
    <div class="header">
        <h2><?php echo empty($view_event_id) ? 'New Event' : 'Update Event'; ?></h2>
    </div>

    <div class="module-section form-section">
        <form
            name="event_form"
            id="event_form"
            method="POST"
            enctype="multipart/form-data"
            onsubmit="event.preventDefault(); formSubmit('event_form', 'event_action.php', 'event.php', 'event');"
        >
            <input type="hidden" name="view_event_id" value="<?php echo $view_event_id; ?>">

            <div class="form-row">
                <div class="form-group col-6">
                    <label>Event Name *</label>
                    <input
                        type="text"
                        name="event_name"
                        id="event_name"
                        class="form-input"
                        placeholder="Enter Event Name"
                        value="<?php echo htmlspecialchars($event_name); ?>"
                    >
                    <span id="error-event_name" class="error-msg"></span>
                </div>

                <div class="form-group col-6">
                    <label>Event Date *</label>
                    <input
                        type="date"
                        name="event_date"
                        id="event_date"
                        class="form-input"
                        value="<?php echo htmlspecialchars($event_date); ?>"
                    >
                    <span id="error-event_date" class="error-msg"></span>
                </div>

                <div class="form-group col-12">
                    <label>Description</label>
                    <textarea
                        name="event_description"
                        id="event_description"
                        class="form-input"
                        rows="3"
                        placeholder="Enter Event Description"
                    ><?php echo htmlspecialchars($event_description); ?></textarea>
                    <span id="error-event_description" class="error-msg"></span>
                </div>

                <div class="form-group col-12">
                    <label>Images</label>
                    <style>
                        .premium-upload-zone {
                            border: 2px dashed rgba(59, 130, 246, 0.4);
                            background: rgba(59, 130, 246, 0.02);
                            border-radius: 8px;
                            padding: 1.5rem;
                            text-align: center;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            margin-bottom: 0.5rem;
                        }
                        .premium-upload-zone:hover, .premium-upload-zone.dragover {
                            border-color: #3b82f6;
                            background: rgba(59, 130, 246, 0.06);
                            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
                        }
                        .premium-upload-zone .upload-icon {
                            font-size: 2rem;
                            color: #3b82f6;
                            margin-bottom: 0.5rem;
                            transition: transform 0.3s ease;
                        }
                        .premium-upload-zone:hover .upload-icon {
                            transform: translateY(-4px);
                        }
                        .premium-upload-zone .upload-text {
                            font-size: 0.95rem;
                            font-weight: 600;
                            color: #1e293b;
                        }
                        .premium-upload-zone .upload-subtext {
                            font-size: 0.8rem;
                            color: #64748b;
                            margin-top: 0.25rem;
                        }
                        .upload-previews-container {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 1rem;
                            margin-top: 0.75rem;
                            margin-bottom: 0.75rem;
                        }
                        .preview-card {
                            position: relative;
                            width: 80px;
                            height: 80px;
                            border-radius: 8px;
                            border: 1px solid rgba(0, 0, 0, 0.08);
                            background: #fff;
                            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            overflow: visible;
                            transition: transform 0.2s ease, box-shadow 0.2s ease;
                        }
                        .preview-card:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                        }
                        .preview-card img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            border-radius: 8px;
                        }
                        .preview-card .btn-remove-preview {
                            position: absolute;
                            top: -6px;
                            right: -6px;
                            background: #ef4444;
                            color: #fff;
                            border: none;
                            width: 18px;
                            height: 18px;
                            border-radius: 50%;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 0.65rem;
                            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
                            transition: background 0.2s, transform 0.2s;
                        }
                        .preview-card .btn-remove-preview:hover {
                            background: #dc2626;
                            transform: scale(1.1);
                        }
                    </style>

                    <div class="premium-upload-zone" id="upload_zone">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <div class="upload-text">Drag & drop your event images, or <span style="color:#3b82f6;">browse</span></div>
                        <div class="upload-subtext">Supported formats: JPG, JPEG, PNG, WEBP (Max 5MB each)</div>
                        <input
                            type="file"
                            name="attachments[]"
                            id="attachments_input"
                            multiple
                            accept="image/*"
                            style="display: none;"
                        >
                    </div>

                    <input type="hidden" name="existing_attachments" id="existing_attachments" value="<?php echo $attachments_str; ?>">
                    <div class="upload-previews-container" id="previews_container"></div>
                </div>

                <div class="form-group col-12" style="display:flex; justify-content: flex-end; gap:0.75rem; margin-top:1.5rem;">
                    <button type="button" class="btn-add" style="background:#64748b;" onclick="loadPage('event.php')">Cancel</button>
                    <button type="submit" class="btn-add"><?php echo empty($view_event_id) ? 'Create Event' : 'Save Changes'; ?></button>
                </div>
            </div>
        </form>
    </div>

    <script>
        (function() {
            const uploadZone = document.getElementById('upload_zone');
            const fileInput = document.getElementById('attachments_input');
            const existingInput = document.getElementById('existing_attachments');
            
            let selectedFiles = [];
            let existingFiles = existingInput.value ? existingInput.value.split(',').map(s => s.trim()).filter(Boolean) : [];

            // Event Listeners
            uploadZone.addEventListener('click', () => fileInput.click());
            
            fileInput.addEventListener('change', function() {
                for (let file of this.files) {
                    selectedFiles.push(file);
                }
                updateFileInputAndPreviews();
            });

            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.classList.add('dragover');
            });

            uploadZone.addEventListener('dragleave', () => {
                uploadZone.classList.remove('dragover');
            });

            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.classList.remove('dragover');
                if (e.dataTransfer.files.length) {
                    for (let file of e.dataTransfer.files) {
                        if (file.type.startsWith('image/')) {
                            selectedFiles.push(file);
                        }
                    }
                    updateFileInputAndPreviews();
                }
            });

            window.removeExistingFile = function(fileName) {
                existingFiles = existingFiles.filter(f => f !== fileName);
                existingInput.value = existingFiles.join(',');
                renderAllPreviews();
            };

            window.removeSelectedFile = function(index) {
                selectedFiles.splice(index, 1);
                updateFileInputAndPreviews();
            };

            function updateFileInputAndPreviews() {
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
                renderAllPreviews();
            }

            function renderAllPreviews() {
                const container = document.getElementById('previews_container');
                if (!container) return;
                container.innerHTML = '';

                // Existing
                existingFiles.forEach(function(file) {
                    const card = document.createElement('div');
                    card.className = 'preview-card';
                    card.innerHTML = `
                        <a href="upload/${file}" target="_blank"><img src="upload/${file}" alt="${file}"></a>
                        <button type="button" class="btn-remove-preview" onclick="removeExistingFile('${file}')" title="Remove"><i class="fas fa-times"></i></button>
                    `;
                    container.appendChild(card);
                });

                // New
                selectedFiles.forEach(function(file, index) {
                    const card = document.createElement('div');
                    card.className = 'preview-card';
                    const objectUrl = URL.createObjectURL(file);
                    card.innerHTML = `
                        <a href="${objectUrl}" target="_blank"><img src="${objectUrl}" alt="${file.name}"></a>
                        <button type="button" class="btn-remove-preview" onclick="removeSelectedFile(${index})" title="Remove"><i class="fas fa-times"></i></button>
                    `;
                    container.appendChild(card);
                });
            }

            renderAllPreviews();
        })();
    </script>
    <?php
    exit;
}

if (isset($action) && $action === 'delete') {
    if (!checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'event', PERMISSION_DELETE)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    $id = $bf->sanitize($_REQUEST['id'] ?? '');
    if (!empty($id)) {
        $bf->UpdateSQL($GLOBALS['event_table'], ['deleted' => 1, 'updated_date_time' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully']);
    }
    exit;
}

// Default AJAX Listing Table
if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'event', PERMISSION_VIEW)) {
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
    $search = isset($_POST['search']) ? $bf->sanitize($_POST['search']) : '';
    $start = ($page - 1) * $limit;

    $result = $bf->getTableList($GLOBALS['event_table'], ['event_name', 'event_description'], $start, $limit, $search);
    $events = $result['data'];
    $total_records = $result['total_records'];
    $total_pages = ceil($total_records / $limit);

    if (empty($events)) { ?>
        <div class="table-responsive">
            <table><tr><td style="text-align:center">No events found.</td></tr></table>
        </div>
    <?php } else {
        ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Event No</th>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Images</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($events as $e) {
                    $images_str = $e['images'] ?? '';
                    $images = array_filter(array_map('trim', explode(',', $images_str)));
                ?>
                    <tr>
                        <td><span style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($e['event_number'] ?: $e['event_id']); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($e['event_name']); ?></strong></td>
                        <td><?php echo date('d-m-Y', strtotime($e['event_date'])); ?></td>
                        <td>
                            <div style="display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; gap: 5px !important; align-items: center !important; justify-content: flex-start !important;">
                                <?php if (!empty($images)): ?>
                                    <?php foreach ($images as $img): ?>
                                        <a href="upload/<?php echo htmlspecialchars($img); ?>" target="_blank" style="display: inline-block !important; width: 32px !important; height: 32px !important; border-radius: 4px !important; overflow: hidden !important; border: 1px solid rgba(0,0,0,0.1) !important; flex-shrink: 0 !important; margin: 0 !important; padding: 0 !important;">
                                            <img src="upload/<?php echo htmlspecialchars($img); ?>" alt="Event" style="width: 100% !important; height: 100% !important; object-fit: cover !important; display: block !important; margin: 0 !important; padding: 0 !important;">
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="font-size:0.75rem; color:#94a3b8;">No Images</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; gap:0.5rem;">
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'event', PERMISSION_EDIT)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;" onclick="ShowPage('event', '<?php echo $e['id']; ?>')">Edit</button>
                                <?php endif; ?>
                                <?php if (checkPermission($_SESSION['company_id'], $_SESSION['role_id'], 'event', PERMISSION_DELETE)): ?>
                                    <button class="btn-add" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; background: #ef4444;" onclick="deleteRecord('event', '<?php echo $e['id']; ?>')">Delete</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <div class="pagination-info">
                Showing <?php echo ($total_records > 0) ? $start + 1 : 0; ?> to <?php echo min($start + $limit, $total_records); ?> of <?php echo $total_records; ?> entries
            </div>
            <div class="pagination-buttons">
                <button class="page-btn" <?php echo ($page <= 1) ? 'disabled' : ''; ?> onclick="loadData('event', <?php echo $page - 1; ?>, $('#event_limit').val(), $('#event_search').val())">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                if ($end_page - $start_page < 4) $start_page = max(1, $end_page - 4);
                for ($i = $start_page; $i <= $end_page; $i++) { ?>
                    <button class="page-btn <?php echo ($i == $page) ? 'active' : ''; ?>" onclick="loadData('event', <?php echo $i; ?>, $('#event_limit').val(), $('#event_search').val())">
                        <?php echo $i; ?>
                    </button>
                <?php } ?>
                <button class="page-btn" <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?> onclick="loadData('event', <?php echo $page + 1; ?>, $('#event_limit').val(), $('#event_search').val())">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php
    }
    exit;
}
?>
